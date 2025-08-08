<?php

namespace App\Livewire\Tv;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Nagari;
use Livewire\Livewire;
use Livewire\Component;
use App\Models\WdmsModel;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Models\AbsensiPegawai;
use App\Helpers\WhatsAppHelper;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Http;

class InformasiTvLivewire extends Component
{
    public $now;
    public $users;
    public $tv, $galeri;
    public $sn_fp, $logo, $tvNow, $datas;



    #[On('fingerprint-updated')]
    public function updateData($mesin, $data)
    {


        // memasukan data absensi pegawai
        if ($data['emp_id']) {
            $emp_id = $data['emp_id'];
        } else {
            $emp_id = intval($data['emp_code']);
        }
        // seleksi permesin data nagari dan user
        $user = User::where('emp_id', $emp_id)
            ->whereHas('nagari', function ($q) use ($mesin) {
                $q->where('sn_fingerprint', $mesin);
            })->first();

        $is_late = $data['punch_time'] > '08:00' ?  'Ontime' : 'Terlambat';
        $punchTime = Carbon::parse($data['punch_time']);
        $date = $punchTime->toDateString();
        $time = $punchTime->toTimeString();
        $attendance = AbsensiPegawai::whereUserId($user->id)
            ->whereDate('date_in', $date)
            ->first();
        if (!$attendance) {
            // Tidak ada record sama sekali → Buat baru
            $attendance = AbsensiPegawai::create([
                'absensi_by' => 'Fingerprint',
                'emp_id' => $emp_id,
                'absensi' => 'Hadir',
                'status_absensi' => $is_late,
                'sn_mesin' => $mesin,
                'accept' => true,
                'accept_by' => $user->name,
                'nagari_id' => $user->nagari->id,
                'user_id' => $user->id,
                'date_in' => $date,
                'time_in' => $time,
            ]);
            $this->dispatch('absenBerhasil', nama: $user->name, jam: $attendance->time_in, status: $is_late);

            if ($user->aktif) {
                $response = retry(3, function () use ($user, $is_late) {
                    return WhatsAppHelper::sendMessage(
                        $user->no_hp,
                        'Hai *' . $user->name . '* , Anda *' . $is_late . '* anda telah hadir menggunakan fingerprint di *Nagari ' . $user->nagari->name .
                            '* ,ini akan masuk ke WhatsApp Wali Nagari ' . $user->nagari->name .
                            ' *Sebelum Jam: 12:00 Siang* terima kasih '
                    );
                });
            }
        } else {
            // Sudah ada record → update sesuai waktu yang masuk

            // Jika waktu lebih pagi dari time_in → set sebagai time_in
            if ($punchTime->hour < 12) {
                // Anggap punch masuk
                if (!$attendance->time_in || $time < $attendance->time_in) {
                    $attendance->time_in = $time;
                }
            } elseif ($punchTime->hour >= 13) {
                // Anggap punch pulang
                if (!$attendance->time_out || $time > $attendance->time_out) {
                    $attendance->time_out = $time;
                }
            }

            $attendance->updated_at = now();
            $attendance->save();
        }

        $this->users = WdmsModel::getAbsensiMasuk($mesin, $this->now);
    }

    #[On('fingerprint-deleted')]
    public function deleteData()
    {
        $this->users = WdmsModel::getAbsensiMasuk($this->sn_fp, now()->format('Y-m-d'));
    }

    #[Layout('components.layouts.tv')]
    public function render()
    {
        // return view('livewire.tv.commingsoon');
        return view('livewire.tv.informasi-tv-livewire');
    }
    public function mount($sn)
    {
        // $this->now = Carbon::create('2025-6-13')->format('Y-m-d');
        $this->now = Carbon::now()->format('Y-m-d');
        $this->tvNow = Carbon::now()->format('d M Y');
        $sn_fp = Nagari::with('TvInformasi', 'galeri')->where('name', $sn)->first();
        if (!$sn_fp) {
            abort(404, 'Nagari tidak ditemukan');
        }
        $this->logo = $sn_fp->logo;
        $this->sn_fp = $sn_fp->sn_fingerprint;
        $this->tv = $sn_fp->TvInformasi;
        $this->galeri = $sn_fp->galeri->take(10);
        $month = Carbon::now()->month;

        $this->users = WdmsModel::getAbsensiMasuk($this->sn_fp, $this->now);
    }
}
