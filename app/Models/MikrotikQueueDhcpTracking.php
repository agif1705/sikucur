<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MikrotikQueueDhcpTracking extends Model
{
    protected $table = 'mikrotik_queue_dhcp_trackings';

    protected $guarded = [];

    public $timestamps = false;

    public function mikrotikConfig(): BelongsTo
    {
        return $this->belongsTo(MikrotikConfig::class);
    }
}
