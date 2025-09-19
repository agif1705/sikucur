<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Nagari;
use App\Models\Jabatan;
use App\Models\WdmsModel;
use App\Models\IzinPegawai;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AbsensiPegawai;
use App\Helpers\WhatsAppHelper;
use App\Models\WhatsAppCommand;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Handlers\SendingWhatsappHandlers;
use App\Services\GowaService;
use App\Services\SinkronFingerprintService;

class WhatsAppController extends Controller
{

    public function handleCommand(Request $request)
    {
        $data = $request->validate([
            'sender'       => 'required|string|max:255',
            'chat'         => 'required|string|max:255',
            'msgId'        => 'required|string|max:255',

        ]);
        $chat2 = Str::lower($data['chat']);
        $chat = Str::slug($chat2, '-');
        // dd($chat);
        $waId = Str::before($data['sender'], '@');
        // Cari user + command
        $user = User::with([
            'nagari:id,name',
            'nagari.whatsAppCommand' => fn($q) => $q->where('command', $chat)
        ])
            ->where('no_hp', $waId)
            ->select('id', 'name', 'no_hp', 'nagari_id')
            ->first();

        if (!$user) {
            return $this->apiResponse(false, "Nomor ini tidak terdaftar sebagai pegawai.", [
                'pegawai' => false,
                'data' => $data,
            ]);
        }

        $command = $user->nagari->whatsAppCommand->first();
        if (!$command || !class_exists($command->handler_class)) {
            return $this->apiResponse(true, "Perintah *{$chat}* belum ada mungkin akan kita buat. terimakasih.", [
                'pegawai' => false,
                'data' => $data,
                'bot' => true
            ]);
        }

        // Jalankan handler dari DB
        $handler = app($command->handler_class);
        $result = $handler->handle($user, $chat, $data);

        return $this->apiResponse($result['success'], $result['message'], $result['data']);
    }

    private function apiResponse($success, $message, $data)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data'    => $data
        ], $success ? 200 : 400);
    }


    function generateUniqueLink(int $length = 30): string
    {
        do {
            // Generate random string, misal pake Str::random
            $code = Str::random($length);
            // cek apakah sudah ada di DB
            $exists = IzinPegawai::where('link', $code)->exists();
        } while ($exists);

        return $code;
    }
    function scheduleHarian(Request $request)
    {
        $tanggalHariIni = Carbon::today();
        $data = $request->validate([
            'timestamp'       => 'required',
            'token'        => 'required',
        ]);

        if ($request->input('token') == 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyAgCiAgICAicm9sZSI6ICJzZXJ2aWNlX3JvbGUiLAogICAgImlzcyI6ICJzdXBhYmFzZS1kZW1vIiwKICAgICJpYXQiOiAxNjQxNzY5MjAwLAogICAgImV4cCI6IDE3OTk1MzU2MDAKfQ.DaYlNEoUrrEn2Ig7tqibS-PHK5vgusbcbo7X36XVt4Q') {
            $nagari = Nagari::with('users')->where('slug', 'sikucur')->first();
            $absensi = WdmsModel::getAbsensiMasuk($nagari->sn_fingerprint);
            $tanggal = now()->toDateString();
            $baduo = " \n \n \n \n _Sent || via *Cv.Baduo Mitra Solustion*_";
            $pesan = "📊 Laporan Absensi Hari Ini mak wali Asrul & Seketaris Fadil (Semangat Hari Ini: {$tanggal})\n\n";
            foreach ($absensi as $i => $item) {
                $statusIcon = $item['status']
                    ? ($item['is_late'] ? "⚠️ Terlambat" : "✅ HADIR")
                    : "❌ TIDAK-HADIR";

                $jam = $item['time_only'] ?? '-';

                $pesan .= ($i + 1) . ". {$item['slug']} ({$item['jabatan']}) - {$jam} {$statusIcon}\n";
            }
            $state = self::getTerminalState();
            $singkron = SinkronFingerprintService::sinkronFingerPrint($nagari);
            $wa = new GowaService();
            if (!$state->original['state'] == null) {
                $wali = $wa->sendText($nagari->wali->no_hp, $pesan . ' ' . $baduo);
                $seketaris = $wa->sendText($nagari->seketaris->no_hp, $pesan . ' ' . $baduo);
                $result = $wa->sendText('6281282779593', $pesan . ' ' . $baduo);
                return $this->apiResponse(true, 'Berhasil', ['state' => [
                    $wali,
                    $seketaris
                ]]);
            } else {
                $wali = $wa->sendText($nagari->wali->no_hp, "📊 Laporan Absensi Hari Ini Fingerprint Tidak Online / Mati\n\n" . $baduo);
                $seketaris = $wa->sendText($nagari->seketaris->no_hp, "📊 Laporan Absensi Hari Ini Fingerprint Tidak Online / Mati\n\n" . $baduo);
                return $this->apiResponse(false, 'Terminal Fingerprint tidak terhubung', ['state' => [
                    $wali,
                    $seketaris
                ]]);
            }
        }
    }
    public function getTerminalState()
    {
        // ambil token dulu
        $authResponse = Http::timeout(10)->post('https://fingerprint.baduo.cloud/jwt-api-token-auth/', [
            'username' => 'agif',
            'password' => '@Lvaro02',
        ]);
        $token = $authResponse->json('token');
        // ambil data terminal
        $terminalResponse = Http::withHeaders([
            'Authorization' => 'JWT ' . $token,
        ])->timeout(10)
            ->get('https://fingerprint.baduo.cloud/iclock/api/terminals/2/');

        $data = $terminalResponse->json();
        // ambil value "state"
        $state = $data['state'] ?? null;
        return response()->json([
            'state' => $state,
        ]);
    }
    public function kehadiran(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');
        // $now = Carbon::create(2025, 6, 13)->format('Y-m-d');
        $tvNow = Carbon::now()->format('d M Y');
        $noWa = preg_replace('/^(\+62|62|0)/', '', $request->no_wa);

        if (User::where('no_hp', $noWa)->exists()) {
            $user = User::with('nagari')->where('no_hp', $noWa)->select('id', 'name', 'emp_id', 'nagari_id', 'no_hp')->first();
            $user_role = $user->roles->pluck('name')->first();
            $users = WdmsModel::with(['user' => function ($query) {
                $query->select('name', 'emp_id', 'no_hp');
            }])->where('terminal_sn', $user->nagari->sn_fingerprint)
                ->whereDate('punch_time', $now)
                ->select('emp_id', 'punch_time', 'emp_code')
                ->whereTime('punch_time', '<=', '12:00')
                ->get()
                ->unique('emp_code')
                ->map(function ($item) {
                    $item->time_only = \Carbon\Carbon::parse($item->punch_time)->format('H:i');
                    $item->date_only = \Carbon\Carbon::parse($item->punch_time)->format('Y-m-d');
                    $item->user_name = $item->user->name;
                    if ($item->time_only > '08:00') {
                        $item->is_late = true;
                    } else {
                        $item->is_late = false;
                    }
                    return $item;
                })->toJson();
            return $users;
        }
    }
}