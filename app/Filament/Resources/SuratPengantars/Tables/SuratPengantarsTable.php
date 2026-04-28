<?php

namespace App\Filament\Resources\SuratPengantars\Tables;

use App\Filament\Resources\PermohonanSurats\PermohonanSuratResource;
use App\Models\SuratPengantar;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class SuratPengantarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pemohon_nama')
                    ->label('Pemohon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('korong')
                    ->label('Korong')
                    ->searchable(),
                Tables\Columns\TextColumn::make('waliKorong.name')
                    ->label('Wali Korong')
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('permohonanSurat.nomor_permohonan')
                    ->label('Permohonan')
                    ->placeholder('Belum dibuat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pengantar')
                    ->label('Tanggal')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('previewPdf')
                    ->label('Lihat PDF')
                    ->icon('heroicon-o-eye')
                    ->url(fn (SuratPengantar $record) => route('surat.pengantar.pdf', $record))
                    ->openUrlInNewTab(),
                Action::make('buatSurat')
                    ->label('Buat Surat')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->visible(fn (SuratPengantar $record) => $record->status === SuratPengantar::STATUS_SUBMITTED && ! $record->permohonanSurat()->exists())
                    ->url(fn (SuratPengantar $record) => PermohonanSuratResource::getUrl('create', [
                        'surat_pengantar_id' => $record->id,
                    ])),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
