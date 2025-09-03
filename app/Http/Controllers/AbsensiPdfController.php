<?php

namespace App\Http\Controllers;

use PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Nagari;
use App\Models\WdmsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\RequestException;

class AbsensiPdfController extends Controller
{
    public function index($bulan, $tahun)
    {
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // $holiday_api = Http::get('https://hari-libur-api.vercel.app/api')->json();
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
            ->pluck('event_date', 'event_name')
            ->toArray();

        // Generate semua tanggal dalam bulan
        $datesInMonth = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Hanya tambahkan hari kerja (Senin-Jumat)
            if (!$currentDate->isWeekend()) {
                $datesInMonth[] = $currentDate->format('Y-m-d');
            }
            $currentDate->addDay();
        }

        // Ambil data absensi
        $sn = auth()->user()->nagari->id;
        $users = User::with(['RekapAbsensiPegawai' => function ($query) use ($startDate, $endDate, $sn) {
            $query->whereBetween('date', [$startDate, $endDate])
                ->where('nagari_id', $sn)
                ->orderBy('date');
        }])->get()->except(1);
        // Format data absensi per user per tanggal
        $attendanceData = $users->map(function ($user) use ($datesInMonth, $holidays) {
            $userAttendances = $user->RekapAbsensiPegawai->groupBy(function ($item) {
                return $item->date;
            });
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

            // Hitung total kehadiran dan keterlambatan
            $stats = collect($dailyAttendance)->reduce(function ($carry, $item) {
                if (!$item['is_holiday']) {

                    if ($item['masuk'] === 'A') { // Hitung jika tidak absent
                        $carry['total_tidak_hadir']++;
                    }
                    if ($item['masuk'] != 'A' || $item['pulang'] != '-') {
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

                // ... (data lainnya)
            ];
        });
        // return view('pdf.absensi', [
        //     'datesInMonth' => $datesInMonth,
        //     'holidays' => $holidays,
        //     'attendanceData' => $attendanceData,
        //     'bulan' => $bulan,
        //     'tahun' => $tahun,
        //     'monthName' => Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y')
        // ]);


        $pdf = PDF::loadView('pdf.absensi', [
            'datesInMonth' => $datesInMonth,
            'holidays' => $holidays,
            'attendanceData' => $attendanceData,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'monthName' => Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y')
        ])->setPaper('a4', 'landscape');
        $filename = "absensi-{$bulan}-{$tahun}.pdf";
        $pdf->save(public_path("absensi/{$filename}"));
        // return $pdf->stream();
        return $pdf->download("absensi-{$bulan}-{$tahun}.pdf");
    }
}
