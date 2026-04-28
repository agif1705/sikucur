<?php

namespace Database\Seeders;

use App\Models\WhatsAppCommand;
use Illuminate\Database\Seeder;

class WhatsAppCommandSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->commands() as $command) {
            WhatsAppCommand::updateOrCreate(
                [
                    'nagari_id' => $command['nagari_id'],
                    'command' => $command['command'],
                ],
                $command
            );
        }
    }

    private function commands(): array
    {
        return [
            [
                'footer_whats_app_id' => 1,
                'nagari_id' => 1,
                'command' => 'info',
                'description' => 'Informasi perintah WhatsApp untuk pegawai. Contoh: absensi-pegawai',
                'handler_class' => 'App\Handlers\InfoPerintahHandler',
                'is_active' => true,
            ],
            [
                'footer_whats_app_id' => 1,
                'nagari_id' => 1,
                'command' => 'izin',
                'description' => 'Form absensi pegawai termasuk izin, sakit, dinas luar, dan dinas dalam.',
                'handler_class' => 'App\Handlers\IzinPegawaiHandler',
                'is_active' => true,
            ],
            [
                'footer_whats_app_id' => 1,
                'nagari_id' => 1,
                'command' => 'persen-absensi-pegawai',
                'description' => 'Melihat persentase absensi pegawai bulan ini.',
                'handler_class' => 'App\Handlers\PersentasiAbsensiPegawaiHandler',
                'is_active' => true,
            ],
            [
                'footer_whats_app_id' => 1,
                'nagari_id' => 1,
                'command' => 'absensi-pegawai',
                'description' => 'Melihat persentase kehadiran pegawai bulan ini.',
                'handler_class' => 'App\Handlers\AbsensiPegawaiHandler',
                'is_active' => true,
            ],
            [
                'footer_whats_app_id' => 1,
                'nagari_id' => 1,
                'command' => 'surat',
                'description' => 'Generate link pengisian surat pengantar wali korong/RT. Link berlaku 30 menit.',
                'handler_class' => 'App\Handlers\SuratPengantarHandler',
                'is_active' => true,
            ],
        ];
    }
}