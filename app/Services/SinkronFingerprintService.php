<?php

namespace App\Services;

use App\Models\AbsensiWebPegawai;
use App\Models\Nagari;
use App\Models\WdmsModel;
use Illuminate\Support\Carbon;
use App\Models\RekapAbsensiPegawai;

class SinkronFingerprintService
{
    public static function sinkronFingerPrint(Nagari $nagari): void
    {
        $month = Carbon::now()->month;
        $today = Carbon::today()->format('Y-m-d');
        // ambil data dari absensi web pegawai
        $absensi_web = AbsensiWebPegawai::where('nagari_id', $nagari->id)->whereDate('date', $today)->get();
        foreach ($absensi_web as $absensi) {
            $check = RekapAbsensiPegawai::where('user_id', $absensi->user_id)
                ->whereDate('date', $absensi->date_in)
                ->first();
            if (!$check) {
                RekapAbsensiPegawai::create([
                    'user_id' => $absensi->user_id,
                    'nagari_id' => $absensi->nagari_id,
                    'is_late' => false,
                    'sn_mesin' => $absensi->link,
                    'status_absensi' => $absensi->absensi,
                    'resource' => 'web',
                    'id_resource' => 'web-' . $absensi->id,
                    'time_in' => '07:59',
                    'time_out' =>  '16:01',
                    'date' => $absensi->date,
                ]);
            }
        }

        // Ambil SN fingerprint dari user
        $sn_fingerprint = $nagari->sn_fingerprint ?? null;
        // ================== ABSENSI MASUK ==================
        $absensi_masuk = WdmsModel::with('user')
            ->whereTime('punch_time', '<=', '12:00')
            ->select('emp_id', 'punch_time', 'emp_code', 'id')
            ->get()
            ->sortBy('punch_time')
            ->groupBy(fn($item) => $item->emp_id . '-' . Carbon::parse($item->punch_time)->format('Y-m-d'))
            ->map(function ($grouped) {
                $item = $grouped->first();

                return (object) [
                    'id'        => $item->id,
                    'user_id'   => $item->user->id,
                    'nagari_id' => $item->user->nagari->id,
                    'sn_mesin'  => $item->user->nagari->sn_fingerprint,
                    'date_in'   => Carbon::parse($item->punch_time)->format('Y-m-d'),
                    'time_in'   => Carbon::parse($item->punch_time)->format('H:i'),
                    'is_late'   => Carbon::parse($item->punch_time)->format('H:i') > '08:00',
                ];
            })
            ->values();
        foreach ($absensi_masuk as $value) {
            $check = RekapAbsensiPegawai::where('user_id', $value->user_id)
                ->whereDate('date', $value->date_in)
                ->first();

            if (!$check) {
                RekapAbsensiPegawai::create([
                    'user_id'        => $value->user_id,
                    'nagari_id'      => $value->nagari_id,
                    'is_late'        => $value->is_late,
                    'sn_mesin'       => $value->sn_mesin,
                    'status_absensi' => 'Hadir',
                    'resource'       => 'Fingerprint',
                    'id_resource'    => 'fp-' . $value->id,
                    'time_in'        => $value->time_in,
                    'time_out'       => $value->time_out ?? null,
                    'date'           => $value->date_in,
                ]);
            }
        }

        // ================== ABSENSI PULANG ==================
        $absensi_pulang = WdmsModel::with('user')
            ->whereTime('punch_time', '>=', '12:00')
            ->select('emp_id', 'punch_time', 'emp_code')
            ->get()
            ->map(function ($item) {
                return (object) [
                'user'     => $item->user,
                'date'     => \Carbon\Carbon::parse($item->punch_time)->toDateString(),
                'pulang' => \Carbon\Carbon::parse($item->punch_time)->format('H:i'),
                ];
            });
        foreach ($absensi_pulang as $absensiPulang) {

            $check = RekapAbsensiPegawai::where('user_id', $absensiPulang->user->id)
                ->whereDate('date', $absensiPulang->date)
                ->first();

            if ($check) {
                $check->update([
                    'time_out' => $absensiPulang->pulang,
                ]);
            }
        }
    }
}
