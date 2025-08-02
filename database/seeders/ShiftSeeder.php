<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::create([
            'name' => 'pagi',
            'start_time' => now()->setTime(8, 0, 0)->format('H:i:s'),
            'end_time' => now()->setTime(14, 0, 0)->format('H:i:s'),
            'is_aktif' => true,
        ]);
        Shift::create([
            'name' => 'siang',
            'start_time' => now()->setTime(11, 0, 0)->format('H:i:s'),
            'end_time' => now()->setTime(21, 0, 0)->format('H:i:s'),
            'is_aktif' => false,
        ]);
        Shift::create([
            'name' => 'malam',
            'start_time' => now()->setTime(20, 0, 0)->format('H:i:s'),
            'end_time' => now()->setTime(8, 0, 0)->format('H:i:s'),
            'is_aktif' => false,

        ]);
    }
}
