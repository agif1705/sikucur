<?php

namespace Database\Seeders;

use App\Models\TvGaleri;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TvGaleriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < 47; $i++) {
            TvGaleri::create([
                'nagari_id' => 1,
                'name' => "sikucur",
                'image' => "/galeri/sikucur (" . $i . ").jpg"
            ]);
        }
    }
}
