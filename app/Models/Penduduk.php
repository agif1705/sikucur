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
        'jk',
        'tempat_lahir',
        'tanggal_lahir',
        'korong',
        'kk',
        'kepala_keluarga',
        'no_hp',
    ];
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
