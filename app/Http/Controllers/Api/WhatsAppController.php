<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Nagari;
use App\Models\WdmsModel;
use App\Models\IzinPegawai;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Models\WhatsAppCommand;

class WhatsAppController extends Controller
{

    public function handleCommand(Request $request)
    {
        $data = $request->validate([
            'sender'       => 'required|string|max:255',
            'senderNumber' => 'required|string|max:255',
            'chat'         => 'required|string|max:255',
            'msgId'        => 'required|string|max:255',
            'ChatId'        => 'required|string|max:255',

        ]);
        $chat = Str::lower($data['chat']);

        // Cari user + command
        $user = User::with([
            'nagari:id,name',
            'nagari.whatsAppCommand' => fn($q) => $q->where('command', $chat)
        ])
            ->where('no_hp', $data['senderNumber'])
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
                'pegawai' => true,
                'data' => $data,

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
    // public function izinPegawai(Request $request)
    // {
    //     // Validasi input
    //     $data = $request->validate([
    //         'sender'       => 'required|string|max:255',
    //         'senderNumber' => 'required|string|max:255',
    //         'chat'         => 'required|string|max:255',
    //         'admin'        => 'required|string|max:255',
    //     ]);

    //     $chat = Str::lower($data['chat']);

    //     // Cari user
    //     $user = User::with([
    //         'nagari:id,name',
    //         'nagari.whatsAppCommand' => fn($q) => $q->where('command', $chat)->first()
    //     ])
    //         ->select('id', 'name', 'username', 'no_hp', 'nagari_id')
    //         ->where('no_hp', $data['senderNumber'])
    //         ->firstOrFail();
    //     dd($user);
    //     if (!$user) {
    //         return response()->json([
    //             'pegawai'    => true,
    //             'no_hp'      => $data['senderNumber'],
    //             'sender'     => $data['sender'],
    //             'name'       => null,
    //             'nagari'     => null,
    //             'link'       => "Anda Bukan pegawai yang terdaftar",
    //             'used'       => false,
    //             'url'        => "tidak ditemukan",
    //             'expires_at' => "tidak ditemukan",
    //             'replay'     => "Maaf, tidak ditemukan permintaan izin pegawai untuk nomor ini atau perintah anda salah. Silakan coba lagi atau hubungi admin.",
    //             'status'     => 'success',
    //             'info'       => $request->data
    //         ], 200);
    //     }

    //     // Cek izin aktif
    //     $checkizin = IzinPegawai::where('user_id', $user->id)
    //         ->where('nagari', $user->nagari->name)
    //         ->where('expired_at', '>', now())
    //         ->where('used', false)
    //         ->first();

    //     if ($checkizin) {
    //         $url = $this->generateIzinUrl($checkizin->link, $user->nagari->name, $checkizin->expired_at);
    //         return response()->json($this->buildResponse(
    //             $user,
    //             $checkizin->link,
    //             $url,
    //             $checkizin->expired_at->timestamp,
    //             "Link  Sudah ada. Izin Pegawai {$user->name} Nagari *{$user->nagari->name}* telah dibuat. Silakan klik link berikut untuk mengisi izin: {$url}",
    //             $request->data
    //         ), 200);
    //     }

    //     // Buat izin baru
    //     $link       = $this->generateUniqueLink();
    //     $nagari     = $user->nagari->name;
    //     $expired_at = now()->addMinutes(30);

    //     IzinPegawai::create([
    //         'user_id'    => $user->id,
    //         'used'       => false,
    //         'link'       => $link,
    //         'nagari'     => $nagari,
    //         'expired_at' => $expired_at,
    //     ]);

    //     $url = $this->generateIzinUrl($link, $nagari, $expired_at);
    //     $replay = "Link Izin Pegawai {$user->name} Nagari *{$nagari}* telah dibuat. Silakan klik link berikut untuk mengisi izin:\n{$url}\nLink ini hanya berlaku selama 30 menit. Mohon segera mengisi absensi ini untuk keperluan:\n  {$chat}\nJika ada pertanyaan, silakan hubungi admin: {$data['admin']}\nTerima kasih!";

    //     return response()->json($this->buildResponse($user, $link, $url, $expired_at->timestamp, $replay, $request->data), 200);
    // }

    // // Helper untuk buat URL izin
    // private function generateIzinUrl($link, $nagari, $expired_at)
    // {
    //     return URL::temporarySignedRoute('izin-pegawai.form', $expired_at, [
    //         'link'       => $link,
    //         'nagari'     => $nagari,
    //         'expired_at' => $expired_at->timestamp,
    //     ]);
    // }

    // // Helper untuk response JSON
    // private function buildResponse($user, $link, $url, $expires_at, $replay, $info)
    // {
    //     return [
    //         'pegawai'    => true,
    //         'no_hp'      => $user->no_hp,
    //         'sender'     => request('sender'),
    //         'name'       => $user->name,
    //         'nagari'     => $user->nagari->name ?? 'Tidak ada nagari',
    //         'link'       => $link,
    //         'used'       => false,
    //         'url'        => $url,
    //         'expires_at' => $expires_at,
    //         'replay'     => $replay,
    //         'status'     => 'success',
    //         'info'       => $info
    //     ];
    // }

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
