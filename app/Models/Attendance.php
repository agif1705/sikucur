<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'nagari_id',
        'absensi_by',
        'absensi',
        'accept',
        'accept_by',
        'keterangan_absensi',
        'jadwal_latitude',
        'jadwal_longitude',
        'jadwal_start_time',
        'jadwal_end_time',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'start_time',
        'end_time',
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
}
