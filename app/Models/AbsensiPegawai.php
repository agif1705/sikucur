<?php

namespace App\Models;

use Carbon\Carbon;
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
}
