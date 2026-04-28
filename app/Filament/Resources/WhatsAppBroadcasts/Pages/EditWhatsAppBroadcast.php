<?php

namespace App\Filament\Resources\WhatsAppBroadcasts\Pages;

use App\Filament\Resources\WhatsAppBroadcasts\WhatsAppBroadcastResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWhatsAppBroadcast extends EditRecord
{
    protected static string $resource = WhatsAppBroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
