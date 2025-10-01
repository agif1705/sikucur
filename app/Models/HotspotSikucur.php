<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotspotSikucur extends Model
{
    protected $fillable = [
        'penduduk_id',
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
    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }
}
