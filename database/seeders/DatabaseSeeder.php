<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Coments;
use App\Models\FooterWhatsApp;
use App\Models\ListYoutube;
use App\Models\WhatsAppCommand;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            NagariSeeder::class,
            JabatanSeeder::class,
            UserSeeder::class,
            // PegawaiSeeder::class,
            // KantorSeeder::class,
            ShiftSeeder::class,
            JadwalUserSeeder::class,
            ShieldSeeder::class,
            RoleSeeder::class,
            ModelHasRoleSeeder::class,
            ComentsSeeder::class,
            PresensiSeeder::class,
            TvInformasiSeeder::class,
            TvGaleriSeeder::class,
            FooterWhatsAppSeeder::class,
            WhatsAppCommandSeeder::class,
            ListYoutubeSeeder::class
        ]);
    }
}
