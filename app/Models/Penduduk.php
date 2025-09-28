<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
