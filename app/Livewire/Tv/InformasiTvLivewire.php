<?php

namespace App\Livewire\Tv;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Nagari;
use Livewire\Component;
use App\Models\WdmsModel;
use App\Models\Attendance;
use Illuminate\Support\Str;
use App\Helpers\WhatsAppHelper;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Http;

class InformasiTvLivewire extends Component
{
    // public $users;
    // public $now, $tv,   $galeri;
    // public $sn_fp, $logo, $tvNow;

    // public function mount($sn)
    // {
    //     // $this->now = Carbon::create('2025-6-13')->format('Y-m-d');
    //     $this->now = Carbon::now()->format('Y-m-d');
    //     $this->tvNow = Carbon::now()->format('d M Y');
    //     $sn_fp = Nagari::with('TvInformasi', 'galeri')->where('name', $sn)->first();
    //     $this->logo = $sn_fp->logo;
    //     $this->sn_fp = $sn_fp->sn_fingerprint;
    //     $this->tv = $sn_fp->TvInformasi;
    //     $this->galeri = $sn_fp->galeri->take(10);
    //     $this->users = WdmsModel::with('user')->where('terminal_sn', $this->sn_fp)
    //         ->whereDate('punch_time', $this->now)
    //         ->select('emp_id', 'punch_time', 'emp_code')
    //         ->whereTime('punch_time', '<=', '12:00')
    //         ->get()
    //         ->unique('emp_code')
    //         ->map(function ($item) {
    //             $item->time_only = \Carbon\Carbon::parse($item->punch_time)->format('H:i');
    //             if ($item->time_only > '08:00') {
    //                 $item->is_late = true;
    //             } else {
    //                 $item->is_late = false;
    //             }
    //             return $item;
    //         });




    //     // dd($sn_fp);
    // }

    // public function refreshData()
    // {
    //     $this->users = WdmsModel::with('user')->where('terminal_sn', $this->sn_fp)
    //         ->whereDate('punch_time', $this->now)
    //         ->select('emp_id', 'punch_time', 'emp_code')
    //         ->whereTime('punch_time', '<=', '12:00')
    //         ->get()
    //         ->unique('emp_code')
    //         ->map(function ($item) {
    //             $item->time_only = \Carbon\Carbon::parse($item->punch_time)->format('H:i');
    //             if ($item->time_only > '08:00') {
    //                 $item->is_late = true;
    //             } else {
    //                 $item->is_late = false;
    //             }
    //             return $item;
    //         });
    //     foreach ($this->users as $key => $attandance) {
    //         $check = Attendance::where('user_id', $attandance->user->id)
    //             ->whereNotNull('start_time')
    //             ->whereDate('date_in', $this->now)
    //             ->exists();
    //         $is_late = $attandance->is_late ? ' *Terlambat* ' : ' *Tepat Waktu* ';
    //         if ($check === false) {
    //             // Mulai database transaction
    //             DB::beginTransaction();

    //             try {
    //                 $save_attandance = Attendance::create([
    //                     'user_id' => $attandance->user->id,
    //                     'nagari_id' => $attandance->user->nagari->id,
    //                     'absensi_by' => 'fingerprint',
    //                     'absensi' => 'Hadir',
    //                     'accept' => true,
    //                     'accept_by' => 'fingerprint',
    //                     'keterangan_absensi' => 'Hadir_Fingerprint',
    //                     'jadwal_latitude' => -0.50749418802036,
    //                     'jadwal_longitude' => 100.13539851192,
    //                     'jadwal_start_time' => '08:00:00',
    //                     'jadwal_end_time' => '16:00:00',
    //                     'start_latitude' => -0.50749418802036,
    //                     'start_longitude' => 100.13539851192,
    //                     'end_latitude' => -0.50749418802036,
    //                     'end_longitude' => 100.13539851192,
    //                     'start_time' => $attandance->time_only,
    //                     'date_in' => $attandance->punch_time
    //                 ]);

    //                 $noWa = sprintf('0%d', $attandance->user->no_hp);

    //                 // Kirim WhatsApp dengan retry
    //                 $response = retry(3, function () use ($noWa, $attandance, $is_late) {
    //                     return WhatsAppHelper::sendToFonnte(
    //                         $noWa,
    //                         'Hai *' . $attandance->user->name . '* , Anda ' . $is_late . ' anda telah hadir menggunakan fingerprint di *Nagari ' . $attandance->user->nagari->name .
    //                             '* ,ini akan masuk ke WhatsApp Wali Nagari ' . $attandance->user->nagari->name .
    //                             ' *Sebelum Jam: 12:00 Siang* terimakasih '
    //                     );
    //                 });

    //                 // Verifikasi response
    //                 if (!isset($response['success'])) {
    //                     throw new \Exception("Invalid response from WhatsApp API");
    //                 }

    //                 if ($response['success'] === false) {
    //                     throw new \Exception("Failed to send WhatsApp: " . ($response['error'] ?? 'Unknown error'));
    //                 }

    //                 // Jika semua berhasil, commit transaction
    //                 DB::commit();
    //             } catch (\Exception $e) {
    //                 // Rollback transaction jika ada error
    //                 DB::rollBack();

    //                 // Log error
    //                 \Log::error('Failed to process attendance for user ' . $attandance->user->id . ': ' . $e->getMessage());

    //                 // Anda bisa menambahkan handling error lainnya di sini
    //                 continue; // Lanjut ke user berikutnya
    //             }
    //         }
    //     }
    // }
    public function render()
    {

        return view('livewire.tv.commingsoon')->layout('components.layouts.tv');
        // return view('livewire.tv.informasi-tv-livewire')->layout('components.layouts.tv');
    }
}
