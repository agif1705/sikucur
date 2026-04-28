<?php

namespace App\Filament\Resources\VideoTvs\Pages;

use App\Filament\Resources\VideoTvs\VideoTvResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateVideoTv extends CreateRecord
{
    protected static string $resource = VideoTvResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['nagari_id'] = Auth::user()->nagari->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
