<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'password',
        'name',
        'expires_at',
        'mikrotik_config_id',
        'created_by',
        'active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function mikrotikConfig()
    {
        return $this->belongsTo(MikrotikConfig::class);
    }
}
