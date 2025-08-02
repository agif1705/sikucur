<?php

namespace App\Livewire\AbsensiPegawai;

use Livewire\Component;
use App\Models\Presensi;
use Filament\Forms\Form;
use App\Models\Attendance;
use App\Models\JadwalUser;
use Illuminate\Contracts\View\View;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;

class AbsensiDinasLuarDaerah extends Component implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];
    public $insideRadius = false;
    public $latitude;
    public $longitude;
    public function mount(): void
    {
        $this->form->fill();
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('absensi')
                    ->label('Status Absensi')
                    ->options(Presensi::all()->pluck('name', 'id')->except([1, 6]))
                    ->required()->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Masukan Sesuai Keperluan Absensi'),
                Textarea::make('tujuan')
                    ->label('Tujuan Dinas Luar Kantor')
                    ->required()
                    ->maxLength(255),

            ])
            ->statePath('data');
    }
    public function create(): void
    {
        dd($this->form->getState());
    }
    public function render()
    {
        $kehadiran = Attendance::where('user_id', Auth::user()->id)
            ->whereDate('created_at', date('Y-m-d'))
            ->first();
        $check_kehadiran = Attendance::where('user_id', Auth::user()->id)->whereDate('created_at', date('Y-m-d'))->whereDate('end_time', date('Y-m-d'))->first();
        $jadwal = JadwalUser::with('nagari', 'shift')->where('user_id', auth()->user()->id)->first();
        return view(
            'livewire.absensi-pegawai.absensi-dinas-luar-daerah',
            [
                'jadwal' => $jadwal,
                'isRadius' => $this->insideRadius,
                'kehadiran' => $kehadiran,
                'check_kehadiran' => $check_kehadiran,
            ]
        );
    }
}
