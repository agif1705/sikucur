<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatusSuratResource\Pages;
use App\Models\StatusSurat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class StatusSuratResource extends Resource
{
    protected static ?string $model = StatusSurat::class;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationLabel = 'Status Surat';
    protected static ?string $navigationGroup = 'Master Data Surat';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama_status')
                                    ->label('Nama Status')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Permohonan Masuk'),

                                Forms\Components\TextInput::make('kode_status')
                                    ->label('Kode Status')
                                    ->required()
                                    ->maxLength(5)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Contoh: MASUK')
                                    ->uppercase(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('warna_status')
                                    ->label('Warna Status')
                                    ->required()
                                    ->options([
                                        'primary' => 'Primary (Biru)',
                                        'secondary' => 'Secondary (Abu-abu)',
                                        'success' => 'Success (Hijau)',
                                        'danger' => 'Danger (Merah)',
                                        'warning' => 'Warning (Kuning)',
                                        'info' => 'Info (Biru Muda)',
                                        'light' => 'Light (Putih)',
                                        'dark' => 'Dark (Hitam)',
                                    ])
                                    ->default('primary'),

                                Forms\Components\TextInput::make('urutan')
                                    ->label('Urutan')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->helperText('Urutan proses status'),
                            ]),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->placeholder('Deskripsi detail tentang status ini'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('urutan')
                    ->label('No')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kode_status')
                    ->label('Kode')
                    ->badge()
                    ->color(fn(StatusSurat $record) => $record->warna_status)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_status')
                    ->label('Nama Status')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('permohonanSurat_count')
                    ->label('Permohonan')
                    ->counts('permohonanSurat')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('urutan')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatusSurats::route('/'),
            'create' => Pages\CreateStatusSurat::route('/create'),
            // 'view' => Pages\ViewStatusSurat::route('/{record}'),
            'edit' => Pages\EditStatusSurat::route('/{record}/edit'),
        ];
    }
}
