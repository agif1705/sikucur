<?php

namespace App\Services\Pdf;

use App\Model            Log::info('Dates and holidays prepared', [
                'total_dates' => count($allDatesInMonth),
                'working_dates_only' => count($workingDatesOnly),
                'total_holidays' => count($holidays)
            ]);

            // Ambil data users dengan attendance
            $users = $this->getUsersWithAttendance($startDate, $endDate, $nagariId);

            Log::info('Users fetched with attendance data', [
                'user_count' => $users->count(),
                'user_names' => $users->pluck('name')->toArray()
            ]);App\Models\Nagari;
use PDF;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Service untuk generate laporan absensi bulanan dalam format PDF
 */
class AbsensiReportBulananService
{
    /**
     * Generate laporan absensi bulanan dalam format PDF
     */
    public function generate(int $tahun, int $bulan, int $nagariId)
    {
        try {
            Log::info('Starting PDF generation in service', [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'nagariId' => $nagariId
            ]);

            // Validasi input
            $this->validateInput($tahun, $bulan, $nagariId);

            // Buat tanggal periode
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            Log::info('Date range created', [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString()
            ]);

            // Ambil data hari libur dengan nama
            $holidays = $this->getNationalHolidays($tahun, $bulan);

            // Generate semua tanggal dalam bulan
            $allDatesInMonth = $this->getAllDatesInMonth($startDate, $endDate);

            // Filter hanya hari kerja untuk ditampilkan di PDF
            $workingDatesOnly = $this->getWorkingDatesOnly($allDatesInMonth, $nagariId);

            Log::info('Dates and holidays prepared', [
                'total_dates' => count($allDatesInMonth),
                'working_dates_only' => count($workingDatesOnly),
                'total_holidays' => count($holidays)
            ]);

            // Ambil data users dengan attendance
            $users = $this->getUsersWithAttendance($startDate, $endDate, $nagariId);

            Log::info('Users data retrieved', [
                'total_users' => $users->count(),
                'user_names' => $users->pluck('name')->toArray()
            ]);

            if ($users->isEmpty()) {
                Log::warning('No users found for PDF generation');

                // Buat PDF kosong dengan pesan
                $emptyHtml = $this->generateEmptyPdfHtml($tahun, $bulan, $nagariId);
                $pdf = PDF::loadHTML($emptyHtml);

                return [
                    'pdf' => $pdf,
                    'data' => [],
                    'message' => 'Tidak ada data pegawai untuk periode ini'
                ];
            }

            // Format data attendance (hanya untuk hari kerja yang ditampilkan)
            $attendanceData = $this->formatAttendanceData($users, $workingDatesOnly, $holidays);

            Log::info('Attendance data formatted', [
                'total_records' => $attendanceData->count()
            ]);

            // Generate PDF
            return $this->generatePDF($attendanceData, $workingDatesOnly, $holidays, $bulan, $tahun, $nagariId);

        } catch (\Exception $e) {
            Log::error('Error in AbsensiReportBulananService::generate', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return error PDF
            $errorHtml = $this->generateErrorPdfHtml($e->getMessage());
            $pdf = PDF::loadHTML($errorHtml);

            return [
                'pdf' => $pdf,
                'data' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate HTML untuk PDF kosong
     */
    private function generateEmptyPdfHtml(int $tahun, int $bulan, int $nagariId): string
    {
        $nagari = \App\Models\Nagari::find($nagariId);
        $bulanName = Carbon::create($tahun, $bulan)->locale('id')->monthName;

        return '
        <html>
        <head>
            <title>Laporan Absensi - Tidak Ada Data</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .message { text-align: center; color: #666; font-size: 18px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN ABSENSI PEGAWAI</h1>
                <h2>' . ($nagari ? $nagari->name : 'Unknown Nagari') . '</h2>
                <h3>Periode: ' . $bulanName . ' ' . $tahun . '</h3>
            </div>
            <div class="message">
                <p><strong>Tidak ada data pegawai untuk periode ini</strong></p>
                <p>Tanggal Generate: ' . now()->format('d/m/Y H:i:s') . '</p>
            </div>
        </body>
        </html>';
    }

    /**
     * Generate HTML untuk error PDF
     */
    private function generateErrorPdfHtml(string $errorMessage): string
    {
        return '
        <html>
        <head>
            <title>Error - Laporan Absensi</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .error { color: red; text-align: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>ERROR - LAPORAN ABSENSI</h1>
            </div>
            <div class="error">
                <p><strong>Terjadi kesalahan:</strong></p>
                <p>' . htmlspecialchars($errorMessage) . '</p>
                <p>Tanggal: ' . now()->format('d/m/Y H:i:s') . '</p>
            </div>
        </body>
        </html>';
    }

    /**
     * Validasi input parameters
     */
    private function validateInput(int $tahun, int $bulan, int $nagariId): void
    {
        if ($bulan < 1 || $bulan > 12) {
            throw new \InvalidArgumentException('Bulan harus antara 1-12');
        }

        if ($tahun < 2020 || $tahun > now()->year + 1) {
            throw new \InvalidArgumentException('Tahun tidak valid');
        }

        $nagari = Nagari::find($nagariId);
        if (!$nagari) {
            throw new \InvalidArgumentException('Nagari tidak ditemukan');
        }

        Log::info('Input validation passed', [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'nagari' => $nagari->name
        ]);
    }

    /**
     * Ambil data users dengan relasi absensi
     */
    private function getUsersWithAttendance(Carbon $startDate, Carbon $endDate, int $nagariId)
    {
        Log::info('Fetching users with attendance', [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'nagari_id' => $nagariId
        ]);

        $query = User::query()
            ->where('nagari_id', $nagariId)
            ->where('id', '!=', 1) // Exclude super admin
            ->with([
                'rekapAbsensiPegawai' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate])
                          ->orderBy('date');
                },
                'nagari.workDays',
                'jabatan'
            ])
            ->orderBy('name');

        // Filter berdasarkan role user yang sedang login
        if (Auth::check()) {
            $user = Auth::user();
            if (!($user->hasRole('super_admin') || $user->hasRole('Kaur Umum dan Perencanan'))) {
                $query->where('id', $user->id);
            }
        }

        $users = $query->get();

        Log::info('Users fetched', [
            'count' => $users->count(),
            'users' => $users->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'attendance_count' => $u->rekapAbsensiPegawai->count()
            ])->toArray()
        ]);

        return $users;
    }

    /**
     * Format data absensi per user
     */
    private function formatAttendanceData($users, array $datesInMonth, array $holidays): \Illuminate\Support\Collection
    {
        $today = now()->toDateString();

        return $users->map(function ($user) use ($datesInMonth, $holidays, $today) {
            $userAttendances = $user->RekapAbsensiPegawai->groupBy('date');
            $dailyAttendance = [];
            $workingDaysCount = 0;
            $stats = [
                'total_present' => 0,
                'total_late' => 0,
                'total_tidak_hadir' => 0,
                'total_hari_kerja' => 0  // Will be calculated below
            ];

            foreach ($datesInMonth as $date) {
                $isHoliday = isset($holidays[$date]);
                $isFutureDate = $date > $today;

                if ($isHoliday) {
                    // Hari libur nasional - tandai H dengan nama libur
                    $dailyAttendance[$date] = [
                        'masuk' => 'H',
                        'pulang' => 'H',
                        'is_holiday' => true,
                        'is_late' => false,
                        'is_working_day' => true,
                        'holiday_name' => $holidays[$date]['name']
                    ];
                } elseif ($isFutureDate) {
                    // Tanggal masa depan - tandai dengan "-"
                    $dailyAttendance[$date] = [
                        'masuk' => '-',
                        'pulang' => '-',
                        'is_holiday' => false,
                        'is_late' => false,
                        'is_working_day' => true,
                        'is_future' => true
                    ];
                } else {
                    // Hari kerja normal
                    $workingDaysCount++;
                    $attendanceInfo = $this->processUserDailyAttendance($userAttendances[$date] ?? collect());
                    $attendanceInfo['is_working_day'] = true;
                    $attendanceInfo['is_holiday'] = false;
                    $dailyAttendance[$date] = $attendanceInfo;

                    // Update statistics hanya untuk hari kerja yang sudah lewat
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

            // Set total hari kerja setelah loop (tidak termasuk holidays dan future dates)
            $stats['total_hari_kerja'] = $workingDaysCount;            return [
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
     * Filter hanya tanggal hari kerja untuk ditampilkan di PDF
     */
    private function getWorkingDatesOnly(array $allDates, int $nagariId): array
    {
        $nagari = \App\Models\Nagari::with('workDays')->find($nagariId);
        $workingDates = [];

        foreach ($allDates as $date) {
            if ($this->isWorkingDay($date, $nagari)) {
                $workingDates[] = $date;
            }
        }

        Log::info('Filtered working dates', [
            'total_dates' => count($allDates),
            'working_dates' => count($workingDates),
            'filtered_out' => count($allDates) - count($workingDates)
        ]);

        return $workingDates;
    }

    /**
     * Ambil semua tanggal dalam bulan (termasuk weekend)
     */
    private function getAllDatesInMonth(Carbon $startDate, Carbon $endDate): array
    {
        $dates = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dates[] = $current->toDateString();
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Cek apakah tanggal adalah hari kerja berdasarkan work_days nagari
     */
    private function isWorkingDay($date, $nagari): bool
    {
        $dayName = strtolower(Carbon::parse($date)->format('l')); // monday, tuesday, etc

        $workDay = $nagari->workDays->where('day', $dayName)->first();

        // Jika tidak ada setting work_day, default weekdays = kerja, weekend = libur
        if (!$workDay) {
            $isWorking = !in_array($dayName, ['saturday', 'sunday']);
            Log::debug('No work_day setting found', [
                'date' => $date,
                'day_name' => $dayName,
                'default_is_working' => $isWorking
            ]);
            return $isWorking;
        }

        Log::debug('Work day check', [
            'date' => $date,
            'day_name' => $dayName,
            'is_working_day' => $workDay->is_working_day
        ]);

        return $workDay->is_working_day;
    }

    /**
     * Ambil hari libur nasional dengan nama dari API
     */
    private function getNationalHolidays(int $tahun, int $bulan): array
    {
        try {
            $cacheKey = "national_holidays_{$tahun}_{$bulan}";

            return Cache::remember($cacheKey, 3600, function () use ($tahun, $bulan) {
                $response = Http::timeout(10)->get('https://api-harilibur.vercel.app/api', [
                    'year' => $tahun,
                    'month' => $bulan
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $holidays = [];

                    foreach ($data as $holiday) {
                        $holidays[$holiday['holiday_date']] = [
                            'name' => $holiday['holiday_name'],
                            'date' => $holiday['holiday_date']
                        ];
                    }

                    Log::info('Holidays fetched from API', [
                        'count' => count($holidays),
                        'holidays' => array_keys($holidays)
                    ]);

                    return $holidays;
                }

                Log::warning('Failed to fetch holidays from API', [
                    'status' => $response->status(),
                    'year' => $tahun,
                    'month' => $bulan
                ]);

                return [];
            });
        } catch (\Exception $e) {
            Log::error('Error fetching national holidays', [
                'error' => $e->getMessage(),
                'year' => $tahun,
                'month' => $bulan
            ]);
            return [];
        }
    }

    /**
     * Generate PDF dari data yang telah diformat
     */
    private function generatePDF($attendanceData, array $datesInMonth, array $holidays, int $bulan, int $tahun, int $nagariId): array
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

            $filename = "absensi-pegawai-{$bulan}-{$tahun}.pdf";

            // Buat direktori jika belum ada
            $directory = 'private/public/absensi/';
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
                Log::info('Created directory: ' . $directory);
            }

            // Path untuk menyimpan file
            $storagePath = "{$directory}/{$filename}";
            $fullPath = storage_path("app/{$storagePath}");

            // Simpan PDF ke storage
            $pdfContent = $pdf->output();
            Storage::put($storagePath, $pdfContent);

            // Verifikasi file tersimpan
            if (Storage::exists($storagePath)) {
                Log::info('PDF file saved successfully', [
                    'filename' => $filename,
                    'path' => $fullPath,
                    'size' => Storage::size($storagePath) . ' bytes'
                ]);
            } else {
                Log::error('PDF file failed to save', [
                    'filename' => $filename,
                    'path' => $fullPath
                ]);
            }

            return [
                'path' => $storagePath,
                'full_path' => $fullPath,
                'filename' => $filename,
                'pdf' => $pdf,
                'success' => true
            ];

        } catch (\Exception $e) {
            Log::error('Error creating PDF: ' . $e->getMessage(), [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Gagal membuat file PDF: ' . $e->getMessage());
        }
    }
}
