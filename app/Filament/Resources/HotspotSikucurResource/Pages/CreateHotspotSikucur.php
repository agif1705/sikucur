<?php

namespace App\Filament\Resources\HotspotSikucurResource\Pages;

use Filament\Actions;
use App\Facades\Mikrotik;
use App\Models\MikrotikConfig;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\HotspotSikucurResource;

class CreateHotspotSikucur extends CreateRecord
{
    protected static string $resource = HotspotSikucurResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $config = MikrotikConfig::find($data['mikrotik_config_id']);
        $addUserHotspot = Mikrotik::addHotspotUser(
            $config,
            $data['nik'],
            $data['phone_mikrotik']
        );
        $mikrotikUserId = $addUserHotspot['after']['ret'];
        $data['ret_id'] = $mikrotikUserId;
        $data['mikrotik_id'] = $mikrotikUserId;
        $data['status'] = true;
        $data['activated_at'] = now();
        $data['expired_at'] = now()->addDays(360);
        return $data;
        Notification::make()
            ->title('Error')
            ->body('Gagal menambahkan user ke MikroTik: ' . $e->getMessage())
            ->danger()
            ->send();
    }


    protected function getRedirectUrl(): string
    {
        Notification::make()
            ->title('Berhasil')
            ->body('User berhasil ditambahkan ke database dan MikroTik.')
            ->success();
        return $this->getResource()::getUrl('index');
    }
}
