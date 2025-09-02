<?php

namespace App\Filament\Pages;

use Carbon\Month;
use Carbon\Carbon;
use App\Models\User;
use Filament\Tables;
use Carbon\CarbonPeriod;
use Filament\Pages\Page;
use App\Models\Attendance;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Termwind\Components\Dd;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Livewire\Attributes\Reactive;
use App\Models\RekapAbsensiPegawai;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use App\Models\AbsensiGabunganFakeModel;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use App\Services\BulananAbsensiPegawaiService;
use Illuminate\Pagination\LengthAwarePaginator;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Components\RepeatableEntry;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class AttendaceUser extends Page implements HasTable
{
    use InteractsWithTable;
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.attendace-user';
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?string $navigationLabel = 'Absensi Bulanan';

    public $month;
    public $year;
    public $bulan, $tahun, $getAbsensi;


    public function mount(): void
    {
        $this->month = request()->query('month', now()->month);
        $this->bulan = Carbon::create($this->year, $this->month, 1)->monthName;
        $this->year = request()->query('year', now()->year);
    }
    public function getHeading(): string
    {
        return 'Absensi Pegawai Nagari ' . Auth::user()->nagari->name;
    }

    public function table(Table $table): Table
    {
        $startDate = Carbon::create($this->year, $this->month, 1);
        $endDate   = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Generate columns untuk setiap hari
        $dayColumns = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($this->year, $this->month, $day);
            $dayName     = strtolower($currentDate->format('l')); // ✅ lowercase

            $dayColumns[] = TextColumn::make($currentDate->format('Y-m-d'))
                ->label($day)
                ->getStateUsing(function ($record) use ($currentDate, $dayName) {
                    // 1️⃣ cek absensi rekap
                    $attendance = $record->rekapAbsensiPegawai
                        ->where('date', $currentDate->toDateString())
                        ->first();

                    if ($attendance) {
                        return match ($attendance->status_absensi) {
                        'Hadir' => 'H',
                        'HDLD'  => 'HDLD',
                        'HDDD'  => 'HDDD',
                        'Sakit' => 'S',
                        'Cuti'  => 'C',
                        'Izin'  => 'I',
                        default => 'A',
                        };
                    }

                    // 2️⃣ kalau gak ada data → cek tabel work_days nagari
                    $workDay = $record->nagari
                        ->workDays()
                        ->where('day', $dayName) // lowercase cocok
                        ->first();

                    if ($workDay && !$workDay->is_working_day) {
                        return 'L'; // Libur
                    }

                    return 'A'; // default Alpha
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
                    'H', 'HDLD', 'HDD' => 'heroicon-o-check-circle',
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
                    // ->where('id', '!=', 1)
                    ->with(['rekapAbsensiPegawai' => function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('date', [$startDate, $endDate]);
                    }])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('User Name')
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
                ->getStateUsing(function ($record) use ($daysInMonth, $startDate, $endDate) {
                    // ambil semua data absensi bulan ini
                    $hari_kerja = $this->getWorkingDaysThisMonth(now()->month, now()->year);
                    $rekap = new RekapAbsensiPegawai();
                    $holidays = $rekap->Holiday(now()->month, now()->year);
                    $total_hari_kerja = $hari_kerja - $holidays;
                // dd($total_hari_kerja);
                // Alpha = hari kerja - semua absensi valid
                return  $total_hari_kerja - $record->rekapAbsensiPegawai
                    ->whereIn('status_absensi', ['Hadir', 'HDLD', 'HDDD', 'Sakit', 'Cuti', 'Izin'])->whereBetween('date', [$startDate, $endDate])
                    ->count();
                })
                ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                ->icon(fn($state) => $state > 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->alignCenter(),

                ...$dayColumns,
            ])->paginated(false)
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filter bulan dan tahun
            ])
            ->actions([
                // Actions jika diperlukan

            ])
            ->bulkActions([
                // Bulk actions jika diperlukan
            ]);
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->form([
                    Select::make('month')
                        ->options([
                            '1' => 'January',
                            '2' => 'February',
                            '3' => 'maret',
                            '4' => 'april',
                            '5' => 'mei',
                            '6' => 'juni',
                            '7' => 'juli',
                            '8' => 'agustus',
                            '9' => 'september',
                            '10' => 'oktober',
                            '11' => 'november',
                            '12' => 'December',
                        ])
                        ->default($this->month),
                    Select::make('year')
                        ->options(function () {
                            $years = range(now()->year - 5, now()->year + 5);
                            return array_combine($years, $years);
                        })
                        ->default($this->year),
                ])
                ->action(function (array $data): void {
                    $this->redirect(route('filament.admin.pages.attendace-user', [
                        'month' => $data['month'],
                        'year' => $data['year'],
                    ]));
                }),
            Action::make('pdf')
                ->color('warning')
                ->label('Laporan Absensi Pdf')
                ->action(function (array $data) {
                    $month = $data['month'];
                    $year = now()->year;

                    return redirect()->to("/pdf/absensi/{$month}/{$year}");
                })->openUrlInNewTab()
                ->form(self::getMonthYearForm())
                ->modalSubmitActionLabel('Generate PDF')
                ->modalDescription('Pilih bulan dan tahun untuk generate PDF')

        ];
    }
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
        ];
    }
    protected static function getWorkingDaysThisMonth($month, $year)
    {
        $today = Carbon::today();
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $today; // Hingga kemarin

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
