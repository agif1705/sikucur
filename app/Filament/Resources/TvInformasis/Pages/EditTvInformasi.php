<?php

namespace App\Filament\Resources\TvInformasis\Pages;

use App\Filament\Resources\TvInformasis\TvInformasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTvInformasi extends EditRecord
{
    protected static string $resource = TvInformasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['nagari_id'] = Auth::user()->nagari->id;
        $data['user_id'] = Auth::id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // redirect ke list setelah create
        return $this->getResource()::getUrl('index');
    }
}
