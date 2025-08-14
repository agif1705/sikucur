<?php

namespace App\Filament\Resources\ListYoutubeResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ListYoutubeResource;

class CreateListYoutube extends CreateRecord
{
    protected static string $resource = ListYoutubeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['nagari_id'] = Auth::user()->nagari->id;
        return $data;
    }
}
