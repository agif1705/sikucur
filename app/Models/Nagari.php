<?php

namespace App\Models;

use App\Models\WorkDay;
use Illuminate\Database\Eloquent\Model;

class Nagari extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'alamat',
        'logo',
        'longitude',
        'latitude',

    ];
    // public function attendences()
    // {
    //     return $this->belongsTo(Attendance::class);
    // }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function whatsAppCommand()
    {
        return $this->hasMany(WhatsAppCommand::class);
    }
    public function wali()
    {
        return $this->belongsTo(User::class, 'wali_id')->select('id', 'name', 'no_hp');
    }
    public function workDays()
    {
        return $this->hasMany(WorkDay::class);
    }
    public function initializeDefaultWorkDays()
    {
        $days = [
            ['day' => 'monday', 'is_working_day' => true],
            ['day' => 'tuesday', 'is_working_day' => true],
            ['day' => 'wednesday', 'is_working_day' => true],
            ['day' => 'thursday', 'is_working_day' => true],
            ['day' => 'friday', 'is_working_day' => true],
            ['day' => 'saturday', 'is_working_day' => false],
            ['day' => 'sunday', 'is_working_day' => false],
        ];

        foreach ($days as $day) {
            $this->workDays()->create($day);
        }
    }
    public function TvInformasi()
    {
        return $this->hasOne(TvInformasi::class);
    }
    public function galeri()
    {
        return $this->hasMany(TvGaleri::class);
    }
}
