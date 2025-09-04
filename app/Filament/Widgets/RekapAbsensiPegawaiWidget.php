<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Illuminate\Support\Carbon;
use App\Models\RekapAbsensiPegawai;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class RekapAbsensiPegawaiWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $bulan = now()->month;
        $tahun = now()->year;

        // total hari kerja bulan ini
        $hari_kerja = $this->getWorkingDaysThisMonth($bulan, $tahun);
        $rekap = new RekapAbsensiPegawai();
        $holidays = $rekap->Holiday($bulan, $tahun);
        $total_hari_kerja = $hari_kerja - $holidays;

        // ambil hanya user ini dengan hitungan
        $query = RekapAbsensiPegawai::forUserThisMonth(Auth::id());

        $kehadiran   = $query->clone()->whereIn('status_absensi', ['Hadir', 'I', 'S', 'HDLD', 'HDDD'])->count();
        $terlambat   = $query->clone()->where('is_late', true)->count();
        $tepat_waktu = $query->clone()->where('is_late', false)->count();
        $persen_hadir = $total_hari_kerja > 0 ? round(($kehadiran / $total_hari_kerja) * 100, 2) : 0;
        $persen_tepat = $kehadiran > 0 ? round(($tepat_waktu / $kehadiran) * 100, 2) : 0;
        $pk_color = $persen_hadir < 80 ? 'danger' : 'info';
        $pk_icon  = $persen_hadir < 80 ? 'heroicon-o-x-circle' : 'heroicon-o-clock';
        $pk_desc  = $persen_hadir < 80 ? 'Kehadiran Anda di bawah standar dari 80% ' : 'Kehadiran Anda memenuhi standar dari 80% ';
        $pt_color = $persen_tepat < 80 ? 'danger' : 'info';
        $pt_icon  = $persen_tepat < 80 ? 'heroicon-o-x-circle' : 'heroicon-o-clock';
        $pt_desc  = $persen_tepat < 80 ? 'Tepat Waktu Anda di bawah standar dari 80% ' : 'Tepat Waktu Anda memenuhi standar dari 80% ';
        return [
            Stat::make('Total Hari Kerja', $total_hari_kerja)
                ->description(now()->translatedFormat('F Y'))
                ->color('success'),
            Stat::make('Absensi', $kehadiran)
                ->description(now()->translatedFormat('F Y'))
                ->color('success'),
            Stat::make('Terlambat', $terlambat)
                ->description(now()->translatedFormat('F Y'))
                ->color('danger'),
            Stat::make('Tepat Waktu', $tepat_waktu)
                ->description(now()->translatedFormat('F Y'))
                ->color('info')
                ->icon('heroicon-o-clock'),
            Stat::make('Persentasi Kehadiran', $persen_hadir." %")
                ->descriptionIcon($pk_icon)
                ->description($pk_desc)
                ->color($pk_color),
            Stat::make('Persentasi Tepat Waktu', $persen_tepat." %")
                ->descriptionIcon($pt_icon)
                ->description($pt_desc)
                ->color($pt_color)
        ];
    }
    protected function getHeading(): string
    {
        return '📊 Rekap Absensi Bulanan';
    }
    protected function getDescription(): ?string
    {
        return 'Statistik absensi pegawai bulan ini';
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
