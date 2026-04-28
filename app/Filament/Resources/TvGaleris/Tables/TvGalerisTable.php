<?php

namespace App\Filament\Resources\TvGaleris\Tables;

use App\Models\TvGaleri;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class TvGalerisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nagari.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Judul Foto Dari galeri')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->getStateUsing(fn (TvGaleri $record): ?string => $record->image ? ltrim($record->image, '/') : null)
                    ->square(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function ($record) {
                        // hapus file dari storage
                        if ($record->image) {
                            Storage::disk('public')->delete($record->image);
                        }

                        // kalau field multiple JSON
                        if (is_array($record->images ?? null)) {
                            foreach ($record->images as $file) {
                                Storage::disk('public')->delete($file);
                            }
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                // Hapus single image
                                if ($record->image) {
                                    Storage::disk('public')->delete($record->image);
                                }

                                // Kalau field multiple JSON
                                if (is_array($record->images ?? null)) {
                                    foreach ($record->images as $file) {
                                        Storage::disk('public')->delete($file);
                                    }
                                }
                            }
                        }),
                ]),
            ]);
    }
}
