<?php

namespace Database\Seeders;

use App\Models\Nagari;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NagariWaliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Nagari::all()->each(function ($nagari) {
            $wali = 2; // ambil user acak
            $nagari->wali()->associate($wali);
            $nagari->save();
        });
    }
}
