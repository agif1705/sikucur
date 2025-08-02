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
use Livewire\Attributes\Reactive;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Concerns\InteractsWithTable;
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
    public $bulan, $tahun;


    public function mount(): void
    {
        $this->month = request()->query('month', now()->month);
        $this->bulan = Carbon::create($this->year, $this->month, 1)->monthName;
        $this->year = request()->query('year', now()->year);
    }
    public function getHeading(): string
    {
        return 'Absensi Pegawai Nagari ' . auth()->user()->nagari->name;
    }

    public function table(Table $table): Table
    {
        $startDate = Carbon::create($this->year, $this->month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Generate columns for each day in month
        $dayColumns = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($this->year, $this->month, $day);
            $dateString = $currentDate->format('Y-m-d');
            $dayColumns[] = TextColumn::make($currentDate->format('Y-m-d'))
                ->label($day)
                ->getStateUsing(function (User $record) use ($currentDate) {
                    $attendance = $record->absensiPegawai()->whereDate('date_in', $currentDate)->first();
                    if (!$attendance) {
                        return 'A';
                    }
                    if ($attendance->nagari->workDays()->where('day', $currentDate->format('l'))->first()->is_working_day == false) {
                        return 'L';
                    }
                    $absensi = match ($attendance->absensi) {
                        'Hadir' => 'H',
                        'Hadir Dinas Luar Daerah' => 'HDL',
                        'Hadir Dinas Dalam Daerah' => 'HDD',
                        'sakit' => 'S',
                        'cuti' => 'C',
                        'Izin' => 'I',
                        default => 'A', // Fallback jika status tidak dikenali
                    };
                    return $absensi;
                })
                ->color(fn(string $state): string => match ($state) {

                    'L' => 'danger',
                    'H' => 'success',
                    'HDL' => 'success',
                    'HDD' => 'success',
                    'A' => 'danger',
                    'S' => 'danger',
                    'I' => 'primary',
                })->icon(fn(string $state): string => match ($state) {

                    'L' => 'heroicon-o-calendar',
                    'H' => 'heroicon-o-check-circle',
                    'HDL' => 'heroicon-o-check-circle',
                    'HDD' => 'heroicon-o-check-circle',
                    'A' => 'heroicon-o-x-circle',
                    'I' => 'heroicon-o-information-circle',
                    'S' => 'heroicon-o-x-circle',
                })
                ->alignCenter();
        }


        return $table
            ->query(User::query()->where('id', '!=', 1)->with(['absensiPegawai' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date_in', [$startDate, $endDate]);
            }]))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('User Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_kantor')
                    ->label('Kantor')
                    ->getStateUsing(function ($record) use ($startDate, $endDate) {
                        return $record->absensiPegawai
                            ->where('absensi', 'Hadir')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->count();
                    }),
                Tables\Columns\TextColumn::make('total_hadir_dinas_luar')
                    ->label('Luar Daerah')
                    ->getStateUsing(function ($record) use ($startDate, $endDate) {
                        return $record->absensiPegawai
                            ->where('absensi', 'Hadir Dinas Luar Daerah')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->count();
                    }),
                Tables\Columns\TextColumn::make('total_hadir_dinas_dalam')
                    ->label('Dalam Daerah')
                    ->getStateUsing(function ($record) use ($startDate, $endDate) {
                        return $record->absensiPegawai
                            ->where('absensi', 'Hadir Dinas Dalam Daerah')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->count();
                    }),

                Tables\Columns\TextColumn::make('total_izin')
                    ->label('Total Izin')
                    ->getStateUsing(function ($record) use ($startDate, $endDate) {

                        return $record->absensiPegawai
                            ->where('absensi', 'Izin')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->count();
                    })
                    ->color(fn($state) => $state > 0 ? 'primary' : 'success')
                    ->icon(fn($state) => $state > 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle'),
                Tables\Columns\TextColumn::make('total_absent')
                    ->label('Total Alpha')
                    ->getStateUsing(function (User $record) use ($daysInMonth) {
                        $count = 0;

                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            $currentDate = Carbon::create($this->year, $this->month, $day);
                            $attendance = $record->absensiPegawai()->whereDate('created_at', $currentDate)->first();

                            if (!$attendance) {
                                $count++; // Hitung sebagai A
                                continue;
                            }

                            if ($attendance->nagari->workDays()->where('day', $currentDate->format('l'))->first()->is_working_day == false) {
                                continue;
                            }

                            if ($attendance->absensi === null || $attendance->absensi === 'Absen') {
                                $count++;
                            }
                        }
                        if ($count = $daysInMonth) {
                            $count = 0;
                        }
                        return $count;
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
}
