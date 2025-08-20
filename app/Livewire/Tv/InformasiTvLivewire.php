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
use App\Models\iclock_transaction;
use App\Models\ListYoutube;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Http;

class InformasiTvLivewire extends Component
{
    public $now;
    public $users;
    public $tv, $galeri;
    public $sn_fp, $logo, $tvNow, $datas, $playlistStr;
    public $videoId = "Lb4AwReHYxQ";

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
        $is_late = Carbon::parse($data['punch_time'])->format('H:i') > '08:00' ?  'Terlambat' : 'Ontime';
        // if ($user->aktif) {
        //     $response = retry(3, function () use ($user, $is_late, $data) {
        //         return WhatsAppHelper::sendMessage(
        //             6281282779593,
        //             'Hai *' . $user->name . '* , Anda *' . $is_late . '* anda telah hadir pada jam *' . Carbon::parse($data['punch_time'])->format('H:i') . '* menggunakan fingerprint di *Nagari ' . $user->nagari->name .
        //                 '* ,ini akan masuk ke WhatsApp Wali Nagari ' . $user->nagari->name .
        //                 ' *Sebelum Jam: 12:00 Siang* terima kasih '
        //         );
        //     });
        // }
        $this->dispatch('absenBerhasil', nama: $user->name, jam: Carbon::parse($data['punch_time'])->format('H:i'), status: $is_late);

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
        $sn_fp = Nagari::with('TvInformasi', 'galeri')->where('slug', $sn)->first();
        $nagariId = $sn_fp->id;
        $list = ListYoutube::where('nagari_id', $nagariId)->get();
        $playlist = [
            'id_youtube' => $list->pluck('id_youtube')->toArray()
        ];
        $this->playlistStr = implode(',', $playlist['id_youtube']);
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
