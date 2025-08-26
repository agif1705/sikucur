<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppCommand extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    protected $fillable = [
        'footer_whats_app_id',
        'nagari_id',
        'command',
        'description',
        'handler_class',
        'is_active',
    ];
    /**
     * Get the footer that owns the WhatsAppCommand
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function footer(): BelongsTo
    {
        return $this->belongsTo(FooterWhatsApp::class, 'footer_whatsapp_id', 'id');
    }
    /**
     * Get the nagari that owns the WhatsAppCommand
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nagari(): BelongsTo
    {
        return $this->belongsTo(Nagari::class, 'nagari_id', 'id');
    }
}
