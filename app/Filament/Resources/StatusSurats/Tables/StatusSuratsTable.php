<?php

namespace App\Filament\Resources\StatusSurats\Tables;

use App\Models\StatusSurat;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class StatusSuratsTable
{
    public static function configure(Table $table): Table
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
                    ->color(fn (StatusSurat $record) => $record->warna_status)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_status')
                    ->label('Nama Status')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('permohonanSurat_count')
                    ->label('Permohonan')
                    ->counts('permohonanSurat')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('urutan')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
