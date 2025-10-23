<?php
// filepath: app/Models/PermohonanSurat.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PermohonanSurat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_permohonan',
        'nomor',
        'jenis_surat_id',
        'nagari_id',
        'status_id',
        'petugas_id',
        'penduduk_id',
        'tanggal_permohonan',
        'tanggal_surat',
        'tanggal_estimasi_selesai',
        'pemohon_nik',
        'pemohon_nama',
        'pemohon_alamat',
        'pemohon_telepon',
        'pemohon_email',
        'pemohon_jk',
        'pemohon_tempat_lahir',
        'pemohon_tanggal_lahir',
        'pemohon_agama',
        'pemohon_template',
        'pemohon_judul_surat',
        'pemohon_kode_surat',
        'form_data',
        'PejabatTandaTangan_nama',
        'PejabatTandaTangan_jabatan',
        'TandaTangan',
        'keperluan',
        'catatan_petugas',
    ];

    protected $casts = [
        'form_data' => 'array',
        'tanggal_permohonan' => 'datetime',
        'tanggal_surat' => 'date',
        'tanggal_estimasi_selesai' => 'datetime',
        'pemohon_tanggal_lahir' => 'date',
    ];

    // Relationships
    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function nagari()
    {
        return $this->belongsTo(Nagari::class);
    }

    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function status()
    {
        return $this->belongsTo(StatusSurat::class, 'status_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function pejabatTandaTangan()
    {
        return $this->belongsTo(User::class, 'TandaTangan');
    }

    public static function generateNomorUrut($jenisSuratId, $tanggal)
    {
        $tanggalCarbon = Carbon::parse($tanggal);
        $bulan = $tanggalCarbon->month;
        $tahun = $tanggalCarbon->year;

        $count = static::where('jenis_surat_id', $jenisSuratId)
            ->whereYear('tanggal_permohonan', $tahun)
            ->whereMonth('tanggal_permohonan', $bulan)
            ->count();

        return $count + 1;
    }

    /**
     * Get nomor surat lengkap
     */
    public static function getNomorSuratLengkapAttribute($jenisSuratId)
    {
        $surat = JenisSurat::find($jenisSuratId);
        $nomorUrut = str_pad(static::generateNomorUrut($jenisSuratId, now()), 3, '0', STR_PAD_LEFT);
        $prefix = 'NS';
        $kodeSurat = $surat->kode_surat;
        $kode = $surat->kode;

        $tahun = now()->year;
        $bulan = now()->month;
        $bulanRomawi = static::getBulanRomawiAttribute($bulan);

        return "{$kode}/{$nomorUrut}/{$kodeSurat}/{$prefix}/{$bulanRomawi}-{$tahun}";
    }

    /**
     * Get bulan dalam format romawi
     */
    public static function getBulanRomawiAttribute($bulan)
    {
        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $romawi[$bulan] ?? 'I';
    }
    protected $appends = ['nomor_surat_lengkap'];
}
