<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class iclock_transaction extends Model
{
    protected $connection = 'pgsql'; // jika pakai connection berbeda
    protected $table = 'iclock_transaction';
    protected $fillable = [
        'punch_time',
        'terminal_sn',
        'terminal_alias',
        'area_alias',
        'emp_id',
        'terminal_id',
    ];
    public function user()
    {
        return $this->belongsTo(
            \App\Models\User::class,
            'emp_code',
            'emp_id'
        );
    }
    public function isLate()
    {
        $jadwal_start_time = Carbon::now()->setTime(8, 0, 0);
        $start_time = Carbon::parse($this->punch_time);
        return $start_time->greaterThan($jadwal_start_time);
    }
    public function checkIn($emp_id)
    {
        $wdms = $this->where('emp_id', $emp_id)->whereDate('punch_time', now()->format('Y-m-d'))->select('punch_time')->first();
        if ($wdms->count() == 0) {
            $check = Carbon::parse($wdms->punch_time);
            return $check;
        }
    }
    public static function getAbsensiMasuk($sn_fp, $tanggal)
    {
        return self::with('user')
            ->where('terminal_sn', $sn_fp)
            ->whereDate('punch_time', $tanggal)
            ->whereTime('punch_time', '<=', '12:00')
            ->select('emp_id', 'punch_time', 'emp_code')
            ->get()
            ->sortBy('punch_time')
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
            ->values()->reject(fn($item) => $item->user_id == 1)
            ->unique('emp_id');
    }
}
