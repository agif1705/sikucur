<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SuratPengantar extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_WAITING_APPROVAL = 'waiting_approval';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_REJECTED = 'rejected';

    public const WILAYAHS = [
        'Bunga Tanjung',
        'Durian Kadok',
        'Sungai Janiah',
        'Lansano',
    ];

    protected $fillable = [
        'nagari_id',
        'penduduk_id',
        'petugas_id',
        'wali_korong_id',
        'jenis_surat_id',
        'token',
        'status',
        'used',
        'expired_at',
        'tanggal_pengantar',
        'wali_response_at',
        'pemohon_nik',
        'pemohon_nama',
        'pemohon_alamat',
        'pemohon_alamat_domisili',
        'pemohon_telepon',
        'korong',
        'keperluan',
        'form_data',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'tanggal_pengantar' => 'date',
        'wali_response_at' => 'datetime',
        'used' => 'boolean',
        'form_data' => 'array',
    ];

    public function nagari(): BelongsTo
    {
        return $this->belongsTo(Nagari::class);
    }

    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function waliKorong(): BelongsTo
    {
        return $this->belongsTo(WaliKorong::class, 'wali_korong_id');
    }

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class, 'jenis_surat_id');
    }

    public function permohonanSurat(): HasOne
    {
        return $this->hasOne(PermohonanSurat::class, 'surat_pengantar_id');
    }

    public function isExpired(): bool
    {
        return $this->expired_at !== null && $this->expired_at->isPast();
    }

    public function isReadyForPermohonan(): bool
    {
        return $this->status === self::STATUS_SUBMITTED && ! $this->permohonanSurat()->exists();
    }

    public static function wilayahOptions(): array
    {
        return array_combine(self::WILAYAHS, self::WILAYAHS);
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_WAITING_APPROVAL => 'Menunggu Persetujuan Wali Korong',
            self::STATUS_SUBMITTED => 'Disetujui Wali Korong',
            self::STATUS_REJECTED => 'Ditolak Wali Korong',
        ];
    }
}
