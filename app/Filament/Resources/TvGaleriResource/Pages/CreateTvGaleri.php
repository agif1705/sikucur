<?php

namespace App\Filament\Resources\TvGaleriResource\Pages;

use App\Filament\Resources\TvGaleriResource;
use Auth;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTvGaleri extends CreateRecord
{
    protected static string $resource = TvGaleriResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['nagari_id'] = Auth::user()->nagari->id;
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        // redirect ke list setelah create
        return $this->getResource()::getUrl('index');
    }
}
