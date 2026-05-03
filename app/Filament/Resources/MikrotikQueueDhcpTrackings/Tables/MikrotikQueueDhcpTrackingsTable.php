<?php

namespace App\Filament\Resources\MikrotikQueueDhcpTrackings\Tables;

use App\Facades\Mikrotik;
use App\Models\MikrotikConfig;
use App\Models\MikrotikDhcpLease;
use App\Models\MikrotikQueue;
use App\Models\MikrotikQueueDhcpTracking;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class MikrotikQueueDhcpTrackingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->orderByRaw("CASE WHEN COALESCE(queue_ip, lease_ip) LIKE '172.16.10.%' THEN 0 ELSE 1 END")
                ->orderByRaw("
                    CASE
                        WHEN COALESCE(queue_ip, lease_ip) ~ '^[0-9]{1,3}(\\.[0-9]{1,3}){3}$'
                        THEN
                            split_part(COALESCE(queue_ip, lease_ip), '.', 1)::bigint * 16777216 +
                            split_part(COALESCE(queue_ip, lease_ip), '.', 2)::bigint * 65536 +
                            split_part(COALESCE(queue_ip, lease_ip), '.', 3)::bigint * 256 +
                            split_part(COALESCE(queue_ip, lease_ip), '.', 4)::bigint
                        ELSE NULL
                    END
                ")
                ->orderBy('tracking_status'))
            ->columns([
                Tables\Columns\TextInputColumn::make('dhcp_name')
                    ->label('Nama DHCP Lease')
                    ->placeholder('Belum ada nama')
                    ->searchable()
                    ->width('320px')
                    ->extraAttributes(['style' => 'min-width: 320px;'])
                    ->extraInputAttributes(['style' => 'min-width: 300px;'])
                    ->sortable()
                    ->disabled(fn (MikrotikQueueDhcpTracking $record): bool => blank($record->dhcp_lease_id))
                    ->updateStateUsing(fn (MikrotikQueueDhcpTracking $record, ?string $state): ?string => self::syncDhcpName($record, $state)),
                Tables\Columns\TextColumn::make('lease_ip')
                    ->label('IP Lease')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('queue_ip')
                    ->label('IP Queue')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('queue_name')
                    ->label('Nama Queue')
                    ->placeholder('Belum ada nama queue')
                    ->searchable()
                    ->width('280px')
                    ->extraAttributes(['style' => 'min-width: 280px;'])
                    ->extraInputAttributes(['style' => 'min-width: 260px;'])
                    ->sortable()
                    ->disabled(fn (MikrotikQueueDhcpTracking $record): bool => blank($record->queue_id))
                    ->updateStateUsing(fn (MikrotikQueueDhcpTracking $record, ?string $state): ?string => self::syncQueueName($record, $state)),
                Tables\Columns\TextColumn::make('tracking_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Lengkap' => 'success',
                        'Queue Belum Ada Nama', 'DHCP Lease Belum Ada Nama' => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('queue_target')
                    ->label('Target Queue')
                    ->placeholder('-')
                    ->searchable()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('blocked')
                    ->label('Blok Akses')
                    ->sortable()
                    ->disabled(fn (MikrotikQueueDhcpTracking $record): bool => blank($record->dhcp_lease_id))
                    ->updateStateUsing(fn (MikrotikQueueDhcpTracking $record, bool $state): bool => self::syncBlockAccess($record, $state)),
                Tables\Columns\TextColumn::make('blocked_label')
                    ->label('Status Akses')
                    ->state(fn (MikrotikQueueDhcpTracking $record): string => blank($record->dhcp_lease_id)
                        ? '-'
                        : ($record->blocked ? 'Diblokir' : 'Terbuka'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Diblokir' => 'danger',
                        'Terbuka' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mikrotik_config_id')
                    ->label('MikroTik')
                    ->options(fn () => MikrotikConfig::query()
                        ->orderBy('name')
                        ->orderBy('nagari')
                        ->get()
                        ->mapWithKeys(fn (MikrotikConfig $config) => [
                            $config->id => $config->name ?: "{$config->nagari} - {$config->location}",
                        ])
                        ->all())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('tracking_status')
                    ->label('Status')
                    ->options([
                        'Belum Ada Queue' => 'Belum Ada Queue',
                        'Belum Ada DHCP Lease' => 'Belum Ada DHCP Lease',
                        'Queue Belum Ada IP' => 'Queue Belum Ada IP',
                        'Queue Belum Ada Nama' => 'Queue Belum Ada Nama',
                        'DHCP Lease Belum Ada Nama' => 'DHCP Lease Belum Ada Nama',
                        'Lengkap' => 'Lengkap',
                    ]),
            ])
            ->actions([
                Action::make('remoteOnt')
                    ->label('Remote ONT')
                    ->icon('gmdi-open-in-new')
                    ->color('info')
                    ->url(fn (MikrotikQueueDhcpTracking $record): string => route('mikrotik.remote-ont', $record))
                    ->openUrlInNewTab()
                    ->disabled(fn (MikrotikQueueDhcpTracking $record): bool => blank($record->queue_ip ?: $record->lease_ip)),
                Action::make('remoteOntPublic')
                    ->label('Remote Public')
                    ->icon('gmdi-public')
                    ->color('success')
                    ->url(fn (MikrotikQueueDhcpTracking $record): string => route('mikrotik.remote-ont-public', $record))
                    ->openUrlInNewTab()
                    ->disabled(fn (MikrotikQueueDhcpTracking $record): bool => blank($record->queue_ip ?: $record->lease_ip)),
            ])
            ->bulkActions([
                //
            ]);
    }

    private static function syncDhcpName(MikrotikQueueDhcpTracking $tracking, ?string $name): ?string
    {
        $previousName = $tracking->dhcp_name;
        $name = trim((string) $name);

        if (blank($name)) {
            Notification::make()
                ->title('Gagal Mengubah Nama DHCP Lease')
                ->body('Nama DHCP lease tidak boleh kosong.')
                ->danger()
                ->send();

            return $previousName;
        }

        $lease = MikrotikDhcpLease::query()
            ->with('mikrotikConfig')
            ->find($tracking->dhcp_lease_id);

        if (! $lease || ! $lease->mikrotikConfig) {
            Notification::make()
                ->title('Gagal Mengubah Nama DHCP Lease')
                ->body('Data DHCP lease atau konfigurasi MikroTik tidak ditemukan.')
                ->danger()
                ->send();

            return $previousName;
        }

        try {
            $response = $lease->ret_id
                ? Mikrotik::updateDhcpLease($lease->mikrotikConfig, $lease->ret_id, [
                    'mac_address' => $lease->mac_address,
                    'address' => $lease->address,
                    'server' => $lease->server,
                    'client_id' => $lease->client_id,
                    'comment' => $name,
                ])
                : (Mikrotik::addDhcpLease($lease->mikrotikConfig, [
                    'mac_address' => $lease->mac_address,
                    'address' => $lease->address,
                    'server' => $lease->server,
                    'client_id' => $lease->client_id,
                    'comment' => $name,
                ])['lease_data'] ?? []);

            $lease->forceFill([
                'ret_id' => $response['.id'] ?? $lease->ret_id,
                'comment' => $response['comment'] ?? $name,
            ])->saveQuietly();

            Notification::make()
                ->title('Nama DHCP Lease Diperbarui')
                ->body('Nama DHCP lease berhasil dikirim ke MikroTik.')
                ->success()
                ->send();

            return $lease->comment;
        } catch (\Exception $e) {
            Log::error('Failed to update DHCP lease name from tracking table input', [
                'tracking_id' => $tracking->id,
                'dhcp_lease_id' => $lease->id,
                'ret_id' => $lease->ret_id,
                'name' => $name,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Gagal Mengubah Nama DHCP Lease')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return $previousName;
        }
    }

    private static function syncBlockAccess(MikrotikQueueDhcpTracking $tracking, bool $blocked): bool
    {
        $lease = MikrotikDhcpLease::query()
            ->with('mikrotikConfig')
            ->find($tracking->dhcp_lease_id);

        if (! $lease || ! $lease->mikrotikConfig) {
            Notification::make()
                ->title('Gagal Mengubah Block Access')
                ->body('Data DHCP lease atau konfigurasi MikroTik tidak ditemukan.')
                ->danger()
                ->send();

            return (bool) $tracking->blocked;
        }

        try {
            $response = $lease->ret_id
                ? Mikrotik::updateDhcpLease($lease->mikrotikConfig, $lease->ret_id, [
                    'mac_address' => $lease->mac_address,
                    'address' => $lease->address,
                    'server' => $lease->server,
                    'client_id' => $lease->client_id,
                    'comment' => $lease->comment,
                    'blocked' => $blocked,
                ])
                : (Mikrotik::addDhcpLease($lease->mikrotikConfig, [
                    'mac_address' => $lease->mac_address,
                    'address' => $lease->address,
                    'server' => $lease->server,
                    'client_id' => $lease->client_id,
                    'comment' => $lease->comment,
                    'blocked' => $blocked,
                ])['lease_data'] ?? []);

            self::updateLeaseFromMikrotikResponse($lease, $response, $blocked);

            Notification::make()
                ->title('Block Access Diperbarui')
                ->body($lease->blocked ? 'Akses DHCP client diblokir.' : 'Akses DHCP client dibuka kembali.')
                ->success()
                ->send();

            return $lease->blocked;
        } catch (\Exception $e) {
            Log::error('Failed to update DHCP lease block access from tracking table toggle', [
                'tracking_id' => $tracking->id,
                'dhcp_lease_id' => $lease->id,
                'ret_id' => $lease->ret_id,
                'blocked' => $blocked,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Gagal Mengubah Block Access')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return $lease->blocked;
        }
    }

    private static function syncQueueName(MikrotikQueueDhcpTracking $tracking, ?string $name): ?string
    {
        $previousName = $tracking->queue_name;
        $name = trim((string) $name);

        if (blank($name)) {
            Notification::make()
                ->title('Gagal Mengubah Nama Queue')
                ->body('Nama queue tidak boleh kosong.')
                ->danger()
                ->send();

            return $previousName;
        }

        $queue = MikrotikQueue::query()
            ->with('mikrotikConfig')
            ->find($tracking->queue_id);

        if (! $queue || ! $queue->mikrotikConfig || ! $queue->ret_id) {
            Notification::make()
                ->title('Gagal Mengubah Nama Queue')
                ->body('Data queue, konfigurasi MikroTik, atau ID queue tidak ditemukan.')
                ->danger()
                ->send();

            return $previousName;
        }

        try {
            $response = Mikrotik::updateSimpleQueue($queue->mikrotikConfig, $queue->ret_id, [
                'name' => $name,
            ]);

            $queue->forceFill([
                'name' => $response['name'] ?? $name,
            ])->saveQuietly();

            Notification::make()
                ->title('Nama Queue Diperbarui')
                ->body('Nama queue berhasil dikirim ke MikroTik.')
                ->success()
                ->send();

            return $queue->name;
        } catch (\Exception $e) {
            Log::error('Failed to update simple queue name from tracking table input', [
                'tracking_id' => $tracking->id,
                'queue_id' => $queue->id,
                'ret_id' => $queue->ret_id,
                'name' => $name,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Gagal Mengubah Nama Queue')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return $previousName;
        }
    }

    private static function updateLeaseFromMikrotikResponse(MikrotikDhcpLease $lease, array $response, bool $fallbackBlocked): void
    {
        $lease->forceFill([
            'ret_id' => $response['.id'] ?? $lease->ret_id,
            'address' => $response['address'] ?? $response['active-address'] ?? $lease->address,
            'active_address' => $response['active-address'] ?? $lease->active_address,
            'server' => $response['server'] ?? $lease->server,
            'host_name' => $response['host-name'] ?? $lease->host_name,
            'client_id' => $response['client-id'] ?? $lease->client_id,
            'status' => $response['status'] ?? $lease->status,
            'last_seen' => $response['last-seen'] ?? $lease->last_seen,
            'comment' => $response['comment'] ?? $lease->comment,
            'dynamic' => self::mikrotikBool($response['dynamic'] ?? $lease->dynamic),
            'disabled' => self::mikrotikBool($response['disabled'] ?? $lease->disabled),
            'blocked' => self::mikrotikBool($response['blocked'] ?? $response['block-access'] ?? $fallbackBlocked),
        ])->saveQuietly();
    }

    private static function mikrotikBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return $value === 'true' || $value === 'yes' || $value === '1' || $value === 1;
    }
}
