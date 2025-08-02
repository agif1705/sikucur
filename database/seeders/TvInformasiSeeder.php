<?php

namespace Database\Seeders;

use App\Models\TvInformasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TvInformasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TvInformasi::create([
            'name' => 'Visitasi : Penilaian 5 Besar PKK Nagari Sikucur Se-Kabupaten Padang Pariaman 2022',
            'nagari_id' => 1,
            'user_id' => 2,
            'video' => 'https://www.youtube.com/watch?v=0CBiZBk5cvo',
            'bupati' => 'Jhon Kenedy Aziz, S.H., M.H.',
            'bupati_image' => 'bupati.png',
            'wakil_bupati' => 'Rahmat Hidayat,   S.E., M.M.',
            'wakil_bupati_image' => 'bupati.png',
            'wali_nagari' => 'Asrul Khairi, A.Md',
            'wali_nagari_image' => 'web_wali_nagari.jpg',
            'bamus' => 'Riko Bakrianto, S.Pd',
            'bamus_image' => 'bamus.png',
            'babinsa' => 'Sertu Candra',
            'babinsa_image' => 'bamus.png',
            'running_text' => 'Salam hangat untuk kita semua, Selamat menikmati layanan internet gratis dari Pemerintahan Nagari Sikucur.
Agar lebih bijak dalam penggunaan Internet (internet positif). Semua pemakaian akses internet terpantau dalam server kami.',

        ]);
    }
}
