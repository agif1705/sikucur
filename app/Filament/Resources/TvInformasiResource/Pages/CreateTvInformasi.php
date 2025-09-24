<?php

namespace App\Filament\Resources\TvInformasiResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TvInformasiResource;

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
