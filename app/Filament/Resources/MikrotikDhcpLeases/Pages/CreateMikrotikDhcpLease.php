<?php

namespace App\Filament\Resources\MikrotikDhcpLeases\Pages;

use App\Facades\Mikrotik;
use App\Filament\Resources\MikrotikDhcpLeases\MikrotikDhcpLeaseResource;
use App\Models\MikrotikConfig;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMikrotikDhcpLease extends CreateRecord
{
    protected static string $resource = MikrotikDhcpLeaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $config = MikrotikConfig::findOrFail($data['mikrotik_config_id']);
        $response = Mikrotik::addDhcpLease($config, $data);

        $data['ret_id'] = $response['after']['ret'] ?? $response['lease_data']['.id'] ?? null;
        $data['mac_address'] = strtoupper(str_replace('-', ':', $data['mac_address']));
        $data['dynamic'] = ($response['lease_data']['dynamic'] ?? 'false') === 'true';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        Notification::make()
            ->title('Berhasil')
            ->body('DHCP lease berhasil ditambahkan ke database dan MikroTik.')
            ->success()
            ->send();

        return $this->getResource()::getUrl('index');
    }
}
