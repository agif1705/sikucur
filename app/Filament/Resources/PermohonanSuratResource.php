<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermohonanSuratResource\Pages;
use App\Models\PermohonanSurat;
use App\Models\JenisSurat;
use App\Models\StatusSurat;
use App\Models\Nagari;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Builder;

class PermohonanSuratResource extends Resource
{
    protected static ?string $model = PermohonanSurat::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationLabel = 'Permohonan Surat';
    protected static ?string $navigationGroup = 'Manajemen Surat';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Permohonan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('nomor_permohonan')
                                    ->label('Nomor Permohonan')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Auto Generate'),

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

                                Forms\Components\Select::make('nagari_id')
                                    ->label('Nagari')
                                    ->relationship('nagari', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Forms\Components\DateTimePicker::make('tanggal_permohonan')
                            ->label('Tanggal Permohonan')
                            ->required()
                            ->default(now())
                            ->native(false),
                    ]),

                Section::make('Data Pemohon')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('pemohon_nik')
                                    ->label('NIK Pemohon')
                                    ->required()
                                    ->maxLength(16)
                                    ->minLength(16)
                                    ->numeric()
                                    ->placeholder('1234567890123456'),

                                Forms\Components\TextInput::make('pemohon_nama')
                                    ->label('Nama Pemohon')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Nama Lengkap'),
                            ]),

                        Forms\Components\Textarea::make('pemohon_alamat')
                            ->label('Alamat Pemohon')
                            ->required()
                            ->rows(3)
                            ->placeholder('Alamat lengkap pemohon'),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('pemohon_telepon')
                                    ->label('Telepon/HP')
                                    ->tel()
                                    ->placeholder('08xxxxxxxxxx'),

                                Forms\Components\TextInput::make('pemohon_email')
                                    ->label('Email')
                                    ->email()
                                    ->placeholder('email@example.com'),
                            ]),
                    ]),

                Section::make('Detail Permohonan')
                    ->schema([
                        Forms\Components\Textarea::make('keperluan')
                            ->label('Keperluan/Tujuan')
                            ->required()
                            ->rows(3)
                            ->placeholder('Jelaskan untuk keperluan apa surat ini digunakan'),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status_id')
                                    ->label('Status')
                                    ->relationship('status', 'nama_status')
                                    ->required()
                                    ->default(1) // Default status pertama
                                    ->disabled(fn (?PermohonanSurat $record) => $record === null),

                                Forms\Components\Select::make('petugas_id')
                                    ->label('Petugas')
                                    ->relationship('petugas', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih petugas yang menangani'),
                            ]),

                        Forms\Components\DateTimePicker::make('tanggal_estimasi_selesai')
                            ->label('Estimasi Selesai')
                            ->native(false),

                        Forms\Components\Textarea::make('catatan_petugas')
                            ->label('Catatan Petugas')
                            ->rows(3)
                            ->placeholder('Catatan dari petugas'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_permohonan')
                    ->label('No. Permohonan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor permohonan disalin!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('jenisSurat.nama_jenis')
                    ->label('Jenis Surat')
                    ->searchable()
                    ->wrap()
                    ->limit(30),

                Tables\Columns\TextColumn::make('pemohon_nama')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('status.nama_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (PermohonanSurat $record) => $record->status->warna_status)
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_permohonan')
                    ->label('Tgl Permohonan')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_estimasi_selesai')
                    ->label('Estimasi Selesai')
                    ->date('d M Y')
                    ->color(function (PermohonanSurat $record) {
                        if (!$record->tanggal_estimasi_selesai) return 'gray';
                        return $record->tanggal_estimasi_selesai->isPast() ? 'danger' : 'success';
                    }),

                Tables\Columns\TextColumn::make('petugas.name')
                    ->label('Petugas')
                    ->placeholder('Belum ditugaskan')
                    ->limit(20),

                Tables\Columns\TextColumn::make('nagari.name')
                    ->label('Nagari')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_surat_id')
                    ->label('Jenis Surat')
                    ->relationship('jenisSurat', 'nama_jenis')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Status')
                    ->relationship('status', 'nama_status')
                    ->preload(),

                Tables\Filters\SelectFilter::make('nagari_id')
                    ->label('Nagari')
                    ->relationship('nagari', 'name')
                    ->preload(),

                Tables\Filters\Filter::make('tanggal_permohonan')
                    ->label('Periode Permohonan')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_permohonan', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_permohonan', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status_id')
                            ->label('Status Baru')
                            ->options(StatusSurat::pluck('nama_status', 'id'))
                            ->required(),
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(3),
                    ])
                    ->action(function (PermohonanSurat $record, array $data): void {
                        $record->update([
                            'status_id' => $data['status_id'],
                            'catatan_petugas' => $data['catatan'],
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_permohonan', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonanSurats::route('/'),
            'create' => Pages\CreatePermohonanSurat::route('/create'),
            // 'view' => Pages\ViewPermohonanSurat::route('/{record}'),
            'edit' => Pages\EditPermohonanSurat::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereHas('status', function($query) {
            $query->where('kode_status', 'MASUK');
        })->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
