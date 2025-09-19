<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use App\Models\User;
use Filament\Tables;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Models\RekapAbsensiPegawai;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

/**
 * Halaman untuk menampilkan absensi bulanan pegawai
 * Mendukung filter berdasarkan bulan dan tahun
 * Dapat menggenerate laporan PDF
 */
class AttendanceUser extends Page implements HasTable
{
    use InteractsWithTable;
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.attendance-user';
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?string $navigationLabel = 'Absensi Bulanan';

    /**
     * Bulan yang dipilih (1-12)
     */
    public int $month;

    /**
     * Tahun yang dipilih
     */
    public int $year;

    /**
     * Nama bulan dalam bahasa Indonesia
     */
    public string $bulan;

    /**
     * Mount method dengan validasi input
     */
    public function mount(): void
    {
        $this->month = (int) request()->query('month', now()->month);
        $this->year = (int) request()->query('year', now()->year);

        // Validasi input month dan year
        if ($this->month < 1 || $this->month > 12) {
            $this->month = now()->month;
        }

        if ($this->year < 2020 || $this->year > now()->year + 1) {
            $this->year = now()->year;
        }

        $this->bulan = Carbon::create($this->year, $this->month, 1)->monthName;
    }

    /**
     * Mendapatkan judul halaman dengan error handling
     */
    public function getHeading(): string
    {
        try {
            $nagariName = Auth::user()->nagari->name ?? 'Unknown';
            return 'Absensi Pegawai Nagari ' . $nagariName;
        } catch (\Exception $e) {
            return 'Absensi Pegawai';
        }
    }

    /**
     * Konfigurasi tabel absensi
     */
    public function table(Table $table): Table
    {
        $startDate = Carbon::create($this->year, $this->month, 1);
        $endDate   = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Buat kolom untuk setiap hari dalam bulan
        $dayColumns = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($this->year, $this->month, $day);
            $dayName     = strtolower($currentDate->format('l')); // format hari dalam bahasa inggris lowercase

            $dayColumns[] = TextColumn::make($currentDate->format('Y-m-d'))
                ->label($day)
                ->getStateUsing(function ($record) use ($currentDate, $dayName) {
                // Cek tanggal masa depan
                $today = now()->toDateString();
                if ($currentDate->toDateString() > $today) {
                    return '-'; // Tanggal belum terjadi
                }
                    $attendance = $record->rekapAbsensiPegawai
                        ->where('date', $currentDate->toDateString())
                        ->first();

                    if ($attendance) {
                        return match ($attendance->status_absensi) {
                        'Hadir' => 'H',
                        'HDLD'  => 'HDLD',
                        'HDDD'  => 'HDDD',
                        'S' => 'S',
                        'C'  => 'C',
                        'I'  => 'I',
                        default => 'A',
                        };
                    }

                    // Jika tidak ada data absensi, cek hari kerja
                    $workDay = $record->nagari
                        ->workDays()
                        ->where('day', $dayName) // lowercase sesuai format
                        ->first();

                    if ($workDay && !$workDay->is_working_day) {
                        return 'L'; // Hari libur
                    }

                    return 'A'; // Default: Tidak hadir (Alpha)
                })
                ->color(fn($state) => match ($state) {
                    'H', 'HDLD', 'HDDD' => 'success',
                    'A'                => 'danger',
                    'S'                => 'warning',
                    'I'                => 'primary',
                    'C'                => 'info',
                    'L'                => 'secondary',
                    default            => 'secondary',
                })
                ->icon(fn($state) => match ($state) {
                    'H', 'HDLD', 'HDDD' => 'heroicon-o-check-circle',
                    'A'                => 'heroicon-o-x-circle',
                    'S'                => 'heroicon-o-exclamation-circle',
                    'I'                => 'heroicon-o-information-circle',
                    'C'                => 'heroicon-o-briefcase',
                    'L'                => 'heroicon-o-calendar',
                    default            => 'heroicon-o-question-mark-circle',
                })
                ->alignCenter();
        }

