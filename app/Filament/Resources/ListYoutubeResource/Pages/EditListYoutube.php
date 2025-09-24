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
        $url = $data['url'];
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $videoId = $params['v'] ?? null;
        $data['id_youtube'] = $videoId;
        $data['nagari_id'] = Auth::user()->nagari->id;
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        // redirect ke list setelah create
        return $this->getResource()::getUrl('index');
    }
}