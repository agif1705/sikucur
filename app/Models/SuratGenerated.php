<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SuratGenerated extends Model
{
    protected $table = 'surat_generated';

    protected $fillable = [
        'permohonan_id',
        'nomor_surat',
        'file_path',
        'qr_code_path',
        'tanggal_terbit',
        'berlaku_sampai',
        'ditandatangani_oleh',
        'jabatan_penandatangan'
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'berlaku_sampai' => 'date'
    ];

    public function permohonanSurat(): BelongsTo
    {
        return $this->belongsTo(PermohonanSurat::class, 'permohonan_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_surat)) {
                $model->nomor_surat = $model->generateNomorSurat();
            }

            if (empty($model->tanggal_terbit)) {
                $model->tanggal_terbit = now()->toDateString();
            }
        });
    }

    private function generateNomorSurat(): string
    {
        $permohonan = $this->permohonanSurat;
        $jenisSurat = $permohonan->jenisSurat;
        $nagari = $permohonan->nagari;

        $tahun = date('Y');
        $bulan = date('m');

        $lastNumber = static::whereYear('tanggal_terbit', $tahun)
            ->whereMonth('tanggal_terbit', $bulan)
            ->whereHas('permohonanSurat', function($q) use ($permohonan) {
                $q->where('jenis_surat_id', $permohonan->jenis_surat_id)
                  ->where('nagari_id', $permohonan->nagari_id);
            })
            ->count() + 1;

        return sprintf(
            '%03d/%s/%s/%s/%s',
            $lastNumber,
            $jenisSurat->kode_surat,
            $nagari->kode ?? 'NGR',
            $bulan,
            $tahun
        );
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getQrCodeUrlAttribute(): ?string
    {
        return $this->qr_code_path ? Storage::url($this->qr_code_path) : null;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->berlaku_sampai && $this->berlaku_sampai->isPast();
    }

    public function getSisaMasaBerlakuAttribute(): ?int
    {
        if (!$this->berlaku_sampai) {
            return null;
        }

        return max(0, now()->diffInDays($this->berlaku_sampai, false));
    }

    public function getStatusBerlakuAttribute(): string
    {
        if (!$this->berlaku_sampai) {
            return 'Tidak Ada Batas Waktu';
        }

        if ($this->is_expired) {
            return 'Sudah Kadaluarsa';
        }

        $sisa = $this->sisa_masa_berlaku;
        if ($sisa <= 30) {
            return "Akan Berakhir dalam {$sisa} hari";
        }

        return 'Masih Berlaku';
    }

    public function getStatusColorAttribute(): string
    {
        if (!$this->berlaku_sampai) {
            return 'primary';
        }

        if ($this->is_expired) {
            return 'danger';
        }

        $sisa = $this->sisa_masa_berlaku;
        if ($sisa <= 7) {
            return 'danger';
        } elseif ($sisa <= 30) {
            return 'warning';
        }

        return 'success';
    }
}