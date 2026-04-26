<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Models\AbsensiWebPegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AbsensiWebPegawaiResource\Pages;
use App\Filament\Resources\AbsensiWebPegawaiResource\RelationManagers;

class AbsensiWebPegawaiResource extends Resource
{
    protected static ?string $model = AbsensiWebPegawai::class;

    protected static string | \BackedEnum | null $navigationIcon = 'gmdi-whatsapp-o';
    protected static string | \UnitEnum | null $navigationGroup = 'Absensi';
    protected static ?string $heade = 'Absensi';
    protected static ?string $navigationLabel = 'Rekap Absensi WhatsApp';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('absensi')
                    ->options([
                        'HDLD' => 'Dinas Luar Daerah',
                        'HDDD' => 'Dinas Dalam Daerah',
                        'S' => 'Sakit',
                        'I' => 'Izin',
                        'C' => 'Cuti',
                    ]),
                Forms\Components\FileUpload::make('file_pendukung')
                    ->label('Foto Pendukung')
                    ->directory('izin')
                    ->image()
                    ->getUploadedFileNameForStorageUsing(function ($file): string {
                        $date = now()->format('Ymd');
                        $uuid = Str::uuid();
                        $name = Auth::user()->username;
                        $ext  = $file->getClientOriginalExtension(); // ambil extensi asli

                        return "izin-{$name}-{$date}-{$uuid}.{$ext}";
                    })->deleteUploadedFileUsing(function ($file) {
                        Storage::disk('public')->delete($file);
                    }),
                Forms\Components\TextInput::make('alasan')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('user_id', Auth::id()))
            ->columns([
                Tables\Columns\TextColumn::make('user.username')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('absensi')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('file_pendukung')
                    ->square(),
                Tables\Columns\TextColumn::make('time_in'),
                Tables\Columns\TextColumn::make('time_out'),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alasan')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
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
            'index' => Pages\ListAbsensiWebPegawais::route('/'),
            'create' => Pages\CreateAbsensiWebPegawai::route('/create'),
            'edit' => Pages\EditAbsensiWebPegawai::route('/{record}/edit'),
        ];
    }
}

