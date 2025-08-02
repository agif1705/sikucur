<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterWhatsApp extends Model
{
    protected $fillable = [
        'footer_whatsapp_id',
        'nagari_id',
        'command',
        'response',
        'is_active',
    ];
}
