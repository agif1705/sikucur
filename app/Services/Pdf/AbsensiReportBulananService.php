<?php

namespace App\Services\Pdf;

use PDF;
use App\Models\User;
use App\Models\Nagari;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Service untuk generate laporan absensi bulanan dalam format PDF
 * Mengambil data absensi pegawai per nagari dan menghasilkan PDF yang siap download
 */

class AbsensiReportBulananService
{
    /**
     * Generate laporan absensi bulanan dalam format PDF
     *
     * @param int $tahun Tahun laporan
     * @param int $bulan Bulan laporan (1-12)
     * @param int $nagariId ID Nagari
     * @return array Array berisi path, filename, dan PDF object
     * @throws \Exception Jika terjadi error dalam proses generate
     */
    public function generate(int $tahun, int $bulan, int $nagariId)
    {
        try {
            // Validasi input
            $this->validateInput($tahun, $bulan, $nagariId);

            // Setup tanggal awal dan akhir bulan
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $endDate   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

            // Ambil hari libur nasional dengan caching dan error handling
            $holidays = $this->getNationalHolidays($tahun, $bulan);

            // Generate daftar tanggal kerja dalam bulan (exclude weekend)
            $datesInMonth = $this->getWorkingDatesInMonth($startDate, $endDate);

            // Ambil data absensi pegawai sesuai nagari
            $users = $this->getUsersWithAttendance($startDate, $endDate, $nagariId);

            // Format data absensi per user
            $attendanceData = $this->formatAttendanceData($users, $datesInMonth, $holidays);

            // Generate PDF
            return $this->generatePDF($attendanceData, $datesInMonth, $holidays, $bulan, $tahun);

        } catch (\Exception $e) {
            Log::error('Error generating attendance report: ' . $e->getMessage(), [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'nagariId' => $nagariId
            ]);
            throw $e;
        }
    }

    /**
     * Validasi input parameter
     */
    private function validateInput(int $tahun, int $bulan, int $nagariId): void
    {
        if ($bulan < 1 || $bulan > 12) {
            throw new \InvalidArgumentException('Bulan harus antara 1-12');
        }

        if ($tahun < 2020 || $tahun > now()->year + 1) {
            throw new \InvalidArgumentException('Tahun tidak valid');
        }

        if ($nagariId <= 0) {
            throw new \InvalidArgumentException('Nagari ID tidak valid');
        }

        // Validasi nagari exists
        if (!Nagari::find($nagariId)) {
            throw new \InvalidArgumentException('Nagari tidak ditemukan');
        }
    }

    /**
     * Ambil daftar hari libur nasional dengan caching
     */
    private function getNationalHolidays(int $tahun, int $bulan): array
    {
        $cacheKey = "national_holidays_{$tahun}_{$bulan}";

        $holiday_api = Cache::remember($cacheKey, now()->addDay(), function () {
            try {
                $response = Http::retry(3, 500, function ($exception) {
                    return $exception->getCode() >= 500 || $exception->getCode() === 0;
                })
                ->timeout(10)
                ->get('https://hari-libur-api.vercel.app/api');

                if ($response->successful()) {
                    return $response->json();
                }

                Log::warning('Holiday API returned non-successful response', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];

            } catch (\Exception $e) {
                Log::error('Failed to fetch holidays from API: ' . $e->getMessage());
                return [];
            }
        });

        // Filter hari libur nasional sesuai bulan dan tahun
        return collect($holiday_api)
            ->where('is_national_holiday', true)
            ->filter(function ($event) use ($bulan, $tahun) {
                try {
                    $eventDate = Carbon::parse($event['event_date']);
                    return $eventDate->month == $bulan &&
                           $eventDate->year == $tahun &&
                           !$eventDate->isWeekend();
                } catch (\Exception $e) {
                    Log::warning('Invalid event date format', ['event' => $event]);
                    return false;
                }
            })
            ->pluck('event_date', 'event_name')
            ->toArray();
    }

