<?php

namespace Database\Seeders;

use App\Models\WhatsAppCommand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WhatsAppCommandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WhatsAppCommand::create([
            'id' => \Str::uuid(),
            'footer_whats_app_id' => 1,
            'nagari_id' => 1,
            'command' => 'halo',
            'response' => 'Halo {name}! Apa kabar?',
            'is_active' => true,
        ]);
        WhatsAppCommand::create([
            'id' => \Str::uuid(),
            'footer_whats_app_id' => 1,
            'nagari_id' => 1,
            'command' => 'izin',
            'response' => 'Mohon untuk di isi form izin ini untuk keperluan:  ' . "\n" . '  {{$link}}',
            'is_active' => true,
        ]);
    }
}
