<?php

namespace App\Filament\Resources\TvGaleris\Pages;

use App\Filament\Resources\TvGaleris\TvGaleriResource;
use Auth;
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
