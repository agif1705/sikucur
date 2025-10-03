<?php

namespace App\Handlers;

use App\Models\User;
use Illuminate\Support\Carbon;
use App\Models\RekapAbsensiPegawai;
use App\Contracts\WhatsAppCommandHandler;

class AbsensiPegawaiBulananHandler implements WhatsAppCommandHandler
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

        // ambil hanya user ini dengan hitungan
        $pegawai = User::withCount([
            // jumlah kehadiran
            'RekapAbsensiPegawai as hadir_count' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('date', $bulan)
                    ->whereYear('date', $tahun);
            },
            'RekapAbsensiPegawai as sakit_count' => function ($query) use ($bulan, $tahun) {
                $query->where('status_absensi', 'S')
                    ->whereMonth('date', $bulan)
                    ->whereYear('date', $tahun);
            },
            'RekapAbsensiPegawai as izin_count' => function ($query) use ($bulan, $tahun) {
                $query->where('status_absensi', 'I')
                    ->whereMonth('date', $bulan)
                    ->whereYear('date', $tahun);
            },
            // jumlah terlambat
            'RekapAbsensiPegawai as late_count' => function ($query) use ($bulan, $tahun) {
                $query->where('is_late', true)
                    ->whereMonth('date', $bulan)
                    ->whereYear('date', $tahun);
            },

        ])->find($user->id);
        $rekap_kehadiran = $user->load(['RekapAbsensiPegawai' => function ($query) use ($bulan, $tahun) {
            $query->whereMonth('date', $bulan)
                ->whereYear('date', $tahun)
                ->orderBy('date', 'asc');
        }]);

        // dd($rekap_kehadiran->RekapAbsensiPegawai);
        // format pesan WhatsApp
        $pesan = "📊 Rekap Kehadiran Pegawai " . $user->name . " di Nagari *" . $user->nagari->name . "* Bulan *" . now()->format('F Y') . "*\n";

        if ($pegawai) {
            $pesan .= "📜 History Kehadiran:\n";
            foreach ($rekap_kehadiran->RekapAbsensiPegawai as $absensi) {
                if ($absensi->status_absensi == 'S') {
                    $pesan .= "• {$absensi->date}  | 🤒 SAKIT\n";
                }
                if ($absensi->status_absensi == 'I') {
                    $pesan .= "• {$absensi->date}  | 📝 IZIN\n";
                } else {
                    $status = $absensi->is_late ? "⏰ Terlambat" : "🟢 Tepat Waktu";
                    $pesan .= "• {$absensi->date} | Jam Masuk: {$absensi->time_in} | {$status}\n";
                }
            }
            $hadir = $pegawai->hadir_count;
            $sakit = $pegawai->sakit_count;
            $izin = $pegawai->izin_count;
            $terlambat = $pegawai->late_count;
            $tepat_waktu = $hadir - $terlambat;
            $tidak_hadir = $total_hari_kerja - $hadir;

            // hitung persentase
            $persen_hadir = $total_hari_kerja > 0 ? round(($hadir / $total_hari_kerja) * 100, 2) : 0;
            $persen_tepat = $hadir > 0 ? round(($tepat_waktu / $hadir) * 100, 2) : 0;

            $pesan .= "\n 📅 Total Hari Kerja: {$total_hari_kerja} Hari \n\n";

            $pesan .= "👤 {$pegawai->slug}\n";
            $pesan .= "   ✅ Total Hadir: {($hadir - $sakit - $izin)} hari ({$persen_hadir}%)\n";
            $pesan .= "   🤒 Sakit: {$sakit} hari\n";
            $pesan .= "   📝 Izin: {$izin} hari\n";
            $pesan .= "   ⏰ Terlambat: {$terlambat} kali\n";
            $pesan .= "   🟢 Tepat Waktu: {$tepat_waktu} kali ({$persen_tepat}%)\n";
            $pesan .= "   ❌ Tidak Hadir: {$tidak_hadir} hari\n\n";
        } else {
            $pesan .= "❌ Data pegawai tidak ditemukan.\n";
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
