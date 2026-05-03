<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
            RoleSeeder::class,
            UserSeeder::class,
            WaliKorongSeeder::class,
            // PegawaiSeeder::class,
            // KantorSeeder::class,
            ShiftSeeder::class,
            JadwalUserSeeder::class,
            ShieldSeeder::class,
            ModelHasRoleSeeder::class,
            // ComentsSeeder::class,
            PresensiSeeder::class,
            TvInformasiSeeder::class,
            TvGaleriSeeder::class,
            FooterWhatsAppSeeder::class,
            WhatsAppCommandSeeder::class,
            ListYoutubeSeeder::class,
            NagariWaliSeeder::class,
            JenisSuratSeeder::class,
            StatusSuratSeeder::class,
            PendudukSeeder::class,
            MikrotikConfigSeeder::class,
            HotspotSikucurSeeder::class,
        ]);
    }
}
