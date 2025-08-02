<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\WdmsModel;
use Filament\Tables\Table;
use App\Models\AbsensiPegawai;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AbsensiPegawaiResource\Pages;
use App\Filament\Resources\AbsensiPegawaiResource\RelationManagers;

class AbsensiPegawaiResource extends Resource
{
    protected static ?string $model = AbsensiPegawai::class;

    protected static ?string $navigationLabel = 'Absensi Pegawai';
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?string $navigationIcon = 'heroicon-c-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('absensi_by')
                    ->maxLength(255),
                Forms\Components\TextInput::make('absensi'),
                Forms\Components\TextInput::make('status_absensi'),
                Forms\Components\TextInput::make('keterangan_absensi')
                    ->maxLength(255),
                Forms\Components\Toggle::make('accept'),
                Forms\Components\TextInput::make('accept_by')
                    ->maxLength(255),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('nagari_id')
                    ->relationship('nagari', 'name')
                    ->required(),
                Forms\Components\TextInput::make('time_in')
                    ->required(),
                Forms\Components\TextInput::make('time_out'),
                Forms\Components\DatePicker::make('date_in')
                    ->required(),
                Forms\Components\DatePicker::make('date_out'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $bulan = Carbon::now()->month;
        return $table
            ->paginated(false)
            ->modifyQueryUsing(function (Builder $query) {
                $is_super_admin = auth()->user()->hasRole('super_admin');
                $currentMonth = now()->month;
                $currentYear = now()->year;

                if (!$is_super_admin) {
                    $query->where('user_id', auth()->user()->id)
                        ->whereMonth('date_in', $currentMonth)
                        ->whereYear('date_in', $currentYear);
                } else {
                    $query->whereMonth('date_in', $currentMonth)
                        ->whereYear('date_in', $currentYear);
                }

                return $query->with(['user']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric(),
                Tables\Columns\TextColumn::make('absensi_by'),
                Tables\Columns\TextColumn::make('absensi')->label('status'),
                Tables\Columns\TextColumn::make('status_absensi'),
                Tables\Columns\TextColumn::make('time_in'),
                Tables\Columns\TextColumn::make('time_out'),
                Tables\Columns\TextColumn::make('date_in')
                    ->date(),
                Tables\Columns\TextColumn::make('date_out')
                    ->date(),
                Tables\Columns\TextColumn::make('keterangan_absensi'),
                Tables\Columns\IconColumn::make('accept')
                    ->boolean(),
                Tables\Columns\TextColumn::make('accept_by')
            ])->defaultSort('date_in', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])->headerActions([
                Action::make('create')
                    ->label('Sinkron FingerPrint Bulan ' . now()->format('F Y'))
                    ->action(fn(User $user) => static::sinkronFingerPrint($user))
                    ->button()
                    ->icon('heroicon-o-plus'),
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
    public static function sinkronFingerPrint(User $user)
    {
        $month = Carbon::now()->month;
        $today = Carbon::now()->format('Y-m-d');
        $sn_fingerprint = $user->with('nagari')->first()->nagari->sn_fingerprint;
        $absensi_masuk = WdmsModel::with('user')->where('terminal_sn', $sn_fingerprint)
            ->whereMonth('punch_time', $month)
            ->whereTime('punch_time', '<=', '12:00')
            ->select('emp_id', 'punch_time', 'emp_code')
            ->get()
            ->map(function ($item) {
                $item->time_in = \Carbon\Carbon::parse($item->punch_time)->format('H:i');
                $item->date_in = \Carbon\Carbon::parse($item->punch_time)->format('Y-m-d');
                $item->user_id = $item->user->id;
                $item->nagari_id = $item->user->nagari->id;
                $item->sn_mesin = $item->user->nagari->sn_fingerprint;
                if ($item->time_in > '08:00') {
                    $item->is_late = true;
                } else {
                    $item->is_late = false;
                }
                return $item;
            });
        foreach ($absensi_masuk as $key => $value) {
            $check = AbsensiPegawai::where('user_id', $value->user->id)
                ->whereDate('date_in', $value->date_in)
                ->exists();
            if ($check == false) {
                AbsensiPegawai::create([
                    'absensi_by' => 'fingerprint',
                    'absensi' => 'Hadir',
                    'status_absensi' => $value->is_late == true ? 'Terlambat' : 'Ontime',
                    'sn_mesin' => $value->sn_mesin,
                    'user_id' => $value->user_id,
                    'emp_id' => $value->emp_code ?? $value->emp_id,
                    'nagari_id' => $value->nagari_id,
                    'time_in' => $value->time_in,
                    'date_in' => $value->date_in,
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
            $check = AbsensiPegawai::where('user_id', $value->user->id)
                ->whereDate('date_in', $today)
                ->whereNotNull('time_out')
                ->exists();
            if ($check === false) {
                $update = AbsensiPegawai::whereUserId($value->user->id)->whereDateIn($value->date_out)->update([
                    'time_out' => $value->time_out,
                    'date_out' => $value->date_out,
                ]);
            }
        }
        // dd($absensi_pulang);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensiPegawais::route('/'),
            'create' => Pages\CreateAbsensiPegawai::route('/create'),
            'edit' => Pages\EditAbsensiPegawai::route('/{record}/edit'),
        ];
    }
}
