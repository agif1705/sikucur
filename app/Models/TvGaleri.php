<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TvGaleri extends Model
{
    protected $fillable = [
        'nagari_id',
        'name',
        'image'
    ];


    public function nagari()
    {
        return $this->belongsTo(Nagari::class);
    }
}
