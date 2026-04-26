<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Nagari;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\RekapAbsensiPegawai;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Services\SinkronFingerprintService;
use App\Filament\Resources\RekapAbsensiPegawaiResource\Pages;

class RekapAbsensiPegawaiResource extends Resource
{
    protected static ?string $model = RekapAbsensiPegawai::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user';
    protected static string | \UnitEnum | null $navigationGroup = 'Absensi';
    protected static ?string $navigationLabel = 'Rekap Absensi Bulanan';

    public static function form(Schema $form): Schema
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
                $query->where('user_id', Auth::user()->id);
                // ->whereMonth('date', $currentMonth)
                // ->whereYear('date', $currentYear);
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
                ->searchable(),
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
                ->searchable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])->headerActions([
                Action::make('create')
                    ->label('Sinkron FingerPrint Bulan ' . now()->format('F Y'))
                    ->action(fn() => static::sinkronFingerPrint())
                    ->button()
                    ->icon('heroicon-o-plus')
                    ->visible(fn() => Auth::user()->hasRole('super_admin')),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
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
    public static function sinkronFingerPrint(): void
    {
        $user = Auth::user();

        if (! $user?->nagari) {
            return;
        }

        SinkronFingerprintService::sinkronFingerPrint(Nagari::find($user->nagari->id));
    }
}

