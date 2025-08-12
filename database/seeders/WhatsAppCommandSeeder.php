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
            'description' => 'Informasi perintah WhatsApp',
            'handler_class' => 'App\Handlers\InfoPerintahHandler',
            'is_active' => true,
        ]);
        WhatsAppCommand::create([
            'id' => Str::uuid(),
            'footer_whats_app_id' => 1,
            'nagari_id' => 1,
            'command' => 'izin',
            'description' => 'Form izin pegawai',
            'handler_class' => 'App\Handlers\IzinPegawaiHandler',
            'is_active' => true,
        ]);
    }
}
