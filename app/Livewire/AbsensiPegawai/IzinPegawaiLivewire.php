<?php

namespace App\Livewire\AbsensiPegawai;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use App\Models\WorkDay;
use Livewire\Component;
use App\Models\IzinPegawai;
use App\Models\WhatsAppLog;
use Illuminate\Support\Str;
use App\Services\GowaService;
use App\Models\AbsensiPegawai;
use Livewire\Attributes\Layout;
use App\Models\AbsensiWebPegawai;
use App\Models\RekapAbsensiPegawai;
use Illuminate\Support\Facades\Auth;
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
        'status' => '',
        'tanggal_mulai' => null,
        'tanggal_selesai' => null,
        'jumlah_hari' => 1,
        'file_pendukung' => null,
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
                    ->default('Hadir Dinas Luar Daerah')
                    ->live(), // tambahkan live() untuk reactivity

                // Field tanggal mulai - hanya muncul jika status Sakit, Izin, atau Cuti
                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->default(now())
                    ->minDate(now()->subDays(3)) // maksimal 7 hari kebelakang
                    ->maxDate(now()->addDays(30)) // maksimal 30 hari kedepan
                    ->hintIcon('heroicon-m-calendar-days', tooltip: 'Pilih tanggal mulai sakit/izin/cuti')
                    ->visible(fn(Forms\Get $get): bool => in_array($get('status'), ['S', 'C']))
                    ->required(fn(Forms\Get $get): bool => in_array($get('status'), ['S', 'C']))
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                        $this->calculateWorkDays($set, $get, $state);
                    }),

                // Field tanggal selesai - hanya muncul jika status Sakit, Izin, atau Cuti
                Forms\Components\DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->default(fn(Forms\Get $get): ?Carbon => $get('tanggal_mulai') ? Carbon::parse($get('tanggal_mulai')) : now())
                    ->minDate(fn(Forms\Get $get): ?Carbon => $get('tanggal_mulai') ? Carbon::parse($get('tanggal_mulai')) : now())
                    ->maxDate(now()->addDays(30))
                    ->hintIcon('heroicon-m-calendar-days', tooltip: 'Pilih tanggal selesai sakit/izin/cuti')
                    ->visible(fn(Forms\Get $get): bool => in_array($get('status'), ['S', 'C']))
                    ->required(fn(Forms\Get $get): bool => in_array($get('status'), ['S', 'C']))
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                        $this->calculateWorkDays($set, $get, $state);
                    }),

                // Field jumlah hari - otomatis calculated, readonly
                Forms\Components\TextInput::make('jumlah_hari')
                    ->label('Jumlah Hari Kerja')
                    ->numeric()
                    ->readOnly()
                    ->hintIcon('heroicon-m-calculator', tooltip: 'Jumlah hari kerja dihitung otomatis (exclude weekend & libur)')
                    ->hint(function ($state) {
                        return empty($state)
                            ? 'Jumlah hari kerja dihitung otomatis (exclude weekend & libur)'
                            : null;
                    })
                    ->hintColor(fn($state) => empty($state) ? 'danger' : 'gray')
                    ->suffix('hari kerja')

                    ->visible(fn(Forms\Get $get): bool => in_array($get('status'), ['S', 'C'])),

                Forms\Components\FileUpload::make('file_pendukung')
                    ->label('Foto Pendukung')
                    ->directory('izin')
                    ->getUploadedFileNameForStorageUsing(function ($file): string {
                        $date = now()->format('Ymd');
                        $uuid = Str::uuid();
                        $name = Auth::user()->username ?? 'user';
                        $ext  = $file->getClientOriginalExtension(); // ambil extensi asli
                        return "izin-{$name}-{$date}-{$uuid}.{$ext}";
                    })
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'ini adalah file pendukung laporan absensi'),

                Textarea::make('alasan')
                    ->label('Alasan / Keperluan / Tujuan')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        // Validasi form terlebih dahulu
        $this->form->validate();

        // Untuk status S (Sakit) dan C (Cuti) yang memiliki range tanggal
        if (in_array($this->data['status'], ['S', 'C']) && $this->data['tanggal_mulai'] && $this->data['tanggal_selesai']) {
            $this->createMultipleDayAbsensi();
        } else {
            // Untuk status lain yang hanya 1 hari
            $this->createSingleDayAbsensi();
        }

        // Update link expired dan kirim notifikasi
        $this->finalizeSubmission();
    }

    private function createSingleDayAbsensi()
    {
        $absensiPegawai = AbsensiWebPegawai::whereUserId($this->users->id)
            ->whereDate('date', now())->first();

        if (!$absensiPegawai) {
            $absensiPegawai = AbsensiWebPegawai::create([
                'absensi' => $this->data['status'],
                'alasan' => $this->data['alasan'],
                'is_late' => false,
                'file_pendukung' => $this->data['file_pendukung'],
                'user_id' => $this->users->id,
                'nagari_id' => $this->users->nagari->id,
                'time_in' => now(),
                'date' => now(),
                'time_out' => now(),
            ]);

            $this->sendWhatsAppNotification($absensiPegawai);
            $this->createRekapAbsensi($absensiPegawai);
        }
    }

    private function createMultipleDayAbsensi()
    {
        $startDate = Carbon::parse($this->data['tanggal_mulai']);
        $endDate = Carbon::parse($this->data['tanggal_selesai']);
        $currentDate = $startDate->copy();

        $createdAbsensi = [];

        while ($currentDate->lte($endDate)) {
            // Cek apakah sudah ada absensi untuk tanggal ini
            $existingAbsensi = AbsensiWebPegawai::whereUserId($this->users->id)
                ->whereDate('date', $currentDate)->first();

            if (!$existingAbsensi) {
                $absensiPegawai = AbsensiWebPegawai::create([
                    'absensi' => $this->data['status'],
                    'alasan' => $this->data['alasan'],
                    'is_late' => false,
                    'file_pendukung' => $this->data['file_pendukung'],
                    'user_id' => $this->users->id,
                    'nagari_id' => $this->users->nagari->id,
                    'time_in' => $currentDate->copy()->setTime(8, 0),
                    'date' => $currentDate->copy(),
                    'time_out' => $currentDate->copy()->setTime(16, 0),
                ]);

                $createdAbsensi[] = $absensiPegawai;
                $this->createRekapAbsensi($absensiPegawai);
            }

            $currentDate->addDay();
        }

        // Kirim notifikasi WhatsApp hanya sekali untuk semua range
        if (!empty($createdAbsensi)) {
            $this->sendWhatsAppNotification($createdAbsensi[0]); // Gunakan absensi pertama sebagai referensi
        }
    }

    private function sendWhatsAppNotification($absensiPegawai)
    {
        $tanggal = now()->toDateString();
        $baduo = " \n \n \n \n _Sent || via *Cv.Baduo Mitra Solustion*_";

        // Format pesan dengan range tanggal jika ada
        $periodeText = "";
        if (in_array($this->data['status'], ['S', 'I', 'C']) && $this->data['tanggal_mulai'] && $this->data['tanggal_selesai']) {
            $tanggalMulai = Carbon::parse($this->data['tanggal_mulai'])->format('d/m/Y');
            $tanggalSelesai = Carbon::parse($this->data['tanggal_selesai'])->format('d/m/Y');
            $jumlahHari = $this->data['jumlah_hari'];

            if ($tanggalMulai === $tanggalSelesai) {
                $periodeText = "\n📅 Tanggal : {$tanggalMulai} (1 hari)";
            } else {
                $periodeText = "\n📅 Periode : {$tanggalMulai} s/d {$tanggalSelesai} ({$jumlahHari} hari)";
            }
        }

        $pesan = "📊 Pemberitahuan Link Izin Pegawai Hari ini " . $tanggal . " Sebagai Berikut :"
            . "\n👤 Nama : " . $this->users->name
            . "\n📋 Status : " . $this->data['status']
            . "\n🏢 Jabatan : " . $this->users->jabatan->name
            . $periodeText
            . "\n📝 Alasan : *" . $this->data['alasan'] . "*  ";

        $wa = new GowaService();

        // Kirim ke Wali
        $wali = $wa->sendText($this->users->nagari->wali->no_hp, $pesan . ' ' . $baduo);
        WhatsAppLog::create([
            'user_id' => $this->users->id,
            'phone'   => $this->users->nagari->wali->no_hp,
            'message' =>  $pesan . ' ' . $baduo,
            'status'  => $wali['success'] ?? false ? 'success' : 'failed',
            'response' => $wali,
        ]);

        // Kirim ke Sekretaris
        $seketaris = $wa->sendText($this->users->nagari->seketaris->no_hp, $pesan . ' ' . $baduo);
        WhatsAppLog::create([
            'user_id' => $this->users->id,
            'phone'   => $this->users->nagari->seketaris->no_hp,
            'message' =>  $pesan . ' ' . $baduo,
            'status'  => $seketaris['success'] ?? false ? 'success' : 'failed',
            'response' => $seketaris,
        ]);

        $this->dispatch('absenBerhasil', nama: $this->users->name, jam: $absensiPegawai->time_in, status: $absensiPegawai->absensi);
    }

    private function createRekapAbsensi($absensiPegawai)
    {
        RekapAbsensiPegawai::create([
            'user_id' => $this->users->id,
            'nagari_id' => $this->users->nagari_id,
            'is_late' => false,
            'sn_mesin' => $this->link,
            'status_absensi' => $this->data['status'],
            'resource' => 'web',
            'id_resource' => 'web-' . $absensiPegawai->id,
            'time_in' => now()->format('H:i'),
            'time_out' => now()->addHours(8)->format('H:i'),
            'date' => $absensiPegawai->date,
        ]);
    }

    private function finalizeSubmission()
    {
        $this->IzinPegawai->update([
            'expired_at' => now()->subMinutes(30),
        ]);
        $this->form_link = false;
    }
    #[Layout('components.layouts.public')]
    protected function calculateWorkDays($set, $get, $state)
    {
        $tanggalMulai = $get('tanggal_mulai');
        $tanggalSelesai = $get('tanggal_selesai');

        if (!$tanggalMulai || !$tanggalSelesai) {
            $set('jumlah_hari', 0);
            return;
        }

        try {
            $startDate = Carbon::parse($tanggalMulai);
            $endDate = Carbon::parse($tanggalSelesai);

            if ($endDate->lt($startDate)) {
                $set('jumlah_hari', 0);
                return;
            }

            $workDays = 0;
            $currentDate = $startDate->copy();

            // Get user's nagari_id for work_days configuration
            $user = Auth::user();
            $nagariId = $user->nagari_id ?? null;

            // Get working days configuration for this nagari
            $workingDaysConfig = WorkDay::where('nagari_id', $nagariId)
                ->where('is_working_day', true)
                ->pluck('day')
                ->toArray();

            // Default working days if no configuration exists (Monday to Friday)
            if (empty($workingDaysConfig)) {
                $workingDaysConfig = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            }

            while ($currentDate->lte($endDate)) {
                $dayName = strtolower($currentDate->format('l')); // Get day name (monday, tuesday, etc.)

                // Check if this day is configured as a working day
                if (in_array($dayName, $workingDaysConfig)) {
                    $workDays++;
                }

                $currentDate->addDay();
            }

            $set('jumlah_hari', $workDays);
        } catch (\Exception $e) {
            $set('jumlah_hari', 0);
        }
    }

    public function render()
    {
        return view('livewire.absensi-pegawai.izin-pegawai-livewire');
    }
}