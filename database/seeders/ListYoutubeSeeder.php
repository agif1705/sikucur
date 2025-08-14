<?php

namespace Database\Seeders;

use App\Models\ListYoutube;
use Termwind\Components\Li;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ListYoutubeSeeder extends Seeder
{
    public $playlist = [
        "RpTfJV4ux1c",
        "V5s36YgfWv8",
        "Sz61lW5trNQ",
        "6vIqikH2QvY",
        "-nbJgfkgSg8",
        "s6vac3hP6yM",
        "k9bCgz5xTms",
        "j-vOeGOCKio",
        "coz56CHNjjE",
        "jXNSJnxkXeE",
    ];
    public function run(): void
    {
        foreach ($this->playlist as $item) {
            ListYoutube::create([
                'url' => 'https://www.youtube.com/@coba',
                'nagari_id' => 1,
                'id_youtube' => $item
            ]);
        }
    }
}
