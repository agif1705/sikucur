<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DokumenPersyaratanResource\Pages;
use App\Models\DokumenPersyaratan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DokumenPersyaratanResource extends Resource
{
    protected static ?string $model = DokumenPersyaratan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Dokumen Persyaratan';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data Surat';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Dokumen')
                    ->schema([

                        Forms\Components\Select::make('jenis_surat_id')
                            ->label('Jenis Surat')
                            ->relationship('jenisSurat', 'nama_jenis')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([

                                Forms\Components\TextInput::make('nama_jenis')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('kode_surat')
                                    ->required()
                                    ->maxLength(10),
                            ]),

                        Grid::make(2)
                            ->schema([

                                Forms\Components\TextInput::make('nama_dokumen')
                                    ->label('Nama Dokumen')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Fotocopy KTP'),

                                Forms\Components\TextInput::make('urutan')
                                    ->label('Urutan')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->helperText('Urutan tampil dokumen'),
                            ]),

                        Forms\Components\Toggle::make('is_wajib')
                            ->label('Dokumen Wajib')
                            ->default(true)
                            ->helperText('Centang jika dokumen ini wajib dilengkapi'),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->placeholder('Keterangan tambahan tentang dokumen ini'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('jenisSurat.nama_jenis')
                    ->label('Jenis Surat')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('nama_dokumen')
                    ->label('Nama Dokumen')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('urutan')
                    ->label('Urutan')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_wajib')
                    ->label('Wajib')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('uploadDokumen_count')
                    ->label('Total Upload')
                    ->counts('uploadDokumen')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
            ])
            ->filters([

                Tables\Filters\SelectFilter::make('jenis_surat_id')
                    ->label('Jenis Surat')
                    ->relationship('jenisSurat', 'nama_jenis')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_wajib')
                    ->label('Status Wajib')
                    ->placeholder('Semua Status')
                    ->trueLabel('Wajib')
                    ->falseLabel('Opsional'),
            ])
            ->actions([

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('jenis_surat_id')
            ->defaultSort('urutan');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDokumenPersyaratans::route('/'),
            'create' => Pages\CreateDokumenPersyaratan::route('/create'),
            // 'view' => Pages\ViewDokumenPersyaratan::route('/{record}'),
            'edit' => Pages\EditDokumenPersyaratan::route('/{record}/edit'),
        ];
    }
}
