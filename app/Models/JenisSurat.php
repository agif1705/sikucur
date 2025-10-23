<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisSurat extends Model
{
    protected $table = 'jenis_surat';

    protected $fillable = [
        'nama_jenis',
        'nama',
        'kode',
        'url_surat',
        'kode_surat',
        'lampiran',
        'mandiri',
        'template',
        'template_desa',
        'form_isian',
        'kode_isian',
        'orientasi',
        'ukuran',
        'syarat_surat',
        'margin'
    ];
    protected $casts = [
        'kode_isian' => 'array', // Cast sebagai array
        'estimasi_hari' => 'integer',
    ];
    public function getDynamicFields()
    {
        return $this->kode_isian ?? [];
    }
    public function renderTemplate($data = [])
    {
        $template = $this->template_path;

        foreach ($data as $key => $value) {
            $template = str_replace($key, $value, $template);
        }

        return $template;
    }
    public function dokumenPersyaratan(): HasMany
    {
        return $this->hasMany(DokumenPersyaratan::class, 'jenis_surat_id');
    }
}
