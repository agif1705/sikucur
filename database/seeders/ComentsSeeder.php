<?php

namespace Database\Seeders;

use App\Models\Coments;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Coments::create([
                'jabatan_id' => rand(1, 10),
                'name' => fake()->name(),
                'no_hp' => fake()->phoneNumber(),
                'coment' => fake()->slug(),
                'status' => rand(0, 1),
            ]);
        }
    }
}
