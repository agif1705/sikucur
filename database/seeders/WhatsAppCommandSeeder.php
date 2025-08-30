<?php

namespace Database\Seeders;

use Str;
use App\Models\WhatsAppCommand;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WhatsAppCommandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WhatsAppCommand::create([
            'id' => Str::uuid(),
            'footer_whats_app_id' => 1,
            'nagari_id' => 1,
            'command' => 'info',
            'description' => 'Informasi perintah WhatsApp untuk pegawai.Contoh perintah ketik kirimkan  "absensi-pegawai"',
            'handler_class' => 'App\Handlers\InfoPerintahHandler',
            'is_active' => true,
        ]);
        WhatsAppCommand::create([
            'id' => Str::uuid(),
            'footer_whats_app_id' => 1,
            'nagari_id' => 1,
            'command' => 'izin',
            'description' => 'Form absensi pegawai termasuk izin,sakit, hadir dinas luar daerah dan hadir dinas dalam daerah',
            'handler_class' => 'App\Handlers\AbsensiPegawaiHandler',
            'is_active' => true,
        ]);
        WhatsAppCommand::create([
            'id' => Str::uuid(),
            'footer_whats_app_id' => 1,
            'nagari_id' => 1,
            'command' => 'persen-absensi-pegawai',
            'description' => 'Form absensi pegawai termasuk izin,sakit, hadir dinas luar daerah dan hadir dinas dalam daerah',
            'handler_class' => 'App\Handlers\PersentasiAbsensiPegawaiHandler',
            'is_active' => true,
        ]);
    }
}
