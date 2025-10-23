<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\JenisSurat;
use Filament\Tables\Table;
use App\Models\MetaJenisSurat;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Section;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\ViewField;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use FilamentTiptapEditor\Enums\TiptapOutput;
use Filament\Infolists\Components\KeyValueEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JenisSuratResource\Pages;
use App\Filament\Resources\JenisSuratResource\RelationManagers;

class JenisSuratResource extends Resource
{
    protected static ?string $model = JenisSurat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Section::make('📋 Petunjuk Template')
                        ->schema([
                            KeyValue::make('meta_placeholder')
                                ->label('Daftar Placeholder')
                                ->keyLabel('Placeholder')
                                ->valueLabel('Deskripsi')
                                ->formatStateUsing(function ($state) {
                                    return $state ?: \App\Models\MetaJenisSurat::query()
                                        ->where('is_active', true)
                                        ->orderBy('category')
                                        ->orderBy('name')
                                        ->pluck('description', 'name')
                                        ->toArray();
                                })
                                ->dehydrated(false)
                                ->addable(false)
                                ->deletable(false)
                                ->editableKeys(false)
                                ->editableValues(false)
                                ->columnSpanFull()
                                ->helperText('💡 Salin placeholder dan gunakan dalam template')
                        ])
                        ->description('Daftar placeholder yang dapat digunakan')
                        ->collapsible()
                        ->persistCollapsed()
                        ->grow(false),

                    Section::make('✏️ Editor Template')
                        ->schema([
                            Forms\Components\TextInput::make('nama_jenis')
                                ->label('Nama Jenis Surat')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Contoh: Surat Keterangan Domisili'),

                            Forms\Components\TextInput::make('kode_surat')
                                ->label('Kode Surat')
                                ->required()
                                ->maxLength(20)
                                ->placeholder('Contoh: SKD')
                                ->helperText('Kode untuk identifikasi surat'),

                            TiptapEditor::make('template')
                                ->dehydrated(true)
                                ->label('Template Surat')
                                ->required()
                                ->columnSpanFull()
                                ->output(TiptapOutput::Html)
                                ->profile('full')
                                ->tools([
                                    'heading',
                                    'bullet-list',
                                    'ordered-list',
                                    'checked-list',
                                    'blockquote',
                                    'hr',
                                    'bold',
                                    'italic',
                                    'strike',
                                    'underline',
                                    'superscript',
                                    'subscript',
                                    'align-left',
                                    'align-center',
                                    'align-right',
                                    'align-justify',
                                    'link',
                                    'media',
                                    'table',
                                    'grid',
                                    'grid-builder',
                                    'undo',
                                    'redo',
                                    'source',
                                ])
                                ->placeholder('Masukkan template surat di sini...')
                                ->helperText('Gunakan placeholder dari panel kiri untuk data dinamis'),
                        ])
                        ->grow(),
                ])->from('md')->columnSpanFull(),

                Section::make('⚙️ Pengaturan Surat')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('estimasi_hari')
                                    ->label('Estimasi Penyelesaian')
                                    ->required()
                                    ->numeric()
                                    ->default(3)
                                    ->suffix('hari')
                                    ->minValue(1)
                                    ->helperText('Estimasi waktu penyelesaian surat'),

                                Forms\Components\Toggle::make('mandiri')
                                    ->label('Surat Mandiri')
                                    ->helperText('Dapat diajukan tanpa verifikasi')
                                    ->default(false),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->required(),
                            ]),

                        Forms\Components\Textarea::make('persyaratan')
                            ->label('Persyaratan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Tuliskan persyaratan yang diperlukan untuk mengajukan surat ini'),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Keterangan tambahan tentang jenis surat ini'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_jenis')
                    ->searchable(),
                Tables\Columns\IconColumn::make('mandiri')
                    ->boolean()
                    ->label('Mandiri')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kode_surat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estimasi_hari')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListJenisSurats::route('/'),
            'create' => Pages\CreateJenisSurat::route('/create'),
            'edit' => Pages\EditJenisSurat::route('/{record}/edit'),
        ];
    }
}
