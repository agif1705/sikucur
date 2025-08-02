<?php

namespace App\Filament\Resources\TvInformasiResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TvInformasiResource;

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
        $url = $data['video'];
        $containsYoutube = Str::contains($url, 'https://');
        if ($containsYoutube) {
            $you = preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
            $data['video'] = $matches[1] ?? null;
        }

        $data['nagari_id'] = Auth::user()->nagari->id;
        $data['user_id'] = Auth::user()->id;
        return $data;
    }
}
