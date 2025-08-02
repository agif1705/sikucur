<?php

namespace App\Filament\Resources\AbsensiPegawaiResource\Widgets;

use App\Models\AbsensiPegawai;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\RequestException;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class AbsensiHariLibur extends BaseWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 6,
    ];

    protected static ?string $height = '200px';
    protected function getStats(): array
    {
        $bulan = Carbon::now()->month;
        // $bulan = 6;
        $today = Carbon::today();
        $tahun = Carbon::now()->year;
        $total_hari_kerja = $this->getWorkingDaysThisMonth($bulan, $tahun);
        $total_hadir = AbsensiPegawai::whereUserId(auth()->user()->id)->whereIn('absensi', ['Hadir', 'Hadir Dinas Dalam Daerah', 'Hadir Dinas Luar Daerah'])->count();
        $total_terlambat = AbsensiPegawai::whereUserId(auth()->user()->id)->where('status_absensi', 'terlambat')->count();

        $holiday_api = Cache::remember('national_holidays_' . $tahun . '_' . $bulan, now()->addDay(), function () {
            try {
                return Http::retry(3, 500, function ($exception) {
                    // Hanya retry untuk error koneksi atau server error
                    return $exception instanceof RequestException &&
                        ($exception->getCode() >= 500 || $exception->getCode() === 0);
                })
                    ->timeout(10) // Timeout 10 detik
                    ->get('https://hari-libur-api.vercel.app/api')
                    ->throw() // Throw exception untuk 4xx/5xx
                    ->json();
            } catch (\Exception $e) {
                // Log error dan return array kosong
                Log::error('Failed to fetch holidays: ' . $e->getMessage());
                return [];
            }
        });
        $holidays = collect($holiday_api)
            ->where('is_national_holiday', true)
            ->filter(function ($event) use ($bulan, $tahun) {
                $eventDate = Carbon::parse($event['event_date']);
                return $eventDate->month == $bulan &&
                    $eventDate->year == $tahun &&
                    !$eventDate->isWeekend();
            })
            ->pluck('event_date', 'event_name');
        $persentase_terlambat = $total_hadir > 0
            ? round(($total_terlambat / $total_hadir) * 100, 2)
            : 0;
        $total_alpha = max(0, ($total_hari_kerja - $holidays->count() - $total_hadir));

        return [
            Stat::make('Total Hari Kerja', $total_hari_kerja - $holidays->count())
                ->description('Dihitung sampai tanggal ' . $today->format('d/m/Y')),
            Stat::make('Total Hadir', $total_hadir)
                ->description('Dihitung sampai tanggal ' . $today->format('d/m/Y')),
            Stat::make('Total Alpha', $total_alpha)->description('Dihitung sampai tanggal ' . $today->format('d/m/Y')),
            Stat::make('Total Hari Libur', $holidays->count()),
            Stat::make('Total Terlambat', $total_terlambat)
                ->description('Persentasi Terlambat ' . $persentase_terlambat . '% dari kehadiran')
                ->color($persentase_terlambat > 80 ? 'danger' : 'success'),
            Stat::make(
                'Persentase Kehadiran',
                $total_hari_kerja > 0
                    ? round(($total_hadir / ($total_hari_kerja - $holidays->count())) * 100, 2) . '%'
                    : '0%'
            ),
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
