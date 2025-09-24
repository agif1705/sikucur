<?php

namespace App\Services\Pdf;

use PDF;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
        $users = User::with([
            'RekapAbsensiPegawai' => function ($query) use ($startDate, $endDate, $nagariId) {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->where('nagari_id', $nagariId)
                    ->orderBy('date');
            },
            'absensiWebPegawai' => function ($query) use ($startDate, $endDate, $nagariId) {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->where('nagari_id', $nagariId)
                    ->orderBy('date');
            }
        ])->get()->except([1,2]);

        // Buat mapping AbsensiWebPegawai berdasarkan ID untuk lookup cepat
        $absensiWebMap = [];
        foreach ($users as $user) {
            foreach ($user->absensiWebPegawai as $webAbsensi) {
                $absensiWebMap[$webAbsensi->id] = $webAbsensi;
            }
        }

        // Array untuk menyimpan detail keterangan untuk halaman 2
        $detailKeterangan = [];

        // Format per user
        $attendanceData = $users->map(function ($user) use ($datesInMonth, $holidays, &$detailKeterangan, $absensiWebMap) {
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
                        'is_late' => false,
                        'status_absensi' => 'L'
                    ];
                } else {
                    $attendances = $userAttendances[$date] ?? collect();
                    $attendance = $attendances->first(); // Ambil record pertama untuk tanggal ini

                    $masuk = null;
                    $pulang = null;
                    $is_late = false;
                    $statusAbsensi = 'A'; // Default alpha
                    $alasanKeterangan = null;

                    if ($attendance) {
                        $statusAbsensi = $attendance->status_absensi;

                        // Cek apakah id_resource berformat 'web-{id}' untuk ambil alasan
                        if ($attendance->id_resource && str_starts_with($attendance->id_resource, 'web-')) {
                            $webId = (int) str_replace('web-', '', $attendance->id_resource);
                            if (isset($absensiWebMap[$webId])) {
                                $alasanKeterangan = $absensiWebMap[$webId]->alasan;
                            }
                        }

                        // Kumpulkan detail untuk halaman 2 jika bukan 'Hadir'
                        if (in_array($statusAbsensi, ['HDLD', 'HDDD', 'S', 'C', 'I'])) {
                            $detailKeterangan[] = [
                                'user_name' => $user->name,
                                'date' => $date,
                                'status_absensi' => $statusAbsensi,
                                'time_in' => $attendance->time_in,
                                'time_out' => $attendance->time_out,
                                'is_late' => $attendance->is_late,
                                'resource' => $attendance->resource ?? 'Manual',
                                'alasan' => $alasanKeterangan, // Tambahkan alasan dari AbsensiWebPegawai
                                'id_resource' => $attendance->id_resource
                            ];
                        }

                        if ($attendance->time_in) {
                            $masuk = Carbon::parse($attendance->time_in)->format('H:i');
                            $total_masuk++;
                            $is_late = $attendance->is_late ?? false;
                        }

                        if ($attendance->time_out) {
                            $pulang = Carbon::parse($attendance->time_out)->format('H:i');
                        }
                    }

                    $total_hari_kerja++;

                    // Tentukan tampilan berdasarkan status_absensi
                    $displayMasuk = match($statusAbsensi) {
                        'Hadir' => $masuk ?? 'H',
                        'HDLD' => 'HDLD',
                        'HDDD' => 'HDDD',
                        'S' => 'S',
                        'C' => 'C',
                        'I' => 'I',
                        default => 'A'
                    };

                    $displayPulang = match($statusAbsensi) {
                        'Hadir' => $pulang ?? '-',
                        'HDLD' => 'HDLD',
                        'HDDD' => 'HDDD',
                        'S' => 'S',
                        'C' => 'C',
                        'I' => 'I',
                        default => 'A'
                    };

                    $dailyAttendance[$date] = [
                        'masuk' => $displayMasuk,
                        'pulang' => $displayPulang,
                        'is_holiday' => false,
                        'is_late' => $is_late,
                        'total_masuk' => $total_masuk,
                        'total_hari_kerja' => $total_hari_kerja,
                        'status_absensi' => $statusAbsensi
                    ];
                }
            }            // Hitung total
            $stats = collect($dailyAttendance)->reduce(function ($carry, $item) {
                if (!$item['is_holiday']) {
                    // Hitung kehadiran berdasarkan status_absensi
                    if (in_array($item['status_absensi'], ['Hadir', 'HDLD', 'HDDD'])) {
                        $carry['total_present']++;
                    }
                    if ($item['status_absensi'] === 'A') {
                        $carry['total_tidak_hadir']++;
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
            'detailKeterangan' => $detailKeterangan,
            'bulan'          => $bulan,
            'tahun'          => $tahun,
            'monthName'      => Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y'),
        ])->setPaper('a4', 'landscape');
        $nagari_name = Auth::user()->nagari->name;
        $filename = "Laporan_Absensi_{$nagari_name}_{$bulan}_{$tahun}.pdf";
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
