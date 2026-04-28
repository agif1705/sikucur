<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Penduduk extends Model
{
    protected $fillable = [
        'nagari_id',
        'name',
        'nik',
        'alamat',
        'alamat_domisili',
        'jk',
        'tempat_lahir',
        'tanggal_lahir',
        'korong',
        'kk',
        'kepala_keluarga',
        'no_hp',
    ];

    protected function setNoHpAttribute($value): void
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?: null;

        if (! $digits) {
            $this->attributes['no_hp'] = null;

            return;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '62') && str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        $this->attributes['no_hp'] = $digits;
    }

    public function getJenisKelaminLabelAttribute()
    {
        return $this->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan';
    }

    public function nagari()
    {
        return $this->belongsTo(Nagari::class);
    }

    public function hotspotSikucur(): HasOne
    {
        return $this->hasOne(HotspotSikucur::class, 'penduduk_id');
    }
}
