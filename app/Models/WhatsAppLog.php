<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppLog extends Model
{
     protected $fillable = [
        'user_id', 'phone', 'message', 'status', 'response'
    ];

    protected $casts = [
        'response' => 'array',
    ];
}
