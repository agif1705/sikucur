<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiPegawai extends Model
{
    protected $fillable = [
        'absensi_by',
        'absensi',
        'emp_id',
        'status_absensi',
        'sn_mesin',
        'accept',
        'accept_by',
        'user_id',
        'nagari_id',
        'time_in',
        'time_out',
        'date_in',
        'date_out'
    ];


    public function nagari(): BelongsTo
    {
        return $this->belongsTo(Nagari::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public static function getAbsensiMasuk($sn_fp, $tanggal)
    {
        return self::with('user')
            ->where('sn_mesin', $sn_fp)
            ->whereDate('date_in', $tanggal)
            ->select('emp_id', 'punch_time', 'emp_code')
            ->get()
            ->sortBy('date_in')
            ->groupBy(function ($item) {
                return $item->emp_id . '-' . Carbon::parse($item->punch_time)->format('Y-m-d');
            })
            ->map(function ($grouped) {
                $item = $grouped->first();
                $item->time_only = Carbon::parse($item->punch_time)->format('H:i');
                $item->date_in = Carbon::parse($item->punch_time)->format('Y-m-d');
                $item->user_id = $item->user->id;
                $item->nagari_id = $item->user->nagari->id;
                $item->sn_mesin = $item->user->nagari->sn_fingerprint;
                $item->is_late = $item->time_only > '08:00';
                return $item;
            })
            ->values()
            ->unique('emp_id');
    }
    // public static function getGabungan($bulan = null, $tahun = null): Collection
    // {
    //     $bulan = $bulan ?? Carbon::now()->month;
    //     $tahun = $tahun ?? Carbon::now()->year;
    //     $izinCutiSakit = \App\Models\AbsensiPegawai::with('user')
    //         ->whereMonth('date_in', $bulan)
    //         ->whereYear('date_in', $tahun)
    //         ->get()
    //         ->map(function ($item) {
    //             return [
    //                 'date_in'   => $item->date_in ?? $item->created_at->toDateString(),
    //                 'time_in'   => $item->time_in ?? $item->created_at->toDateString(),
    //                 'nama'      => $item->user?->name ?? '-',
    //                 'status_hadir'    => $item->absensi ?? 'izin',
    //                 'is_late'    => Carbon::parse($item->time_in)->format('H:i') > '08:00',
    //                 'jam_masuk' => $item->time_in ?? $item->created_at->toDateString(),
    //             ];
    //         });

    //     $fingerprints = WdmsModel::with('user')
    //         ->whereMonth('punch_time', $bulan)
    //         ->whereYear('punch_time', $tahun)
    //         ->get()
    //         ->map(function ($fp) {
    //             return [
    //                 'date_in'   => $fp->punch_time ? Carbon::parse($fp->punch_time)->toDateString()
    //                     : $fp->created_at->toDateString(),
    //                 'time_in'   => $fp->punch_time ? Carbon::parse($fp->punch_time)->format('H:i:s')
    //                     : $fp->created_at->toDateString(),
    //                 'nama'      => $fp->user?->name ?? '-',
    //                 'status_hadir'    => 'hadir',
    //                 'is_late'    => Carbon::parse($fp->punch_time)->format('H:i') > '08:00',
    //                 'jam_masuk' => $fp->punch_time ? Carbon::parse($fp->punch_time)->format('H:i:s')
    //                     : $fp->created_at->toDateString(),
    //             ];
    //         });

    //     $data = collect($izinCutiSakit)
    //         ->merge($fingerprints)
    //         ->sortBy('date_in')
    //         ->values();

    //     dd($data->toArray());
    // }

}
