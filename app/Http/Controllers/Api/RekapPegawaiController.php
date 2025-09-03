<?php

namespace App\Http\Controllers\Api;

use PDF;
use App\Models\User;
use App\Models\Nagari;
use Illuminate\Http\Request;
use App\Services\WahaService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\RekapAbsensiPegawai;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\RequestException;

class RekapPegawaiController extends Controller
{
    public function webhook(Request $request)
    {
        // ini ada perubahan di iclock_transaction dan mengirimkan wa ke User
        $data = $request->validate([
            'sn_mesin'  => 'required|string|max:255',
            'emp_id'    => 'required',
            'emp_code'  => 'required',
            'punch_time' => 'required', // format string
        ]);

        $date = Carbon::parse($data['punch_time'])->format('Y-m-d');
        $time_in = Carbon::parse($data['punch_time'])->format('H:i:s');
        $is_late = $time_in > '08:00:00';

        // Tentukan emp_id yang dipakai
        $emp_id = $data['emp_id'] ?? intval($data['emp_code']);

        // Cari nagari dan user
        $nagari_id = Nagari::where('sn_fingerprint', $data['sn_mesin'])->first()?->id;
        $user = User::whereEmpId($emp_id)->first();

        if (!$user || !$nagari_id) {
            return response()->json(['message' => 'User atau mesin tidak ditemukan'], 404);
        }
        // Cek absensi hari ini
        $absensi = RekapAbsensiPegawai::where('sn_mesin', $data['sn_mesin'])
            ->whereUserId($user->id)
            ->whereDate('date', $date)
            ->first();
        $pesan = "Hai *" . $user->name . "* , Anda *" . $is_late . '* anda telah hadir pada jam *' . carbon::parse($data['punch_time'])->format('H:i') . '* menggunakan fingerprint di *Nagari ' . $user->nagari->name .
            '* ,ini akan masuk ke WhatsApp Wali Nagari ' . $user->nagari->name . " *Sebelum Jam: 10:05 Siang* terima kasih \n   ketik : *info* -> untuk melihat informasi perintah dan bantuan lebih lanjut. \n \n \n \n _Sent || via *Cv.Baduo Mitra Solustion*_";

        if (!$absensi) {
            // Absensi pertama (masuk)
            $absensi = RekapAbsensiPegawai::create([
                'user_id'        => $user->id,
                'nagari_id'      => $nagari_id,
                'is_late'        => $is_late,
                'sn_mesin'       => $data['sn_mesin'],
                'status_absensi' => 'Hadir',
                'resource'       => 'Fingerprint',
                'time_in'        => $time_in,
                'date'           => $date,
            ]);
            if ($user->aktif == true) {
                $wa = new WahaService();
                $result = $wa->sendText($user->no_hp, $pesan);
            }

            return response()->json([
                'message'      => 'Absensi masuk tercatat',
                'user_id'      => $user->emp_code,
                'time_in'      => $time_in,
                'date'         => $date,
                'absensi_type' => 'IN',
                'dataSender'   => 'Fingerprint'
            ], 200);
        } else {
            // Absensi kedua (pulang)
            $absensi->update([
                'time_out' => $time_in,
            ]);

            return response()->json([
                'message'      => 'Absensi pulang tercatat',
                'user_id'      => $user->emp_code,
                'time_out'     => $time_in,
                'date'         => $date,
                'absensi_type' => 'OUT',
            ], 200);
        }
    }
    public function absensiBulanan(Request $request)
    {
        $data = $request->validate([
            'tahun'  => 'required|integer',
            'bulan'  => 'required|integer|min:1|max:12',
        ]);
        $tahun = $data['tahun'];
        $bulan = $data['bulan'];
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
        $users = User::with(['RekapAbsensiPegawai' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate])
                ->where('nagari_id', 1)
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

        $pdf = PDF::loadView('pdf.absensi', [
            'datesInMonth' => $datesInMonth,
            'holidays' => $holidays,
            'attendanceData' => $attendanceData,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'monthName' => Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y')
        ])->setPaper('a4', 'landscape');
        // return $pdf->stream();
        $filename = "absensi-{$bulan}-{$tahun}.pdf";
        $path = "public/invoices/{$filename}";
        Storage::put($path, $pdf->output());
        $wa = new WahaService();
        $wa->sendFile('6281282779593', Storage::url($path));
    }
}
