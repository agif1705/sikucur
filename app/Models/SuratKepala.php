<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKepala extends Model
{
    protected $fillable = [
        'nagari_id',
        'logo',
        'kop_surat'
    ];
    public function nagari()
    {
        return $this->belongsTo(Nagari::class);
    }
}