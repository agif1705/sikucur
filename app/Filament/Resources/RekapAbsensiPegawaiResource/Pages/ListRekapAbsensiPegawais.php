<?php

namespace App\Filament\Resources\RekapAbsensiPegawaiResource\Pages;

use App\Filament\Resources\RekapAbsensiPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListRekapAbsensiPegawais extends ListRecords
{
    protected static string $resource = RekapAbsensiPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(), // Tab default → tampilkan semua data

            'terlambat' => Tab::make()
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->where('is_late', true) // hanya data dengan active = true
                ),

            'Ontime' => Tab::make()
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->where('is_late', false) // hanya data dengan active = false
                ),
        ];
    }
}
