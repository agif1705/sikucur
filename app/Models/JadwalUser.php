<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalUser extends Model
{
    protected $fillable = [
        'user_id',
        'shift_id',
        'nagari_id',

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function nagari()
    {
        return $this->belongsTo(Nagari::class);
    }
}