    /**
     * Generate daftar tanggal kerja dalam bulan (exclude weekend)
     */
    private function getWorkingDatesInMonth(Carbon $startDate, Carbon $endDate): array
    {
        $dates = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            if (!$current->isWeekend()) {
                $dates[] = $current->format('Y-m-d');
            }
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Ambil data users dengan relasi absensi
     */
    private function getUsersWithAttendance(Carbon $startDate, Carbon $endDate, int $nagariId)
    {
        return User::with(['RekapAbsensiPegawai' => function ($query) use ($startDate, $endDate, $nagariId) {
            $query->whereBetween('date', [$startDate, $endDate])
                ->where('nagari_id', $nagariId)
                ->orderBy('date');
        }, 'nagari', 'jabatan'])
        ->where('nagari_id', $nagariId)
        ->where('id', '!=', 1) // Exclude super admin
        ->orderBy('name')
        ->get();
    }

    /**
     * Format data absensi per user
     */
    private function formatAttendanceData($users, array $datesInMonth, array $holidays): \Illuminate\Support\Collection
    {
        return $users->map(function ($user) use ($datesInMonth, $holidays) {
            $userAttendances = $user->RekapAbsensiPegawai->groupBy('date');
            $dailyAttendance = [];
            $stats = [
                'total_present' => 0,
                'total_late' => 0,
                'total_tidak_hadir' => 0,
                'total_hari_kerja' => count($datesInMonth) - count($holidays)
            ];

            foreach ($datesInMonth as $date) {
                $isHoliday = in_array($date, $holidays);

                if ($isHoliday) {
                    $dailyAttendance[$date] = [
                        'masuk' => 'L',
                        'pulang' => 'L',
                        'is_holiday' => true,
                        'is_late' => false
                    ];
                } else {
                    $attendanceInfo = $this->processUserDailyAttendance($userAttendances[$date] ?? collect());
                    $dailyAttendance[$date] = $attendanceInfo;

                    // Update statistics
                    if ($attendanceInfo['masuk'] !== 'A') {
                        $stats['total_present']++;
                    } else {
                        $stats['total_tidak_hadir']++;
                    }

                    if ($attendanceInfo['is_late']) {
                        $stats['total_late']++;
                    }
                }
            }

            return [
                'user' => $user,
                'attendances' => $dailyAttendance,
                'stats' => $stats
            ];
        });
    }

    /**
     * Process absensi harian per user
     */
    private function processUserDailyAttendance($attendances): array
    {
        $masuk = $attendances
            ->filter(fn($item) => $item->resource === 'Fingerprint' && $item->time_in)
            ->min('time_in');

        $pulang = $attendances
            ->filter(fn($item) => $item->resource === 'Fingerprint' && $item->time_out)
            ->max('time_out');

        $isLate = false;
        if ($masuk) {
            $masukFormatted = Carbon::parse($masuk)->format('H:i');
            $isLate = Carbon::parse($masuk)->format('H:i') > '08:00';
        } else {
            $masukFormatted = 'A';
        }

        $pulangFormatted = $pulang ? Carbon::parse($pulang)->format('H:i') : ($masukFormatted === 'A' ? 'A' : '-');

        return [
            'masuk' => $masukFormatted,
            'pulang' => $pulangFormatted,
            'is_holiday' => false,
            'is_late' => $isLate
        ];
    }

    /**
     * Generate PDF dari data yang telah diformat
     */
    private function generatePDF($attendanceData, array $datesInMonth, array $holidays, int $bulan, int $tahun): array
    {
        try {
            $pdf = PDF::loadView('pdf.absensi-pegawai', [
                'datesInMonth'   => $datesInMonth,
                'holidays'       => $holidays,
                'attendanceData' => $attendanceData,
                'bulan'          => $bulan,
                'tahun'          => $tahun,
                'monthName'      => Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y'),
            ])->setPaper('a4', 'landscape');

            $filename = "Laporan_Absensi_{$bulan}_{$tahun}.pdf";

            // Simpan ke storage jika diperlukan (optional)
            // $path = "public/absensi/{$filename}";
            // Storage::put($path, $pdf->output());

            return [
                'filename' => $filename,
                'pdf' => $pdf,
                'success' => true
            ];

        } catch (\Exception $e) {
            Log::error('Error creating PDF: ' . $e->getMessage());
            throw new \Exception('Gagal membuat file PDF: ' . $e->getMessage());
        }
    }
}
