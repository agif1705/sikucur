<?php

namespace App\Filament\Resources\WhatsAppCommands\Pages;

use App\Filament\Resources\WhatsAppCommands\WhatsAppCommandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWhatsAppCommands extends ListRecords
{
    protected static string $resource = WhatsAppCommandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
