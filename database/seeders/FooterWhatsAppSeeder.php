<?php

namespace Database\Seeders;

use App\Models\FooterWhatsApp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FooterWhatsAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FooterWhatsApp::create([
            'name' => 'admin nagari',
            'footer' => " \n \n \n \n _Sent || via *Cv.Baduo Mitra Solustion*_",
            'token_wuzapi' => 'bp6448fe',
        ]);
    }
}
