<?php

namespace App\Http\Controllers\Api;

use PDF;
use App\Models\User;
use App\Models\Nagari;
use Illuminate\Http\Request;
use App\Services\GowaService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\RekapAbsensiPegawai;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Pdf\AbsensiReportBulananService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\RequestException;

class RekapPegawaiController extends Controller
{
    public function webhook(Request $request)
    {
        // ini ada perubahan di iclock_transaction dan mengirimkan wa ke User
        $data = $request->validate([
            'id'  => 'required|integer',
            'sn_mesin'  => 'required|string|max:255',
            'emp_id'    => 'required',
            'emp_code'  => 'required',
            'punch_time' => 'required', // format string
        ]);

        $date = Carbon::parse($data['punch_time'])->format('Y-m-d');
        $time_in = Carbon::parse($data['punch_time'])->format('H:i:s');
        $is_late = $time_in > '08:00:00';

        // Tentukan emp_id yang dipakai
        $emp_id = $data['emp_id'] ?? intval($data['emp_code']);

        // Cari nagari dan user
        $nagari_id = Nagari::where('sn_fingerprint', $data['sn_mesin'])->first()?->id;
        $user = User::whereEmpId($emp_id)->first();

        if (!$user || !$nagari_id) {
            return response()->json(['message' => 'User atau mesin tidak ditemukan'], 404);
        }
        // Cek absensi hari ini
        $absensi = RekapAbsensiPegawai::where('sn_mesin', $data['sn_mesin'])
            ->whereUserId($user->id)
            ->whereDate('date', $date)
            ->first();
        $pesan_masuk = "Hai *" . $user->name . "* , Anda *" . $is_late . '* anda telah hadir pada jam *' . carbon::parse($data['punch_time'])->format('H:i') . '* menggunakan fingerprint di *Nagari ' . $user->nagari->name .
            '* ,ini akan masuk ke WhatsApp Wali Nagari ' . $user->nagari->name . " *Sebelum Jam: 10:05 Siang* terima kasih \n   ketik : *info* -> untuk melihat informasi perintah dan bantuan lebih lanjut. \n \n \n \n _Sent || via *Cv.Baduo Mitra Solustion*_";

        if (!$absensi) {
            // Absensi pertama (masuk)
            $absensi = RekapAbsensiPegawai::create([
                'user_id'        => $user->id,
                'nagari_id'      => $nagari_id,
                'is_late'        => Carbon::parse($data['punch_time'])->format('H:i') > '08:00',
                'status_absensi' => 'Hadir',
                'sn_mesin'       => $data['sn_mesin'],
                'resource'       => 'Fingerprint',
                'id_resource'    => 'fp-' . $data['id'],
                'time_in'        => Carbon::parse($data['punch_time'])->format('H:i'),
                'date'           => Carbon::parse($data['punch_time'])->format('Y-m-d'),
            ]);
            if ($user->aktif == true) {
                $wa = new GowaService();
                $result = $wa->sendText($user->no_hp, $pesan_masuk);
            }

            return response()->json([
                'message'      => 'Absensi masuk tercatat',
                'user_id'      => $user->emp_code,
                'time_in'      => $time_in,
                'date'         => $date,
                'absensi_type' => 'IN',
                'dataSender'   => 'Fingerprint',
                'wa_response'  => $result ?? null
            ], 200);
        } else {
            // Absensi kedua (pulang)
            $absensi->update([
                'time_out' => Carbon::parse($data['punch_time'])->format('H:i'),
            ]);
            $pesan_pulang = "Hai *" . $user->name . "* , Anda telah melakukan absensi pulang pada jam *" .
                Carbon::parse($data['punch_time'])->format('H:i') . "* menggunakan fingerprint di *Nagari " . $user->nagari->name .
                "* ,terima kasih \n   ketik : *info* -> untuk melihat informasi perintah dan bantuan lebih lanjut.\n \n _Sent || via *Cv.Baduo Mitra Solustion*_";
            if ($user->aktif == true) {
                $wa = new GowaService();
                $result = $wa->sendText($user->no_hp, $pesan_pulang);
            }
            return response()->json([
                'message'      => 'Absensi pulang tercatat',
                'user_id'      => $user->emp_code,
                'time_out'     => $time_in,
                'date'         => $date,
                'wa_response'  => $result ?? null,
                'absensi_type' => 'OUT',
            ], 200);
        }
    }
    public function absensiBulanan(Request $request,  AbsensiReportBulananService $service)
    {
        $data = $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required',
        ]);

        $tahun = $data['tahun'];
        $bulan = Carbon::parse($data['bulan'])->month - 1; // jangan dikurangi 1
        $filename = "absensi-pegawai-{$bulan}-{$tahun}.pdf";
        $path = storage_path("app/private/public/absensi/{$filename}");

        // ambil semua nagari beserta user + jabatan
        $nagaris = Nagari::with('users.jabatan')->get();

        // jika file belum ada → generate sekali untuk tiap nagari
        if (!file_exists($path)) {
            foreach ($nagaris as $nagari) {
                $report = $service->generate($tahun, $bulan, $nagari->id);
            }
            // ambil hasil terakhir
            $path = storage_path($report['path']);
            $filename = $report['filename'];
        }

        // kirim WA ke semua user
        $response = [];
        foreach ($nagaris as $nagari) {
            foreach ($nagari->users as $user) {
                $pesan = "Hai *" . $user->name . "(Jabatan : " . $user->jabatan->name . ")" . "* Pegawai Nagari " . $nagari->name . " , ini adalah laporan absensi bulan *" . $bulan . "* pada tahun *" . $tahun . "* , Laporan Ini Akan kami kirim setiap awal bulan untuk di review/ditinjau kembali, Laporan pdf ini bisa di ambil dan di simpan di database \n *Note: _dikirim seluruh pegawai dan pimpinan_ \n ketik : *info* -> untuk melihat informasi perintah dan bantuan lebih lanjut. \n \n _Sent || via *Cv.Baduo Mitra Solustion*_";

                $gowa = new GowaService();
                $response[] = $gowa->sendFile(
                    phone: $user->no_hp,
                    path: $path,
                    caption: $pesan
                );
            }
        }

        // response API
        return response()->json([
            'file' => $filename,
            'path' => $path,
            'url'  => Storage::url("public/absensi/{$filename}"),
            'gowa_response' => $response
        ]);
    }
}
