<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingSurat extends Model
{
    protected $table = 'tracking_surat';

    protected $fillable = [
        'permohonan_id',
        'status_lama_id',
        'status_baru_id',
        'petugas_id',
        'tanggal_perubahan',
        'catatan'
    ];

    protected $casts = [
        'tanggal_perubahan' => 'datetime'
    ];

    public function permohonanSurat(): BelongsTo
    {
        return $this->belongsTo(PermohonanSurat::class, 'permohonan_id');
    }

    public function statusLama(): BelongsTo
    {
        return $this->belongsTo(StatusSurat::class, 'status_lama_id');
    }

    public function statusBaru(): BelongsTo
    {
        return $this->belongsTo(StatusSurat::class, 'status_baru_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tanggal_perubahan)) {
                $model->tanggal_perubahan = now();
            }
        });
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('tanggal_perubahan', 'desc');
    }

    public function getFormatTanggalAttribute(): string
    {
        return $this->tanggal_perubahan->format('d M Y H:i');
    }

    public function getDeskripsiPerubahanAttribute(): string
    {
        $from = $this->statusLama ? $this->statusLama->nama_status : 'Baru';
        $to = $this->statusBaru->nama_status;

        return "Status berubah dari '{$from}' menjadi '{$to}'";
    }
}
