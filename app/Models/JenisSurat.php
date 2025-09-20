<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisSurat extends Model
{
    protected $table = 'jenis_surat';

    protected $fillable = [
        'nama_jenis',
        'kode_surat',
        'persyaratan',
        'template_path',
        'estimasi_hari',
        'keterangan',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'estimasi_hari' => 'integer'
    ];

    public function permohonanSurat(): HasMany
    {
        return $this->hasMany(PermohonanSurat::class);
    }

    public function dokumenPersyaratan(): HasMany
    {
        return $this->hasMany(DokumenPersyaratan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
