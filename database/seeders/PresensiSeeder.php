<?php

namespace Database\Seeders;

use App\Models\Presensi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Presensi::create([
            'name' => 'Hadir',
            'slug' => 'H',
            'is_aktif' => true,
        ]);
        Presensi::create([
            'name' => 'Hadir Dinas Luar Daerah',
            'slug' => 'HDL',
            'is_aktif' => true,
        ]);
        Presensi::create([
            'name' => 'Hadir Dinas Dalam Daerah',
            'slug' => 'HDD',
            'is_aktif' => true,
        ]);
        Presensi::create([
            'name' => 'Sakit',
            'slug' => 'S',
            'is_aktif' => true,
        ]);
        Presensi::create([
            'name' => 'Izin',
            'slug' => 'I',
            'is_aktif' => true,
        ]);
        Presensi::create([
            'name' => 'Alpha',
            'slug' => 'A',
            'is_aktif' => true,
        ]);
    }
}
