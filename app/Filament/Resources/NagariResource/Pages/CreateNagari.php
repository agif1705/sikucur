<?php

namespace App\Filament\Resources\NagariResource\Pages;

use App\Filament\Resources\NagariResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNagari extends CreateRecord
{
    protected static string $resource = NagariResource::class;
    protected function getRedirectUrl(): string
    {
        // redirect ke list setelah create
        return $this->getResource()::getUrl('index');
    }
}
