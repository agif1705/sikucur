<?php

namespace App\Livewire\AbsensiPegawai;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Livewire\Component;
use App\Models\IzinPegawai;
use App\Models\AbsensiPegawai;
use Livewire\Attributes\Layout;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;

class IzinPegawaiLivewire extends Component implements HasForms
{
    use InteractsWithForms;

    public $link, $users, $nagariId, $IzinPegawai;
    public $nagari;
    public $expiresAt;
    public $remainingSeconds;
    public $expired = false;

    public $data = [
        'nama' => '',
        'alasan' => '',
    ];

    public function mount($link, $nagari)
    {
        $this->form->fill();
        $this->IzinPegawai = IzinPegawai::with([
            'user:id,name,emp_id,nagari_id,no_hp',
        ])
            ->where('link', $link)
            ->where('expired_at', '>', now())
            ->firstOrFail();
        if (!$this->IzinPegawai) {
            abort(403, 'Link sudah expired atau tidak valid.');
        }
        $this->users = $this->IzinPegawai->user;
        $this->link = $link;
        $this->nagari = $nagari;
        $this->expiresAt = Carbon::parse($this->IzinPegawai->expired_at)->timestamp;
        $this->remainingSeconds = max(0, $this->expiresAt - now()->timestamp);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('status')
                    ->options([
                        'Hadir Dinas Luar Daerah' => 'Dinas Luar Daerah',
                        'Hadir Dinas Dalam Daerah' => 'Dinas Dalam Daerah',
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Cuti' => 'Cuti',
                    ])->hintIcon('heroicon-m-question-mark-circle', tooltip: 'pilihlah sesuai absensi anda')
                    ->default('Hadir Dinas Luar Daerah'),
                FileUpload::make('file_pendukung')
                    ->label('File Pendukung')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'ini adalah file pendukung laporan absensi')
                    ->required(),
                Textarea::make('alasan')
                    ->label('Alasan / Keperluan / Tujuan')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        if ($this->data['status'] == 'Hadir Dinas Dalam Daerah' || $this->data['status'] == 'Hadir Dinas Luar Daerah') {
            $status_absensi = "HDL";
        }
        $absensiPegawai = AbsensiPegawai::whereUserId($this->users->id)
            ->whereDate('date_in', now())->first();
        if (!$absensiPegawai) {
            $absensiPegawai = AbsensiPegawai::create([
                'absensi_by' => 'Link Izin',
                'emp_id' => $this->users->emp_id,
                'absensi' => $this->data['status'],
                'status_absensi' => $status_absensi,
                'sn_mesin' => "link-izin",
                'accept' => true,
                'accept_by' => $this->data['alasan'],
                'user_id' => $this->users->id,
                'nagari_id' => $this->users->nagari_id,
                'time_in' => now(),
                'date_in' => now(),
            ]);
            $this->dispatch('absenBerhasil', nama: $this->users->name, jam: $absensiPegawai->time_in, status: $absensiPegawai->status_absensi);
        }
        $this->IzinPegawai->update([
            'expired_at' => now()->subMinutes(30),
        ]);
        session()->flash('success', 'Izin berhasil dikirim.');
    }
    #[Layout('components.layouts.public')]
    public function render()
    {
        return view('livewire.absensi-pegawai.izin-pegawai-livewire');
    }
}
