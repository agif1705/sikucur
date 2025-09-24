<?php

namespace App\Filament\Resources\NagariResource\Pages;

use App\Filament\Resources\NagariResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNagari extends EditRecord
{
    protected static string $resource = NagariResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        // redirect ke list setelah create
        return $this->getResource()::getUrl('index');
    }
}
