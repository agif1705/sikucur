<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\UploadDokumen;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UploadDokumenResource\Pages;

class UploadDokumenResource extends Resource
{
    protected static ?string $model = UploadDokumen::class;
    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';
    protected static ?string $navigationLabel = 'Upload Dokumen';
    protected static ?string $navigationGroup = 'Manajemen Surat';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Upload')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('permohonan_id')
                                    ->label('Permohonan Surat')
                                    ->relationship('permohonanSurat', 'nomor_permohonan')
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('dokumen_persyaratan_id')
                                    ->label('Dokumen Persyaratan')
                                    ->relationship('dokumenPersyaratan', 'nama_dokumen')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Forms\Components\FileUpload::make('file_path')
                            ->label('File Dokumen')
                            ->required()
                            ->directory('dokumen-surat')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(5120) // 5MB
                            ->downloadable()
                            ->previewable(),
                    ]),

                Section::make('Verifikasi Dokumen')
                    ->schema([
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Status Verifikasi')
                            ->default(false)
                            ->reactive(),

                        Forms\Components\Select::make('verified_by')
                            ->label('Diverifikasi Oleh')
                            ->relationship('verifiedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('is_verified')),

                        Forms\Components\DateTimePicker::make('verified_at')
                            ->label('Tanggal Verifikasi')
                            ->default(now())
                            ->visible(fn (Forms\Get $get) => $get('is_verified')),

                        Forms\Components\Textarea::make('catatan_verifikasi')
                            ->label('Catatan Verifikasi')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('is_verified')),

                        Forms\Components\Textarea::make('catatan_ditolak')
                            ->label('Catatan Penolakan')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('is_verified') === false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('permohonanSurat.nomor_permohonan')
                    ->label('No. Permohonan')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('permohonanSurat.pemohon_nama')
                    ->label('Pemohon')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('dokumenPersyaratan.nama_dokumen')
                    ->label('Jenis Dokumen')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('file_name')
                    ->label('File')
                    ->limit(30)
                    ->tooltip(fn (UploadDokumen $record) => $record->file_name),

                Tables\Columns\TextColumn::make('formatted_file_size')
                    ->label('Ukuran')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verifikasi')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),

                Tables\Columns\TextColumn::make('verifiedBy.name')
                    ->label('Diverifikasi Oleh')
                    ->placeholder('Belum diverifikasi')
                    ->limit(20),

                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Tgl Verifikasi')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Upload')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('permohonan_id')
                    ->label('Permohonan')
                    ->relationship('permohonanSurat', 'nomor_permohonan')
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Status Verifikasi')
                    ->placeholder('Semua Status')
                    ->trueLabel('Terverifikasi')
                    ->falseLabel('Belum Verifikasi'),

                Tables\Filters\SelectFilter::make('verified_by')
                    ->label('Diverifikasi Oleh')
                    ->relationship('verifiedBy', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (UploadDokumen $record) => !$record->is_verified)
                    ->form([
                        Forms\Components\Textarea::make('catatan_verifikasi')
                            ->label('Catatan Verifikasi')
                            ->rows(3)
                            ->placeholder('Tambahkan catatan verifikasi...'),
                    ])
                    ->action(function (UploadDokumen $record, array $data): void {
                        $record->update([
                            'is_verified' => true,
                            'verified_by' => Auth::user()->id,
                            'verified_at' => now(),
                            'catatan_verifikasi' => $data['catatan_verifikasi'],
                        ]);
                    }),

                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (UploadDokumen $record) => $record->file_url)
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('verify_bulk')
                        ->label('Verifikasi Massal')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\Textarea::make('catatan_verifikasi')
                                ->label('Catatan Verifikasi')
                                ->rows(3),
                        ])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_verified' => true,
                                    'verified_by' => Auth::user()->id,
                                    'verified_at' => now(),
                                    'catatan_verifikasi' => $data['catatan_verifikasi'],
                                ]);
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUploadDokumens::route('/'),
            'create' => Pages\CreateUploadDokumen::route('/create'),
            // 'view' => Pages\ViewUploadDokumen::route('/{record}'),
            'edit' => Pages\EditUploadDokumen::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_verified', false)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}