<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class IzinPegawai extends Model
{
    protected $fillable = [
        'user_id',
        'nagari',
        'link',
        'used',
        'expired_at',
    ];
    protected $casts = [
        'expired_at' => 'datetime',
        'used' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
