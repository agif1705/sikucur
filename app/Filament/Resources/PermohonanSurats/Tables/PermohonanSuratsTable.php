<?php

namespace App\Filament\Resources\PermohonanSurats\Tables;

use App\Models\PermohonanSurat;
use App\Models\StatusSurat;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PermohonanSuratsTable
{
    public static function configure(Table $table): Table
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

                Tables\Columns\TextColumn::make('suratPengantar.korong')
                    ->label('Korong')
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('suratPengantar.waliKorong.name')
                    ->label('Diketahui Wali')
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status.nama_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (PermohonanSurat $record) => $record->status?->warna_status ?? 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_permohonan')
                    ->label('Tgl Permohonan')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_estimasi_selesai')
                    ->label('Estimasi Selesai')
                    ->date('d M Y')
                    ->color(function (PermohonanSurat $record) {
                        if (! $record->tanggal_estimasi_selesai) {
                            return 'gray';
                        }

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
                Action::make('viewPdf')
                    ->label('Lihat PDF')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (PermohonanSurat $record) => route('surat.permohonan.pdf', $record))
                    ->openUrlInNewTab(),

                Action::make('downloadPdf')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (PermohonanSurat $record) => route('surat.permohonan.download', $record))
                    ->openUrlInNewTab(),

                Action::make('viewPengantarPdf')
                    ->label('Pengantar')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->visible(fn (PermohonanSurat $record) => filled($record->surat_pengantar_id))
                    ->url(fn (PermohonanSurat $record) => route('surat.pengantar.pdf', $record->surat_pengantar_id))
                    ->openUrlInNewTab(),

                EditAction::make(),
                Action::make('updateStatus')
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_permohonan', 'desc');
    }
}
