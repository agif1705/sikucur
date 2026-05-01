<?php

namespace App\Filament\Resources\MikrotikDhcpLeases\Pages;

use App\Facades\Mikrotik;
use App\Filament\Resources\MikrotikDhcpLeases\MikrotikDhcpLeaseResource;
use App\Models\MikrotikConfig;
use App\Models\MikrotikDhcpLease;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListMikrotikDhcpLeases extends ListRecords
{
    protected static string $resource = MikrotikDhcpLeaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncFromMikrotik')
                ->label('Sinkron dari MikroTik')
                ->icon('gmdi-sync')
                ->form([
                    Select::make('mikrotik_config_id')
                        ->label('MikroTik')
                        ->options(fn () => MikrotikConfig::query()
                            ->orderBy('name')
                            ->orderBy('nagari')
                            ->get()
                            ->mapWithKeys(fn (MikrotikConfig $config) => [
                                $config->id => $config->name ?: "{$config->nagari} - {$config->location}",
                            ])
                            ->all())
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $config = MikrotikConfig::findOrFail($data['mikrotik_config_id']);

                    try {
                        $leases = Mikrotik::getDhcpLeases($config);

                        $synced = DB::transaction(function () use ($config, $leases): int {
                            MikrotikDhcpLease::where('mikrotik_config_id', $config->id)->delete();

                            $rows = collect($leases)
                                ->filter(fn (array $lease) => filled($lease['mac-address'] ?? null))
                                ->map(fn (array $lease) => [
                                    'mikrotik_config_id' => $config->id,
                                    'mac_address' => strtoupper($lease['mac-address']),
                                    'ret_id' => $lease['.id'] ?? null,
                                    'address' => $lease['address'] ?? $lease['active-address'] ?? null,
                                    'active_address' => $lease['active-address'] ?? null,
                                    'server' => $lease['server'] ?? null,
                                    'host_name' => $lease['host-name'] ?? null,
                                    'client_id' => $lease['client-id'] ?? null,
                                    'status' => $lease['status'] ?? null,
                                    'last_seen' => $lease['last-seen'] ?? null,
                                    'comment' => $lease['comment'] ?? null,
                                    'dynamic' => ($lease['dynamic'] ?? 'false') === 'true',
                                    'disabled' => ($lease['disabled'] ?? 'false') === 'true',
                                    'blocked' => ($lease['block-access'] ?? null) === 'yes' || ($lease['blocked'] ?? 'false') === 'true',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ])
                                ->unique('mac_address')
                                ->values();

                            $rows->chunk(500)->each(fn ($chunk) => MikrotikDhcpLease::insert($chunk->all()));

                            return $rows->count();
                        });

                        Notification::make()
                            ->title('Sinkron Berhasil')
                            ->body("Data lama dihapus, {$synced} DHCP lease terbaru berhasil disimpan.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Log::error('Failed to sync DHCP leases from MikroTik', [
                            'mikrotik_config_id' => $config->id,
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->title('Sinkron Gagal')
                            ->body('Gagal mengambil DHCP lease dari MikroTik: '.$e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('exportExcel')
                ->label('Export Excel')
                ->icon('gmdi-download')
                ->form([
                    Select::make('mikrotik_config_id')
                        ->label('MikroTik')
                        ->options(fn () => MikrotikConfig::query()
                            ->orderBy('name')
                            ->orderBy('nagari')
                            ->get()
                            ->mapWithKeys(fn (MikrotikConfig $config) => [
                                $config->id => $config->name ?: "{$config->nagari} - {$config->location}",
                            ])
                            ->all())
                        ->searchable()
                        ->placeholder('Semua MikroTik'),
                ])
                ->action(fn (array $data): StreamedResponse => $this->exportExcel($data['mikrotik_config_id'] ?? null)),
            Actions\CreateAction::make(),
        ];
    }

    private function exportExcel(?int $mikrotikConfigId = null): StreamedResponse
    {
        $fileName = 'dhcp-leases-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($mikrotikConfigId): void {
            echo '<html><head><meta charset="UTF-8"></head><body>';
            echo '<table border="1">';
            echo '<thead><tr>';

            foreach ($this->exportHeadings() as $heading) {
                echo '<th>'.e($heading).'</th>';
            }

            echo '</tr></thead><tbody>';

            MikrotikDhcpLease::query()
                ->with('mikrotikConfig')
                ->when($mikrotikConfigId, fn ($query) => $query->where('mikrotik_config_id', $mikrotikConfigId))
                ->orderBy('mikrotik_config_id')
                ->orderBy('comment')
                ->chunk(500, function ($leases): void {
                    foreach ($leases as $lease) {
                        echo '<tr>';

                        foreach ($this->exportRow($lease) as $value) {
                            echo '<td style="mso-number-format:\'@\';">'.e($value).'</td>';
                        }

                        echo '</tr>';
                    }
                });

            echo '</tbody></table>';
            echo '</body></html>';
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function exportHeadings(): array
    {
        return [
            'MikroTik',
            'Nama',
            'MAC Address',
            'IP Address',
            'Active IP',
            'Status',
            'Last Seen',
            'Host',
            'Server',
            'Dynamic',
            'Block Access',
            'Disabled',
            'MikroTik ID',
            'Updated At',
        ];
    }

    private function exportRow(MikrotikDhcpLease $lease): array
    {
        return [
            $lease->mikrotikConfig?->name,
            $lease->comment,
            $lease->mac_address,
            $lease->address,
            $lease->active_address,
            $lease->status,
            $lease->last_seen,
            $lease->host_name,
            $lease->server,
            $lease->dynamic ? 'Ya' : 'Tidak',
            $lease->blocked ? 'Ya' : 'Tidak',
            $lease->disabled ? 'Ya' : 'Tidak',
            $lease->ret_id,
            $lease->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
