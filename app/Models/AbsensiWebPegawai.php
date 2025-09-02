<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiWebPegawai extends Model
{
    protected $fillable = [
        'alasan',
        'absensi',
        'is_late',
        'file_pendukung',
        'user_id',
        'nagari_id',
        'time_in',
        'time_out',
        'date',
    ];
    protected $casts = [
        'file_pendukung' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function nagari()
    {
        return $this->belongsTo(Nagari::class);
    }
}
