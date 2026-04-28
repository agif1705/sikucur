<?php

namespace App\Filament\Resources\TvInformasis\Pages;

use App\Filament\Resources\TvInformasis\TvInformasiResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTvInformasi extends CreateRecord
{
    protected static string $resource = TvInformasiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['nagari_id'] = Auth::user()->nagari->id;
        $data['user_id'] = Auth::user()->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // redirect ke list setelah create
        return $this->getResource()::getUrl('index');
    }
}
