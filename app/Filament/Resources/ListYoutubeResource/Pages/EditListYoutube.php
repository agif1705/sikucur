<?php

namespace App\Filament\Resources\ListYoutubeResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ListYoutubeResource;

class EditListYoutube extends EditRecord
{
    protected static string $resource = ListYoutubeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['nagari_id'] = Auth::user()->nagari->id;
        return $data;
    }
}
