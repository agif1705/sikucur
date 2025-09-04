<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;

class RekapAbsensiPegawai extends Model
{

    protected $fillable = [
        'user_id',
        'nagari_id',
        'is_late',
        'sn_mesin',
        'status_absensi',
        'resource',
        'id_resource',
        'time_in',
        'time_out',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function nagari()
    {
        return $this->belongsTo(Nagari::class);
    }
    public function scopeIsLate($query)
    {
        return $query->where('is_late', true);
    }
    public function Holiday($bulan, $tahun)
    {
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
        $today = now()->toDateString();
        $holidays = collect($holiday_api)
            ->where('is_national_holiday', true)
            ->filter(function ($event) use ($bulan, $tahun, $today) {
                $eventDate = Carbon::parse($event['event_date']);
                return $eventDate->month == $bulan &&
                    $eventDate->year == $tahun &&
                $eventDate->toDateString() <= $today &&   // hanya libur sampai hari ini
                !$eventDate->isWeekend();
            })
            ->pluck('event_date', 'event_name');

        return $holidays->count();
    }
    public function getAbsensiToday($now)
    {
        return $this->with('user.nagari', 'user.jabatan')
            ->whereDate('date', now()->format('Y-m-d'))
            ->get();
    }
    public function scopeForUserThisMonth($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
    }
}
