<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TvInformasi extends Model
{
    protected $fillable = [
        'name',
        'running_text',
        'nagari_id',
        'user_id',
        'video',
        'bupati',
        'bupati_image',
        'wakil_bupati',
        'wakil_bupati_image',
        'wali_nagari',
        'wali_nagari_image',
        'bamus',
        'bamus_image',
        'babinsa',
        'babinsa_image',
    ];

    public function nagari()
    {
        return $this->belongsTo(Nagari::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function galeri()
    {
        return $this->hasMany(TvGaleri::class);
    }
}
