<?php

namespace App\Livewire\AbsensiPegawai;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Livewire\Component;
use App\Models\IzinPegawai;
use App\Models\AbsensiPegawai;
use App\Models\AbsensiWebPegawai;
use App\Models\RekapAbsensiPegawai;
use Livewire\Attributes\Layout;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;

class IzinPegawaiLivewire extends Component implements HasForms
{
    use InteractsWithForms;

    public $link, $users, $nagariId, $IzinPegawai;
    public $nagari, $form_link;
    public $expiresAt;
    public $remainingSeconds;
    public $expired = false;

    public $data = [
        'nama' => '',
        'alasan' => '',
    ];

    public function mount($link, $nagari)
    {
        $this->form_link = true;

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
                        'HDLD' => 'Dinas Luar Daerah',
                        'HDDD' => 'Dinas Dalam Daerah',
                        'S' => 'Sakit',
                        'I' => 'Izin',
                        'C' => 'Cuti',
                    ])->hintIcon('heroicon-m-question-mark-circle', tooltip: 'pilihlah sesuai absensi anda')
                    ->default('Hadir Dinas Luar Daerah'),
                FileUpload::make('file_pendukung')
                    ->label('File Pendukung')
                    ->disk('public')
                    ->directory('absensi')
                    ->multiple(false)
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

        $absensiPegawai = AbsensiWebPegawai::whereUserId($this->users->id)
            ->whereDate('date', now())->first();

        if (!$absensiPegawai) {
            $absensiPegawai = AbsensiWebPegawai::create([
                'absensi' => $this->data['status'],
                'is_late' => false,
                'file_pendukung' => $this->data['file_pendukung'],
                'user_id' => $this->users->id,
                'nagari_id' => $this->users->nagari_id,
                'time_in' => now(),
                'date' => now(),
                'time_out' => now(),
            ]);
            $this->dispatch('absenBerhasil', nama: $this->users->name, jam: $absensiPegawai->time_in, status: $absensiPegawai->status_absensi);
            $rekapAbsensiPegawai = RekapAbsensiPegawai::create([
                'user_id' => $this->users->id,
                'nagari_id' => $this->users->nagari_id,
                'is_late' => false,
                'sn_mesin' => $this->link,
                'status_absensi' => $this->data['status'],
                'resource' => 'web',
                'id_resource' => 'web-' . $absensiPegawai->id,
                'time_in' => '07:59',
                'time_out' =>  '16:01',
                'date' => $absensiPegawai->date,

            ]);
        }

        $this->IzinPegawai->update([
            'expired_at' => now()->subMinutes(30),
        ]);
        $this->form_link = false;
    }
    #[Layout('components.layouts.public')]
    public function render()
    {
        return view('livewire.absensi-pegawai.izin-pegawai-livewire');
    }
}
