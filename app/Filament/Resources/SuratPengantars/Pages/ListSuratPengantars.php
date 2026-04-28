<?php

namespace App\Filament\Resources\SuratPengantars\Pages;

use App\Filament\Resources\SuratPengantars\SuratPengantarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratPengantars extends ListRecords
{
    protected static string $resource = SuratPengantarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadTemplate')
                ->label('Unduh Template Pengantar')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('surat.pengantar.template'))
                ->openUrlInNewTab(),
            Actions\CreateAction::make(),
        ];
    }
}
