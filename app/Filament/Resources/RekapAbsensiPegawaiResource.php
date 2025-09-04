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
use App\Models\Nagari;
use App\Services\SinkronFingerprintService;

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
            ])->defaultSort('created_at', 'desc')
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

        $nagari = $user->with('nagari')->first()->nagari;
        $sinkron = SinkronFingerprintService::sinkronFingerPrint(Nagari::find($nagari->id));
    }
}
