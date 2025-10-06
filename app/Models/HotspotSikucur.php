<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotspotSikucur extends Model
{
    protected $fillable = [
        'penduduk_id',
        'mikrotik_config_id',
        'ret_id',
        'phone_mikrotik',
        'mikrotik_id',
        'status',
        'activated_at',
        'expired_at',
    ];
    protected $casts = [
        'activated_at' => 'datetime',
        'expired_at' => 'datetime',
        'status' => 'boolean',
    ];
    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'penduduk_id');
    }
    public function mikrotikConfig(): BelongsTo
    {
        return $this->belongsTo(MikrotikConfig::class);
    }
}
