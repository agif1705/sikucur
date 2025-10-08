<?php

namespace App\Filament\Resources\WhatsAppBroadcastResource\Pages;

use App\Filament\Resources\WhatsAppBroadcastResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWhatsAppBroadcasts extends ListRecords
{
    protected static string $resource = WhatsAppBroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
