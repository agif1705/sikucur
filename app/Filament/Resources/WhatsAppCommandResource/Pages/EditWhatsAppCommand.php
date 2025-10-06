<?php

namespace App\Filament\Resources\WhatsAppCommandResource\Pages;

use App\Filament\Resources\WhatsAppCommandResource;
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
