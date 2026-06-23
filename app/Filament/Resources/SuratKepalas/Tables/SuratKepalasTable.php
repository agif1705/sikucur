<?php

namespace App\Filament\Resources\SuratKepalas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuratKepalasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nagari.name')
                    ->label('Nagari')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->height(40),
                TextColumn::make('kop_surat')
                    ->label('Kop Surat')
                    ->html()
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
