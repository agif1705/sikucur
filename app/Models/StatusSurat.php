<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusSurat extends Model
{
    protected $table = 'status_surat';

    protected $fillable = [
        'nama_status',
        'kode_status',
        'warna_status',
        'deskripsi',
        'urutan'
    ];

    public function permohonanSurat(): HasMany
    {
        return $this->hasMany(PermohonanSurat::class, 'status_id');
    }

    public function trackingStatusLama(): HasMany
    {
        return $this->hasMany(TrackingSurat::class, 'status_lama_id');
    }

    public function trackingStatusBaru(): HasMany
    {
        return $this->hasMany(TrackingSurat::class, 'status_baru_id');
    }
}