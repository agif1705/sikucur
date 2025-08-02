<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorySendWa extends Model
{
    protected $fillable = [
        'user_id',
        'nagari_id',
        'sender',
        'send',
        'by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
