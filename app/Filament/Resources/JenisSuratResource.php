<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\JenisSurat;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use App\Filament\Resources\JenisSuratResource\Pages;

class JenisSuratResource extends Resource
{
    protected static ?string $model = JenisSurat::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Jenis Surat';
    protected static ?string $navigationGroup = 'Master Data Surat';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama_jenis')
                                    ->label('Nama Jenis Surat')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Surat Keterangan Domisili'),

                                Forms\Components\TextInput::make('kode_surat')
                                    ->label('Kode Surat')
                                    ->required()
                                    ->maxLength(10)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Contoh: SKD')
                                    ->alphaDash(),
                            ]),

                        Forms\Components\TextInput::make('estimasi_hari')
                            ->label('Estimasi Hari Penyelesaian')
                            ->required()
                            ->numeric()
                            ->default(3)
                            ->minValue(1)
                            ->maxValue(30)
                            ->suffix('hari'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Aktifkan/nonaktifkan jenis surat ini'),
                    ]),

                Section::make('Detail Surat')
                    ->schema([
                        Forms\Components\Textarea::make('persyaratan')
                            ->label('Persyaratan Umum')
                            ->rows(3)
                            ->placeholder('Contoh: KTP, KK, Pas Foto 3x4')
                            ->helperText('Daftar persyaratan secara umum'),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->placeholder('Keterangan tambahan tentang jenis surat ini'),

                        Forms\Components\TextInput::make('template_path')
                            ->label('Path Template')
                            ->placeholder('templates/surat/domisili.blade.php')
                            ->helperText('Path ke file template surat (opsional)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_surat')
                    ->label('Kode')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_jenis')
                    ->label('Nama Jenis Surat')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('estimasi_hari')
                    ->label('Estimasi')
                    ->suffix(' hari')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('dokumenPersyaratan_count')
                    ->label('Dokumen')
                    ->counts('dokumenPersyaratan')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('permohonanSurat_count')
                    ->label('Total Permohonan')
                    ->counts('permohonanSurat')
                    ->badge()
                    ->color('success')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama_jenis');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJenisSurats::route('/'),
            'create' => Pages\CreateJenisSurat::route('/create'),
            'view' => Pages\ViewJenisSurat::route('/{record}'),
            'edit' => Pages\EditJenisSurat::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
}