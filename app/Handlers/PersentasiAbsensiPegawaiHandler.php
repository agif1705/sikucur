<?php

namespace App\Handlers;

use App\Models\User;
use Illuminate\Support\Carbon;
use App\Models\RekapAbsensiPegawai;
use App\Contracts\WhatsAppCommandHandler;

class PersentasiAbsensiPegawaiHandler implements WhatsAppCommandHandler
{
    public function handle($user, $chat, $senderData)
    {
        $bulan = now()->month;
        $tahun = now()->year;

        // total hari kerja bulan ini
        $hari_kerja = $this->getWorkingDaysThisMonth($bulan, $tahun);
        $rekap = new RekapAbsensiPegawai();
        $holidays = $rekap->Holiday($bulan, $tahun);
        $total_hari_kerja = $hari_kerja - $holidays;

        // ambil semua pegawai dengan hitungan
        $pegawais = User::withCount([
            // jumlah kehadiran
            'RekapAbsensiPegawai as hadir_count' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('date', $bulan)
                    ->whereYear('date', $tahun);
            },
            // jumlah terlambat
            'RekapAbsensiPegawai as late_count' => function ($query) use ($bulan, $tahun) {
                $query->where('is_late', true)
                    ->whereMonth('date', $bulan)
                    ->whereYear('date', $tahun);
            },
        ])->get()->except(1);

        // format pesan WhatsApp
        $pesan = "📊 Rekap Kehadiran Pegawai Bulan " . now()->format('F Y') . "\n";
        $pesan .= "📅 Total Hari Kerja: {$total_hari_kerja}\n\n";

        foreach ($pegawais as $pegawai) {
            $hadir = $pegawai->hadir_count;
            $terlambat = $pegawai->late_count;
            $tepat_waktu = $hadir - $terlambat;
            $tidak_hadir = $total_hari_kerja - $hadir;

            // hitung persentase
            $persen_hadir = $total_hari_kerja > 0 ? round(($hadir / $total_hari_kerja) * 100, 2) : 0;
            $persen_tepat = $hadir > 0 ? round(($tepat_waktu / $hadir) * 100, 2) : 0;

            $pesan .= "👤 {$pegawai->slug}\n";
            $pesan .= "   ✅ Hadir: {$hadir} hari ({$persen_hadir}%)\n";
            $pesan .= "   ⏰ Terlambat: {$terlambat} kali\n";
            $pesan .= "   🟢 Tepat Waktu: {$tepat_waktu} kali ({$persen_tepat}%)\n";
            $pesan .= "   ❌ Tidak Hadir: {$tidak_hadir} hari\n\n";
        }

        return [
            'success' => true,
            'message' => $pesan,
            'data' => self::sender($senderData),
        ];
    }
    public static function sender($senderData)
    {
        return [
            'pegawai' => true,
            'data' => $senderData,
        ];
    }
    protected static function getWorkingDaysThisMonth($month, $year)
    {
        $today = Carbon::today();
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $today; // Hingga kemarin

        $totalWorkingDays = 0;

        while ($start <= $end) {
            if (!$start->isWeekend()) {
                $totalWorkingDays++;
            }
            $start->addDay();
        }

        return $totalWorkingDays;
    }
}
