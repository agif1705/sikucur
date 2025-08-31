<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\WdmsModel;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use App\Models\RekapAbsensiPegawai;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RekapAbsensiPegawaiResource\Pages;
use App\Filament\Resources\RekapAbsensiPegawaiResource\RelationManagers;

class RekapAbsensiPegawaiResource extends Resource
{
    protected static ?string $model = RekapAbsensiPegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?string $navigationLabel = 'Rekap Absensi Bulanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('nagari_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_late')
                    ->required(),
                Forms\Components\TextInput::make('status_absensi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sn_mesin')
                    ->maxLength(255),
                Forms\Components\TextInput::make('resource')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('id_resource')
                    ->maxLength(255),
                Forms\Components\TextInput::make('time_in')
                    ->required(),
                Forms\Components\TextInput::make('time_out'),
                Forms\Components\DatePicker::make('date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $is_super_admin = Auth::user()->hasRole('super_admin');
                $currentMonth = now()->month;
                $currentYear = now()->year;

                if (!$is_super_admin) {
                    $query->where('user_id', Auth::user()->id)
                        ->whereMonth('date', $currentMonth)
                        ->whereYear('date', $currentYear);
                } else {
                    $query->whereMonth('date', $currentMonth)
                        ->whereYear('date', $currentYear);
                }

                return $query->with(['user']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pegawai')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nagari.name')
                    ->label('Nagari')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_late')
                ->label('Tepat Waktu')
                    ->trueIcon('heroicon-o-clock')
                    ->falseIcon('heroicon-o-check-badge')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status_absensi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('resource')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_resource')
                    ->searchable(),
                Tables\Columns\TextColumn::make('time_in'),
                Tables\Columns\TextColumn::make('time_out'),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])->headerActions([
                Action::make('create')
                    ->label('Sinkron FingerPrint Bulan ' . now()->format('F Y'))
                    ->action(fn(User $user) => static::sinkronFingerPrint($user))
                    ->button()
                    ->icon('heroicon-o-plus')
                    ->visible(fn() => Auth::user()->hasRole('super_admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRekapAbsensiPegawais::route('/'),
            // 'create' => Pages\CreateRekapAbsensiPegawai::route('/create'),
            // 'edit' => Pages\EditRekapAbsensiPegawai::route('/{record}/edit'),
        ];
    }
    public static function sinkronFingerPrint(User $user)
    {
        $month = Carbon::now()->month;

        $sn_fingerprint = $user->with('nagari')->first()->nagari->sn_fingerprint;

        $absensi_masuk = WdmsModel::with('user')
            ->where('terminal_sn', $sn_fingerprint)
            ->whereTime('punch_time', '<=', '12:00')
            ->select('emp_id', 'punch_time', 'emp_code', 'id')
            ->get()
            ->sortBy('punch_time') // urutkan dari paling pagi
            ->groupBy(function ($item) {
                // Kelompokkan per user dan tanggal
                return $item->emp_id . '-' . Carbon::parse($item->punch_time)->format('Y-m-d');
            })
            ->map(function ($grouped) {
                // Ambil hanya absensi pertama
                $item = $grouped->first();
                $item->id = $item->id;
                $item->time_in = Carbon::parse($item->punch_time)->format('H:i');
                $item->date_in = Carbon::parse($item->punch_time)->format('Y-m-d');
                $item->user_id = $item->user->id;
                $item->nagari_id = $item->user->nagari->id;
                $item->sn_mesin = $item->user->nagari->sn_fingerprint;
                $item->is_late = $item->time_in > '08:00';
                return $item;
            })
            ->values();
        foreach ($absensi_masuk as $key => $value) {
            $check = RekapAbsensiPegawai::where('user_id', $value->user_id)
                ->whereDate('date', $value->date_in)
                ->first();
            if (!$check) {
                RekapAbsensiPegawai::create([
                    'user_id'        => $value->user_id,
                    'nagari_id'      => $value->nagari_id,
                    'is_late'        => $value['is_late'],
                    'sn_mesin'       => $value['sn_mesin'],
                    'status_absensi' => 'Hadir',
                    'resource'       => 'Fingerprint',
                    'id_resource'    => 'fp-' . $value->id,
                    'time_in'        => $value['time_in'],
                    'date'           => $value['date_in'],
                ]);
            }
        }
        Notification::make()
            ->title('Sinkron FingerPrint')
            ->body('suksess sinkron')
            ->success()
            ->duration(500)
            ->persistent()
            ->send();
        $today = Carbon::now()->format('Y-m-d');
        $absensi_pulang = WdmsModel::with('user')->where('terminal_sn', $sn_fingerprint)
            ->whereMonth('punch_time', $month)
            ->whereTime('punch_time', '>=', '12:00')
            ->select('emp_id', 'punch_time', 'emp_code')
            ->get()
            ->map(function ($item) {
                $item->time_out = \Carbon\Carbon::parse($item->punch_time)->format('H:i');
                $item->date_out = \Carbon\Carbon::parse($item->punch_time)->format('Y-m-d');
                $item->user_id = $item->user->id;
                $item->nagari_id = $item->user->nagari->id;
                $item->sn_mesin = $item->user->nagari->sn_fingerprint;
                if ($item->time_out > '16:00') {
                    $item->pulang = true;
                } else {
                    $item->pulang = false;
                }
                return $item;
            });
        foreach ($absensi_pulang as $key => $value) {
            $check = RekapAbsensiPegawai::where('user_id', $value->user_id)
                ->whereDate('date', $today)
                ->whereNotNull('time_out')
                ->exists();
            if ($check === false) {
                $update = RekapAbsensiPegawai::whereUserId($value->user_id)->update([
                    'time_out' => $value->time_out,
                ]);
            }
        }
    }
}
