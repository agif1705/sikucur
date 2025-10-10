<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class WdmsModel extends Model
{
    protected $connection = 'mysql_second';
    protected $table = 'iclock_transaction';
    protected $fillable = [
        'punch_time',
        'terminal_sn',
        'terminal_alias',
        'area_alias',
        'emp_id',
        'emp_code',
        'terminal_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'emp_id', 'emp_id');
    }
    public function isLate()
    {
        $jadwal_start_time = Carbon::now()->setTime(8, 0, 0);
        $start_time = Carbon::parse($this->punch_time);
        return $start_time->greaterThan($jadwal_start_time);
    }
    public function checkIn($emp_id)
    {
        $wdms = $this->where('emp_id', $emp_id)->whereDate('punch_time', now()->format('Y-m-d'))->select('punch_time')->first();
        if ($wdms->count() == 0) {
            $check = Carbon::parse($wdms->punch_time);
            return $check;
        }
    }
    public static function getAbsensiMasuk($sn_fp)
    {
        // $tanggal = Carbon::parse($tanggal)->subDay(1)->format('Y-m-d');
        // --- Ambil IZIN ---
        $izin = RekapAbsensiPegawai::with('user.nagari', 'user.jabatan')
            ->whereDate('date', now()->format('Y-m-d'))
            ->get()
            ->map(function ($item) {
                if (!$item->user) return null; // skip jika user null
                return [
                    'user_id'   => (int) $item->user->id,
                    'jabatan'   => $item->user->jabatan?->name ?? 'Tidak Ada',
                    'name'      => $item->user->name ?? 'Tanpa Nama',
                    'slug'      => $item->user->slug ?? 'TanpaNama',
                    'nagari_id' => $item->user->nagari_id,
                    'time_only' => Carbon::parse($item->time_in)->format('H:i') ?? null,
                    'date_in'   => $item->date ? Carbon::parse($item->date)->format('Y-m-d') : null,
                    'kantor' => false,
                    'sn_mesin'  => $item->sn_mesin ?? null,
                    'is_late'   => Carbon::parse($item->time_in)->format('H:i') > '08:00',
                    'status'    => $item->status_absensi,
                    'absensi_by' => $item->resource,
                    'image'     => $item->user->image ?? 'default-avatar.png',
                ];
            })
            ->filter() // hapus null
            ->groupBy('user_id') // group by user_id untuk mengatasi duplikasi
            ->map(function ($userAbsensi) {
                // Jika ada multiple absensi untuk user yang sama dalam 1 hari
                // Prioritas: web > fingerprint > lainnya
                $priority = [
                    'web' => 1,
                    'Fingerprint' => 2,
                    'fingerprint' => 2, // case insensitive
                ];

                return $userAbsensi->sortBy(function ($item) use ($priority) {
                    return $priority[$item['absensi_by']] ?? 999; // default priority rendah
                })->first(); // ambil yang prioritas tertinggi (angka terkecil)
            })
            ->values();
        // --- Ambil HADIR ---


        // --- Filter HADIR supaya user yang sudah IZIN tidak muncul ---
        $userIzinIds = $izin->pluck('user_id')->all();

        // --- Gabung IZIN + HADIR ---
        // dd($rekap);
        // --- Ambil semua user aktif kecuali id 1 ---
        $users = User::with('nagari', 'jabatan')->where('id', '!=', 1)->get();

        // --- User yang tidak hadir ---
        $tidakHadir = $users
            ->reject(fn($user) => in_array((int)$user->id, $userIzinIds))
            ->map(function ($item) {
                return [
                    'user_id'   => (int)$item->id,
                    'jabatan'   => $item->jabatan?->name ?? 'Tidak Ada',
                    'name'      => $item->name ?? 'Tanpa Nama',
                    'slug'      => $item->slug ?? 'TanpaNama',
                    'nagari_id' => $item->nagari_id,
                    'time_only' => null,
                    'date_in'   => null,
                    'sn_mesin'  => null,
                    'is_late'   => false,
                    'absensi_by' => null,
                    'status'    => null,
                    'image'     => $item->image ?? 'default-avatar.png',
                ];
            });

        // --- Final merge ---
        $rekapFinal = collect($izin)->merge($tidakHadir)->values();
        // dd($rekapFinal);
        return $rekapFinal;
    }
    public static function getAbsensiMasukNoWaliNagari($sn_fp)
    {
        // $tanggal = Carbon::parse($tanggal)->subDay(1)->format('Y-m-d');
        // --- Ambil IZIN ---
        $izin = RekapAbsensiPegawai::with('user.nagari', 'user.jabatan')
            ->whereDate('date', now()->format('Y-m-d'))
            ->get()
            ->map(function ($item) {
                if (!$item->user) return null; // skip jika user null
                return [
                    'user_id'   => (int) $item->user->id,
                    'jabatan'   => $item->user->jabatan?->name ?? 'Tidak Ada',
                    'name'      => $item->user->name ?? 'Tanpa Nama',
                    'slug'      => $item->user->slug ?? 'TanpaNama',
                    'nagari_id' => $item->user->nagari_id,
                    'time_only' => Carbon::parse($item->time_in)->format('H:i') ?? null,
                    'date_in'   => $item->date ? Carbon::parse($item->date)->format('Y-m-d') : null,
                    'kantor' => false,
                    'sn_mesin'  => $item->sn_mesin ?? null,
                    'is_late'   => Carbon::parse($item->time_in)->format('H:i') > '08:00',
                    'status'    => $item->status_absensi,
                    'absensi_by' => $item->resource,
                    'image'     => $item->user->image ?? 'default-avatar.png',
                ];
            })
            ->filter() // hapus null
            ->groupBy('user_id') // group by user_id untuk mengatasi duplikasi
            ->map(function ($userAbsensi) {
                // Jika ada multiple absensi untuk user yang sama dalam 1 hari
                // Prioritas: web > fingerprint > lainnya
                $priority = [
                    'web' => 1,
                    'Fingerprint' => 2,
                    'fingerprint' => 2, // case insensitive
                ];

                return $userAbsensi->sortBy(function ($item) use ($priority) {
                    return $priority[$item['absensi_by']] ?? 999; // default priority rendah
                })->first(); // ambil yang prioritas tertinggi (angka terkecil)
            })
            ->values();
        // --- Ambil HADIR ---


        // --- Filter HADIR supaya user yang sudah IZIN tidak muncul ---
        $userIzinIds = $izin->pluck('user_id')->all();

        // --- Gabung IZIN + HADIR ---
        // dd($rekap);
        // --- Ambil semua user aktif kecuali id 1 ---
        $users = User::with('nagari', 'jabatan')
            ->where('id', '!=', 1)
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['super_admin', 'WaliNagari']);
            })
            ->get();

        // --- User yang tidak hadir ---
        $tidakHadir = $users
            ->reject(fn($user) => in_array((int)$user->id, $userIzinIds))
            ->map(function ($item) {
                return [
                    'user_id'   => (int)$item->id,
                    'jabatan'   => $item->jabatan?->name ?? 'Tidak Ada',
                    'name'      => $item->name ?? 'Tanpa Nama',
                    'slug'      => $item->slug ?? 'TanpaNama',
                    'nagari_id' => $item->nagari_id,
                    'time_only' => null,
                    'date_in'   => null,
                    'sn_mesin'  => null,
                    'is_late'   => false,
                    'absensi_by' => null,
                    'status'    => null,
                    'image'     => $item->image ?? 'default-avatar.png',
                ];
            });

        // --- Final merge ---
        $rekapFinal = collect($izin)->merge($tidakHadir)->values();
        // dd($rekapFinal);
        return $rekapFinal;
    }
}
