<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function mikrotikConfig(): BelongsTo
    {
        return $this->belongsTo(MikrotikConfig::class);
    }
}
