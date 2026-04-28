<?php

namespace App\Filament\Resources\UploadDokumens\Tables;

use App\Models\UploadDokumen;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UploadDokumensTable
{
    public static function configure(Table $table): Table
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
                ViewAction::make(),

                Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (UploadDokumen $record) => ! $record->is_verified)
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

                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (UploadDokumen $record) => $record->file_url)
                    ->openUrlInNewTab(),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('verify_bulk')
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
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
