<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MikrotikDhcpLease extends Model
{
    protected $fillable = [
        'mikrotik_config_id',
        'ret_id',
        'mac_address',
        'address',
        'active_address',
        'server',
        'host_name',
        'client_id',
        'status',
        'last_seen',
        'comment',
        'dynamic',
        'disabled',
        'blocked',
    ];

    protected $casts = [
        'dynamic' => 'boolean',
        'disabled' => 'boolean',
        'blocked' => 'boolean',
    ];

    public function mikrotikConfig(): BelongsTo
    {
        return $this->belongsTo(MikrotikConfig::class);
    }
}
