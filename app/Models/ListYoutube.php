<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListYoutube extends Model
{
    protected $fillable = [
        'nagari_id',
        'id_youtube',
        'url',
    ];
    public function nagari()
    {
        return $this->belongsTo(Nagari::class);
    }
}
