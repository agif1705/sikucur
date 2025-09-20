<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DokumenPersyaratan extends Model
{
    protected $table = 'dokumen_persyaratan';

    protected $fillable = [
        'jenis_surat_id',
        'nama_dokumen',
        'keterangan',
        'is_wajib',
        'urutan'
    ];

    protected $casts = [
        'is_wajib' => 'boolean',
        'urutan' => 'integer'
    ];

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function uploadDokumen(): HasMany
    {
        return $this->hasMany(UploadDokumen::class);
    }

    public function scopeWajib($query)
    {
        return $query->where('is_wajib', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }

    public function getStatusWajibAttribute(): string
    {
        return $this->is_wajib ? 'Wajib' : 'Opsional';
    }
}