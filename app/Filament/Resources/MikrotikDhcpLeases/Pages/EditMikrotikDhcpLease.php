<?php

namespace App\Filament\Resources\MikrotikDhcpLeases\Pages;

use App\Facades\Mikrotik;
use App\Filament\Resources\MikrotikDhcpLeases\MikrotikDhcpLeaseResource;
use App\Models\MikrotikConfig;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditMikrotikDhcpLease extends EditRecord
{
    protected static string $resource = MikrotikDhcpLeaseResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();
        $config = MikrotikConfig::findOrFail($data['mikrotik_config_id']);
        $data['mac_address'] = strtoupper(str_replace('-', ':', $data['mac_address']));

        if ((int) $record->mikrotik_config_id !== (int) $data['mikrotik_config_id']) {
            if ($record->ret_id && $record->mikrotikConfig) {
                Mikrotik::removeDhcpLease($record->mikrotikConfig, $record->ret_id);
            }

            $response = Mikrotik::addDhcpLease($config, $data);
            $data = $this->mergeMikrotikLeaseResponse($data, $response['lease_data'] ?? $response);

            return $data;
        }

        if ($record->ret_id) {
            $response = Mikrotik::updateDhcpLease($config, $record->ret_id, $data);
            $data = $this->mergeMikrotikLeaseResponse($data, $response);
        } else {
            $response = Mikrotik::addDhcpLease($config, $data);
            $data = $this->mergeMikrotikLeaseResponse($data, $response['lease_data'] ?? $response);
        }

        return $data;
    }

    private function mergeMikrotikLeaseResponse(array $data, array $response): array
    {
        $data['ret_id'] = $response['.id'] ?? $data['ret_id'] ?? null;
        $data['address'] = $response['address'] ?? $response['active-address'] ?? $data['address'] ?? null;
        $data['active_address'] = $response['active-address'] ?? $data['active_address'] ?? null;
        $data['server'] = $response['server'] ?? $data['server'] ?? null;
        $data['host_name'] = $response['host-name'] ?? $data['host_name'] ?? null;
        $data['client_id'] = $response['client-id'] ?? $data['client_id'] ?? null;
        $data['status'] = $response['status'] ?? $data['status'] ?? null;
        $data['last_seen'] = $response['last-seen'] ?? $data['last_seen'] ?? null;
        $data['comment'] = $response['comment'] ?? $data['comment'] ?? null;
        $data['dynamic'] = $this->mikrotikBool($response['dynamic'] ?? $data['dynamic'] ?? false);
        $data['disabled'] = $this->mikrotikBool($response['disabled'] ?? $data['disabled'] ?? false);
        $data['blocked'] = ($response['block-access'] ?? null) === 'yes'
            || $this->mikrotikBool($response['blocked'] ?? $data['blocked'] ?? false);

        return $data;
    }

    private function mikrotikBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return $value === 'true' || $value === '1' || $value === 1;
    }

    protected function getRedirectUrl(): string
    {
        Notification::make()
            ->title('Berhasil')
            ->body('DHCP lease berhasil diperbarui di database dan MikroTik.')
            ->success()
            ->send();

        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus DHCP Lease')
                ->modalDescription('Lease akan dihapus dari database dan MikroTik. Apakah Anda yakin?')
                ->before(function (): void {
                    $record = $this->getRecord();

                    if (! $record->ret_id || ! $record->mikrotikConfig) {
                        return;
                    }

                    try {
                        Mikrotik::removeDhcpLease($record->mikrotikConfig, $record->ret_id);
                    } catch (\Exception $e) {
                        Log::error('Failed to remove DHCP lease from MikroTik', [
                            'lease_id' => $record->id,
                            'ret_id' => $record->ret_id,
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->title('Gagal Menghapus dari MikroTik')
                            ->body('Lease tidak dihapus. Cek koneksi MikroTik atau hapus manual dari router.')
                            ->danger()
                            ->send();

                        throw $e;
                    }
                }),
        ];
    }
}
