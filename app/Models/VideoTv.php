<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoTv extends Model
{
    protected $fillable = [
        'nagari_id',
        'title',
        'file_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function nagari(): BelongsTo
    {
        return $this->belongsTo(Nagari::class);
    }
}
