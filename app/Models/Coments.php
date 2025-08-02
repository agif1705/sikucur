<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coments extends Model
{
    protected $fillable = [
        'name',
        'jabatan_id',
        'no_hp',
        'coment',
        'status',
    ];

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }
}