        return $table
            ->query(
            User::query()
                ->when(
                    Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('Kaur Umum dan Perencanan'),
                    fn($q) => $q->where('id', '!=', 1), // super_admin & kaur umum dapat melihat semua user kecuali id=1
                    fn($q) => $q->where('id', Auth::id()) // user biasa hanya dapat melihat data dirinya sendiri
                )
                ->with([
                    'rekapAbsensiPegawai' => function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('date', [$startDate, $endDate]);
                    },
                    'nagari.workDays' // Eager load work days untuk menghindari N+1 problem
                ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pegawai')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_kantor')
                    ->label('Kantor')
                    ->getStateUsing(
                        fn($record) =>
                        $record->rekapAbsensiPegawai
                            ->where('status_absensi', 'Hadir')
                            ->count()
                    ),

                Tables\Columns\TextColumn::make('total_hadir_dinas_luar')
                    ->label('Luar Daerah')
                    ->getStateUsing(
                        fn($record) =>
                        $record->rekapAbsensiPegawai
                            ->where('status_absensi', 'HDLD')
                            ->count()
                    ),

                Tables\Columns\TextColumn::make('total_hadir_dinas_dalam')
                    ->label('Dalam Daerah')
                    ->getStateUsing(
                        fn($record) =>
                        $record->rekapAbsensiPegawai
                            ->where('status_absensi', 'HDDD')
                            ->count()
                    ),

                Tables\Columns\TextColumn::make('total_izin')
                    ->label('Total Izin')
                    ->getStateUsing(
                        fn($record) =>
                        $record->rekapAbsensiPegawai
                            ->where('status_absensi', 'Izin')
                            ->count()
                    )
                    ->color(fn($state) => $state > 0 ? 'primary' : 'success')
                    ->icon(fn($state) => $state > 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle'),

                Tables\Columns\TextColumn::make('total_absent')
                ->label('Total Alpha')
                ->getStateUsing(function ($record) use ($startDate, $endDate) {
                    $today = now();

                    // Tentukan batas tanggal untuk menghitung absensi
                    if ($startDate->isSameMonth($today)) {
                        $lastCountDate = $today->toDateString();
                    } elseif ($startDate->lt($today)) {
                        $lastCountDate = $endDate->toDateString();
                    } else {
                        return '-'; // Bulan yang akan datang, belum ada data absensi
                    }

                    // Buat daftar semua tanggal kerja (hari kerja saja, tidak termasuk weekend)
                    $dates = [];
                    $current = $startDate->copy();
                    while ($current->toDateString() <= $lastCountDate) {
                        if (!$current->isWeekend()) {
                            $dates[] = $current->toDateString();
                        }
                        $current->addDay();
                    }

                    // Ambil daftar hari libur resmi pada bulan tersebut
                    $rekap = new RekapAbsensiPegawai();
                $holidays = $rekap->Holiday($startDate->month, $startDate->year);

                // Jika method Holiday() mengembalikan angka, ubah menjadi array kosong
                if (!is_array($holidays)) {
                    $holidays = [];
                }

                // Keluarkan hari libur dari daftar tanggal kerja
                $workingDays = collect($dates)->reject(fn($d) => in_array($d, $holidays));

                // Ambil semua tanggal absensi yang valid untuk user
                $hadirDates = $record->rekapAbsensiPegawai
                    ->whereIn('status_absensi', ['Hadir', 'HDLD', 'HDDD', 'Sakit', 'Cuti', 'Izin'])
                    ->whereBetween('date', [$startDate->toDateString(), $lastCountDate])
                    ->pluck('date')
                    ->toArray();

                // Hitung alpha = hari kerja - hari hadir
                $alpha = $workingDays->reject(fn($d) => in_array($d, $hadirDates))->count();

                return $alpha;
            })
                ->color(fn($state) => is_numeric($state) && $state > 0 ? 'danger' : 'success')
                ->icon(fn($state) => is_numeric($state) && $state > 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->alignCenter(),

            ...$dayColumns,
            ])->paginated(false)
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filter bulan dan tahun bisa ditambahkan di sini
            ])
            ->actions([
                // Action untuk setiap baris bisa ditambahkan di sini

            ])
            ->bulkActions([
                // Bulk action bisa ditambahkan di sini
            ]);
    }

    /**
     * Konfigurasi header actions
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->label('Filter Bulan/Tahun')
                ->icon('heroicon-o-funnel')
                ->form([
                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            '1' => 'Januari',
                            '2' => 'Februari',
                            '3' => 'Maret',
                            '4' => 'April',
                            '5' => 'Mei',
                            '6' => 'Juni',
                            '7' => 'Juli',
                            '8' => 'Agustus',
                            '9' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember',
                        ])
                        ->default($this->month)
                        ->required(),
                    Select::make('year')
                        ->label('Tahun')
                        ->options(function () {
                            $currentYear = now()->year;
                            $years = range($currentYear - 5, $currentYear + 1);
                            return array_combine($years, $years);
                        })
                        ->default($this->year)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    // Validasi data sebelum redirect
                    $month = (int) ($data['month'] ?? now()->month);
                    $year = (int) ($data['year'] ?? now()->year);

                    if ($month < 1 || $month > 12) {
                        $month = now()->month;
                    }

                    if ($year < 2020 || $year > now()->year + 1) {
                        $year = now()->year;
                    }

                    $this->redirect(route('filament.admin.pages.attendance-user', [
                        'month' => $month,
                        'year' => $year,
                    ]));
                }),
            Action::make('pdf')
                ->color('warning')
                ->label('Laporan Absensi PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function (array $data) {
                    $month = (int) ($data['month'] ?? now()->month);
                    $year = (int) ($data['year'] ?? now()->year);

                    // Validasi input
                    if ($month < 1 || $month > 12) {
                        $month = now()->month;
                    }

                    if ($year < 2020 || $year > now()->year + 1) {
                        $year = now()->year;
                    }

                    return redirect()->to("/pdf/absensi/{$month}/{$year}");
                })->openUrlInNewTab()
                ->form(self::getMonthYearForm())
                ->modalSubmitActionLabel('Generate PDF')
                ->modalDescription('Pilih bulan dan tahun untuk generate PDF')
                ->visible(fn() => Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('Kaur Umum dan Perencanan')),

        ];
    }

    /**
     * Form untuk memilih bulan dan tahun
     */
    public static function getMonthYearForm(): array
    {
        return [
            Select::make('month')
                ->label('Bulan')
                ->options([
                    '1' => 'Januari',
                    '2' => 'Februari',
                    '3' => 'Maret',
                    '4' => 'April',
                    '5' => 'Mei',
                    '6' => 'Juni',
                    '7' => 'Juli',
                    '8' => 'Agustus',
                    '9' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember',
                ])
                ->default(now()->month)
                ->required(),
            Select::make('year')
                ->label('Tahun')
                ->options(function () {
                    $currentYear = now()->year;
                    $years = range($currentYear - 5, $currentYear + 1);
                    return array_combine($years, $years);
                })
                ->default(now()->year)
                ->required(),
        ];
    }

    /**
     * Menghitung jumlah hari kerja dalam bulan ini hingga hari ini
     *
     * @param int $month Bulan (1-12)
     * @param int $year Tahun
     * @return int Jumlah hari kerja
     */
    protected static function getWorkingDaysThisMonth($month, $year)
    {
        $today = Carbon::today();
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $today; // Hingga hari ini

        $totalWorkingDays = 0;

        while ($start <= $end) {
            if (!$start->isWeekend()) {
                $totalWorkingDays++;
            }
            $start->addDay();
        }

        return $totalWorkingDays;
    }
}
