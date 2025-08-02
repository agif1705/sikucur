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
