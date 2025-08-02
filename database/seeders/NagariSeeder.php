<?php

namespace Database\Seeders;

use App\Models\Nagari;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NagariSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $office = Nagari::create([
            'name' => 'Sikucur',
            'logo' => 'sikucurbangkit.png',
            'slug' => 'sikucur',
            'alamat' => 'Jl.Bungo Tanjung ',
            'latitude' => -0.50749418802036,
            'longitude' => 100.13539851192,
            'sn_fingerprint' => 'AEWD200360337',

        ]);
        $office->initializeDefaultWorkDays();
    }
}
