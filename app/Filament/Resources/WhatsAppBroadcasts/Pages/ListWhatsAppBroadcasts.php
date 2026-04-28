<?php

namespace App\Filament\Resources\WhatsAppBroadcasts\Pages;

use App\Filament\Resources\WhatsAppBroadcasts\WhatsAppBroadcastResource;
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
