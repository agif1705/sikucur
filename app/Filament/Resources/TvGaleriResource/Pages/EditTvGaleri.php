<?php

namespace App\Filament\Resources\TvGaleriResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TvGaleriResource;

class EditTvGaleri extends EditRecord
{
    protected static string $resource = TvGaleriResource::class;
    protected function getRedirectUrl(): string
    {
        // redirect ke list setelah create
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    // hapus file dari storage
                    if ($record->image) {
                        Storage::disk('public')->delete($record->image);
                    }

                    // kalau field multiple JSON
                    if (is_array($record->images ?? null)) {
                        foreach ($record->images as $file) {
                            Storage::disk('public')->delete($file);
                        }
                    }
                }),
        ];
    }
}
