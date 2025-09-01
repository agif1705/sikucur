<?php

namespace App\Services;

use App\Models\Nagari;
use App\Models\WdmsModel;
use Illuminate\Support\Carbon;
use App\Models\RekapAbsensiPegawai;

class SinkronFingerprintService
{
    public static function sinkronFingerPrint(Nagari $nagari): void
    {
        // dd($user);
        $month = Carbon::now()->month;
        $today = Carbon::today()->format('Y-m-d');

        // Ambil SN fingerprint dari user
        $sn_fingerprint = $nagari->sn_fingerprint ?? null;
        // ================== ABSENSI MASUK ==================
        $absensi_masuk = WdmsModel::with('user')
            ->where('terminal_sn', $sn_fingerprint)
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
                    'date'           => $value->date_in,
                ]);
            }
        }

        // ================== ABSENSI PULANG ==================
        $absensi_pulang = WdmsModel::with('user')
            ->where('terminal_sn', $sn_fingerprint)
            ->whereMonth('punch_time', $month)
            ->whereTime('punch_time', '>=', '12:00')
            ->select('emp_id', 'punch_time', 'emp_code')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'user_id'   => $item->user->id,
                    'nagari_id' => $item->user->nagari->id,
                    'sn_mesin'  => $item->user->nagari->sn_fingerprint,
                    'date_out'  => Carbon::parse($item->punch_time)->format('Y-m-d'),
                    'time_out'  => Carbon::parse($item->punch_time)->format('H:i'),
                    'pulang'    => Carbon::parse($item->punch_time)->format('H:i') > '16:00',
                ];
            });

        foreach ($absensi_pulang as $value) {
            $check = RekapAbsensiPegawai::where('user_id', $value->user_id)
                ->whereDate('date', $today)
                ->whereNotNull('time_out')
                ->exists();

            if (!$check) {
                RekapAbsensiPegawai::where('user_id', $value->user_id)
                    ->whereDate('date', $today)
                    ->update([
                        'time_out' => $value->time_out,
                    ]);
            }
        }

    }
}
