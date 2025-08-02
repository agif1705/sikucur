<?php

namespace App\Models;

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
}
