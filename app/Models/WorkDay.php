<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkDay extends Model
{

    protected $fillable = ['office_id', 'day', 'is_working_day'];

    public function nagari()
    {
        return $this->belongsTo(Nagari::class);
    }
}
