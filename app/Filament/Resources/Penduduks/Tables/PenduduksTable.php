<?php

namespace App\Filament\Resources\Penduduks\Tables;

use App\Models\WaliKorong;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class PenduduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(static::columns())
            ->filters(static::filters())
            ->actions(static::actions())
            ->bulkActions(static::bulkActions())
            ->defaultSort('updated_at', 'desc');
    }

    private static function columns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Nama')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('nik')
                ->label('NIK')
                ->searchable()
                ->sortable(),

            Tables\Columns\IconColumn::make('jk')
                ->label('L/P')
                ->icon(fn (string $state): string => match ($state) {
                    '1' => 'gmdi-male',
                    '2' => 'gmdi-female-o',
                    default => 'heroicon-o-question-mark-circle',
                })
                ->color(fn (string $state): string => match ($state) {
                    '1' => 'success',
                    '2' => 'warning',
                    default => 'gray',
                }),

            Tables\Columns\TextColumn::make('korong')
                ->label('Korong')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('alamat_domisili')
                ->label('Domisili')
                ->limit(40)
                ->wrap()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('hotspotSikucur.mikrotikConfig.name')
                ->label('Wilayah Hotspot')
                ->searchable()
                ->placeholder('Belum terdaftar')
                ->badge()
                ->color('success')
                ->toggleable(),

            Tables\Columns\TextColumn::make('tanggal_lahir')
                ->label('Tanggal Lahir')
                ->date('d M Y')
                ->sortable()
                ->toggleable(),
        ];
    }

    private static function filters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('korong')
                ->label('Korong')
                ->options(fn () => static::korongOptions())
                ->searchable()
                ->preload(),
        ];
    }

    private static function actions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    private static function bulkActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ];
    }

    private static function korongOptions(): array
    {
        return WaliKorong::query()
            ->orderBy('wilayah')
            ->pluck('wilayah', 'wilayah')
            ->all();
    }
}
