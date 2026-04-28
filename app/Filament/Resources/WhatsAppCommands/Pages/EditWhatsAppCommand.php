<?php

namespace App\Filament\Resources\WhatsAppCommands\Pages;

use App\Filament\Resources\WhatsAppCommands\WhatsAppCommandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWhatsAppCommand extends EditRecord
{
    protected static string $resource = WhatsAppCommandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
