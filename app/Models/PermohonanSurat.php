<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PermohonanSurat extends Model
{
    protected $table = 'permohonan_surat';

    protected $fillable = [
        'nomor_permohonan',
        'jenis_surat_id',
        'nagari_id',
        'pemohon_nik',
        'pemohon_nama',
        'pemohon_alamat',
        'pemohon_telepon',
        'pemohon_email',
        'keperluan',
        'status_id',
        'tanggal_permohonan',
        'tanggal_estimasi_selesai',
        'tanggal_selesai',
        'petugas_id',
        'catatan_petugas',
        'data_tambahan'
    ];

    protected $casts = [
        'tanggal_permohonan' => 'datetime',
        'tanggal_estimasi_selesai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'data_tambahan' => 'array'
    ];

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function nagari(): BelongsTo
    {
        return $this->belongsTo(Nagari::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusSurat::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function uploadDokumen(): HasMany
    {
        return $this->hasMany(UploadDokumen::class);
    }

    public function trackingSurat(): HasMany
    {
        return $this->hasMany(TrackingSurat::class);
    }

    public function suratGenerated(): HasOne
    {
        return $this->hasOne(SuratGenerated::class);
    }

    // Auto generate nomor permohonan
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_permohonan)) {
                $model->nomor_permohonan = $model->generateNomorPermohonan();
            }
        });
    }

    private function generateNomorPermohonan(): string
    {
        $tahun = date('Y');
        $bulan = date('m');
        $nagariCode = $this->nagari->kode ?? '001';

        $lastNumber = static::whereYear('tanggal_permohonan', $tahun)
            ->whereMonth('tanggal_permohonan', $bulan)
            ->where('nagari_id', $this->nagari_id)
            ->count() + 1;

        return sprintf('%03d/SURAT/%s/%s/%s', $lastNumber, $nagariCode, $bulan, $tahun);
    }
}