<?php

namespace App\Filament\Resources\MikrotikDhcpLeases\Tables;

use App\Facades\Mikrotik;
use App\Models\MikrotikDhcpLease;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class MikrotikDhcpLeasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextInputColumn::make('comment')
                    ->label('Nama')
                    ->searchable()
                    ->width('360px')
                    ->extraAttributes(['style' => 'min-width: 360px;'])
                    ->extraInputAttributes(['style' => 'min-width: 340px;'])
                    ->sortable()
                    ->afterStateUpdated(function (MikrotikDhcpLease $record, ?string $state): void {
                        self::syncCommentToMikrotik($record, $state);
                    }),
                Tables\Columns\ToggleColumn::make('blocked')
                    ->label('Block Access')
                    ->sortable()
                    ->afterStateUpdated(function (MikrotikDhcpLease $record, bool $state): void {
                        self::syncBlockAccessToMikrotik($record, $state);
                    }),
                Tables\Columns\TextColumn::make('mac_address')
                    ->label('MAC Address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('active_address')
                    ->label('Active IP')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'bound' => 'success',
                        'waiting' => 'warning',
                        default => 'gray',
                    })
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_seen')
                    ->label('Last Seen')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('host_name')
                    ->label('Host')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('server')
                    ->label('Server')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('dynamic')
                    ->boolean()
                    ->label('Dynamic')
                    ->sortable(),

                Tables\Columns\IconColumn::make('disabled')
                    ->boolean()
                    ->label('Disabled')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ret_id')
                    ->label('MikroTik ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->before(function (MikrotikDhcpLease $record): void {
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
            ])
            ->bulkActions([
                //
            ]);
    }

    private static function syncBlockAccessToMikrotik(MikrotikDhcpLease $record, bool $blocked): void
    {
        if (! $record->mikrotikConfig) {
            self::revertBlockedState($record, $blocked);

            Notification::make()
                ->title('Gagal Mengubah Block Access')
                ->body('Konfigurasi MikroTik tidak ditemukan.')
                ->danger()
                ->send();

            return;
        }

        try {
            $response = $record->ret_id
                ? Mikrotik::updateDhcpLease($record->mikrotikConfig, $record->ret_id, [
                    'mac_address' => $record->mac_address,
                    'address' => $record->address,
                    'server' => $record->server,
                    'client_id' => $record->client_id,
                    'comment' => $record->comment,
                    'blocked' => $blocked,
                ])
                : (Mikrotik::addDhcpLease($record->mikrotikConfig, [
                    'mac_address' => $record->mac_address,
                    'address' => $record->address,
                    'server' => $record->server,
                    'client_id' => $record->client_id,
                    'comment' => $record->comment,
                    'blocked' => $blocked,
                ])['lease_data'] ?? []);

            self::updateRecordFromMikrotikResponse($record, $response, $blocked);

            Notification::make()
                ->title('Block Access Diperbarui')
                ->body($blocked ? 'Akses DHCP client diblokir.' : 'Akses DHCP client dibuka kembali.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            self::revertBlockedState($record, $blocked);

            Log::error('Failed to update DHCP lease block access from table toggle', [
                'lease_id' => $record->id,
                'ret_id' => $record->ret_id,
                'blocked' => $blocked,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Gagal Mengubah Block Access')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private static function syncCommentToMikrotik(MikrotikDhcpLease $record, ?string $comment): void
    {
        $previousComment = $record->getOriginal('comment');

        if (! $record->mikrotikConfig) {
            self::revertCommentState($record, $previousComment);

            Notification::make()
                ->title('Gagal Mengubah Nama')
                ->body('Konfigurasi MikroTik tidak ditemukan.')
                ->danger()
                ->send();

            return;
        }

        try {
            $response = $record->ret_id
                ? Mikrotik::updateDhcpLease($record->mikrotikConfig, $record->ret_id, [
                    'mac_address' => $record->mac_address,
                    'address' => $record->address,
                    'server' => $record->server,
                    'client_id' => $record->client_id,
                    'comment' => $comment,
                ])
                : (Mikrotik::addDhcpLease($record->mikrotikConfig, [
                    'mac_address' => $record->mac_address,
                    'address' => $record->address,
                    'server' => $record->server,
                    'client_id' => $record->client_id,
                    'comment' => $comment,
                ])['lease_data'] ?? []);

            self::updateRecordFromMikrotikResponse($record, $response, $record->blocked);

            Notification::make()
                ->title('Nama Diperbarui')
                ->body('Comment DHCP lease berhasil dikirim ke MikroTik.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            self::revertCommentState($record, $previousComment);

            Log::error('Failed to update DHCP lease comment from table input', [
                'lease_id' => $record->id,
                'ret_id' => $record->ret_id,
                'comment' => $comment,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Gagal Mengubah Nama')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private static function revertBlockedState(MikrotikDhcpLease $record, bool $attemptedState): void
    {
        $record->forceFill([
            'blocked' => ! $attemptedState,
        ])->saveQuietly();
    }

    private static function revertCommentState(MikrotikDhcpLease $record, ?string $previousComment): void
    {
        $record->forceFill([
            'comment' => $previousComment,
        ])->saveQuietly();
    }

    private static function updateRecordFromMikrotikResponse(MikrotikDhcpLease $record, array $response, bool $fallbackBlocked): void
    {
        $record->forceFill([
            'ret_id' => $response['.id'] ?? $record->ret_id,
            'address' => $response['address'] ?? $response['active-address'] ?? $record->address,
            'active_address' => $response['active-address'] ?? $record->active_address,
            'server' => $response['server'] ?? $record->server,
            'host_name' => $response['host-name'] ?? $record->host_name,
            'client_id' => $response['client-id'] ?? $record->client_id,
            'status' => $response['status'] ?? $record->status,
            'last_seen' => $response['last-seen'] ?? $record->last_seen,
            'comment' => $response['comment'] ?? $record->comment,
            'dynamic' => self::mikrotikBool($response['dynamic'] ?? $record->dynamic),
            'disabled' => self::mikrotikBool($response['disabled'] ?? $record->disabled),
            'blocked' => self::mikrotikBool($response['blocked'] ?? $fallbackBlocked),
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
