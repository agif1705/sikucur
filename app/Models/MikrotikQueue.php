<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MikrotikQueue extends Model
{
    protected $fillable = [
        'mikrotik_config_id',
        'ret_id',
        'name',
        'target',
        'dst',
        'parent',
        'packet_marks',
        'priority',
        'queue_type',
        'limit_at',
        'max_limit',
        'burst_limit',
        'burst_threshold',
        'burst_time',
        'rate',
        'bytes',
        'total_bytes',
        'packets',
        'total_packets',
        'comment',
        'dynamic',
        'disabled',
        'invalid',
    ];

    protected $casts = [
        'dynamic' => 'boolean',
        'disabled' => 'boolean',
        'invalid' => 'boolean',
    ];

    public function mikrotikConfig(): BelongsTo
    {
        return $this->belongsTo(MikrotikConfig::class);
    }
}
