<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\AbsensiPegawai;
use Illuminate\Console\Command;

class WhatsappKirimLaporanAbsensi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:whatsapp-kirim-laporan-absensi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = AbsensiPegawai::whereDate('date_in', Carbon::today())->get();

        // Misal kirim ke API, email, atau simpan log
        \Log::info('Laporan Absensi Hari Ini', ['total' => $data->count()]);

        $this->info('Laporan absensi terkirim!');
    }
}
