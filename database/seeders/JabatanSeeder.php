<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jabatan::create([
            'name' => 'SuperAdmin',
            'slug' => 'SuperAdmin',
        ]);
        Jabatan::create([
            'name' => 'WaliNagari',
            'slug' => 'WaliNagari',
        ]);
        Jabatan::create([
            'name' => 'Seketaris',
            'slug' => 'Seketaris',
        ]);
        Jabatan::create([
            'name' => 'Kaur Keuangan',
            'slug' => 'kaur-keuangan',
        ]);

        Jabatan::create([
            'name' => 'Kaur Umum dan Perencanan',
            'slug' => 'Kaur-umum-dan-perencanan',
        ]);
        Jabatan::create([
            'name' => 'Kasi Kesejahteraan',
            'slug' => 'Kasi-Kesejahteraan',
        ]);
        Jabatan::create([
            'name' => 'Kasi Pemerintahan',
            'slug' => 'Kasi-Pemerintahan',
        ]);
        Jabatan::create([
            'name' => 'Kasi Pelayanan',
            'slug' => 'Kasi-Pelayanan',
        ]);
        Jabatan::create([
            'name' => 'Wali Korong',
            'slug' =>  'Wali-Korong',
        ]);
        Jabatan::create([
            'name' => 'Staf Pelayanan',
            'slug' => 'Staf',
        ]);
        Jabatan::create([
            'name' => 'Staf',
            'slug' => 'Staf',
        ]);
        Jabatan::create([
            'name' => 'HPL',
            'slug' => 'HPL',
        ]);
    }
}
