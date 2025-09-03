<?php

namespace App\Services\Pdf;

use PDF;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AbsensiReportBulananService
{
    public function generate(int $tahun, int $bulan, int $nagariId)
    {
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Ambil hari libur dari API (cache 1 hari)
        $holiday_api = Cache::remember("national_holidays_{$tahun}_{$bulan}", now()->addDay(), function () {
            try {
                return Http::retry(3, 500, function ($exception) {
                    return $exception->getCode() >= 500 || $exception->getCode() === 0;
                })
                    ->timeout(10)
                    ->get('https://hari-libur-api.vercel.app/api')
                    ->throw()
                    ->json();
            } catch (\Exception $e) {
                Log::error('Failed to fetch holidays: ' . $e->getMessage());
                return [];
            }
        });

        // Filter hanya hari libur nasional di bulan & tahun yg diminta
        $holidays = collect($holiday_api)
            ->where('is_national_holiday', true)
            ->filter(function ($event) use ($bulan, $tahun) {
                $eventDate = Carbon::parse($event['event_date']);
                return $eventDate->month == $bulan &&
                       $eventDate->year == $tahun &&
                       !$eventDate->isWeekend();
            })
            ->pluck('event_date', 'event_name')
            ->toArray();

        // Generate semua tanggal kerja dalam bulan
        $datesInMonth = [];
        $currentDate  = $startDate->copy();
        while ($currentDate <= $endDate) {
            if (!$currentDate->isWeekend()) {
                $datesInMonth[] = $currentDate->format('Y-m-d');
            }
            $currentDate->addDay();
        }

        // Ambil absensi sesuai nagari user yang login
        $users = User::with(['RekapAbsensiPegawai' => function ($query) use ($startDate, $endDate, $nagariId) {
            $query->whereBetween('date', [$startDate, $endDate])
                ->where('nagari_id', $nagariId)
                ->orderBy('date');
        }])->get()->except(1);

        // Format per user
        $attendanceData = $users->map(function ($user) use ($datesInMonth, $holidays) {
            $userAttendances = $user->RekapAbsensiPegawai->groupBy('date');
            $dailyAttendance = [];
            $total_hari_kerja = 0;
            $total_masuk = 0;

            foreach ($datesInMonth as $date) {
                $dateObj = Carbon::parse($date);
                $isHoliday = in_array($date, $holidays);
                if ($isHoliday) {
                    $dailyAttendance[$date] = [
                        'masuk' => 'L',
                        'pulang' => 'L',
                        'is_holiday' => true,
                        'is_late' => false
                    ];
                } else {
                    $attendances = $userAttendances[$date] ?? collect();
                    $masuk = $attendances->map(function ($item) {
                        if ($item->resource === 'Fingerprint') {
                            return Carbon::parse($item->time_in)->format('H:i'); // selalu string jam:menit
                        } else {
                            return $item->status_absensi; // langsung status absensi
                        }
                    })->first();
                    $is_late = $attendances->filter(function ($item) {
                        return $item->is_late;
                    })->first();
                    if ($masuk) {
                        $pulang = $attendances->map(function ($item) {
                            if ($item->resource === 'Fingerprint') {
                                return Carbon::parse($item->time_out)->format('H:i'); // selalu string jam:menit
                            } else {
                                return $item->status_absensi;
                            }
                        })->first();
                        $total_masuk++;
                    } else {
                        $pulang = null;
                    }
                    $total_hari_kerja++;

                    $dailyAttendance[$date] = [
                        'masuk'      => $masuk ?? 'A',
                        'pulang' => $pulang ?? 'A',
                        'is_holiday' => false,
                        'is_late' => $is_late ?? false,
                        'total_masuk' => $total_masuk,
                        'total_hari_kerja' => $total_hari_kerja,
                    ];
                }
            }

            // Hitung total
            $stats = collect($dailyAttendance)->reduce(function ($carry, $item) {
                if (!$item['is_holiday']) {
                    if ($item['masuk'] === 'A') {
                        $carry['total_tidak_hadir']++;
                    }
                    if ($item['masuk'] != 'A') {
                        $carry['total_present']++;
                    }
                    if ($item['is_late']) {
                        $carry['total_late']++;
                    }
                }
                return $carry;
            }, [
                'total_present' => 0,
                'total_late' => 0,
                'total_hari_kerja' => 0,
                'total_tidak_hadir' => 0,
                'persen_hadir' => 0,
            ]);

            return [
                'user' => $user,
                'attendances' => $dailyAttendance,
                'total_present' => $stats['total_present'],
                'total_late' => $stats['total_late'],
                'total_tidak_hadir' => $stats['total_tidak_hadir'],
            ];
        });

        // Buat PDF
        $pdf = PDF::loadView('pdf.absensi-pegawai', [
            'datesInMonth'   => $datesInMonth,
            'holidays'       => $holidays,
            'attendanceData' => $attendanceData,
            'bulan'          => $bulan,
            'tahun'          => $tahun,
            'monthName'      => Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y'),
        ])->setPaper('a4', 'landscape');
        $filename = "absensi-pegawai-{$bulan}-{$tahun}.pdf";
        $path = "public/absensi/{$filename}";

        Storage::put($path, $pdf->output());
        $lokasi = "app/private/public/absensi/{$filename}";
        return [
            'path' => $lokasi,
            'filename' => $filename,
            'pdf' => $pdf,
        ];
    }
}
