<?php

namespace App\Filament\Resources\VideoTvs\Pages;

use App\Filament\Resources\VideoTvs\VideoTvResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditVideoTv extends EditRecord
{
    protected static string $resource = VideoTvResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record): void {
                    if ($record->file_path) {
                        Storage::disk('public')->delete($record->file_path);
                    }
                }),
        ];
    }
}
