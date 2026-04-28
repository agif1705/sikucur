<?php

namespace App\Filament\Resources\DokumenPersyaratans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class DokumenPersyaratansTable
{
    public static function configure(Table $table): Table
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
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
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
}
