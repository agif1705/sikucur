<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Nagari;
use Illuminate\Http\Request;
use App\Services\WahaService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\RekapAbsensiPegawai;
use App\Http\Controllers\Controller;

class RekapPegawaiController extends Controller
{
    public function webhook(Request $request)
    {
        // ini ada perubahan di iclock_transaction dan mengirimkan wa ke User
        $data = $request->validate([
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
        $pesan = "Hai *" . $user->name . "* , Anda *" . $is_late . '* anda telah hadir pada jam *' . carbon::parse($data['punch_time'])->format('H:i') . '* menggunakan fingerprint di *Nagari ' . $user->nagari->name .
            '* ,ini akan masuk ke WhatsApp Wali Nagari ' . $user->nagari->name . " *Sebelum Jam: 10:05 Siang* terima kasih \n   ketik : *info* -> untuk melihat informasi perintah dan bantuan lebih lanjut. \n \n \n \n _Sent || via *Cv.Baduo Mitra Solustion*_";

        if (!$absensi) {
            // Absensi pertama (masuk)
            $absensi = RekapAbsensiPegawai::create([
                'user_id'        => $user->id,
                'nagari_id'      => $nagari_id,
                'is_late'        => $is_late,
                'sn_mesin'       => $data['sn_mesin'],
                'status_absensi' => 'Hadir',
                'resource'       => 'Fingerprint',
                'time_in'        => $time_in,
                'date'           => $date,
            ]);
            if ($user->aktif == true) {
                $wa = new WahaService();
                $result = $wa->sendText($user->no_hp, $pesan);
            }

            return response()->json([
                'message'      => 'Absensi masuk tercatat',
                'user_id'      => $user->emp_code,
                'time_in'      => $time_in,
                'date'         => $date,
                'absensi_type' => 'IN',
                'dataSender'   => 'Fingerprint'
            ], 200);
        } else {
            // Absensi kedua (pulang)
            $absensi->update([
                'time_out' => $time_in,
            ]);

            return response()->json([
                'message'      => 'Absensi pulang tercatat',
                'user_id'      => $user->emp_code,
                'time_out'     => $time_in,
                'date'         => $date,
                'absensi_type' => 'OUT',
            ], 200);
        }
    }
}
