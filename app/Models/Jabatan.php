<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }
    public function coments()
    {
        return $this->hasMany(Coments::class);
    }
}
