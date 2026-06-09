<?php

namespace App\Filament\Resources\PppSecrets\Tables;

use App\Facades\Mikrotik;
use App\Models\MikrotikConfig;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class PppSecretsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->width('200px')
                    ->sortable(),
                Tables\Columns\TextColumn::make('service')
                    ->label('Layanan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pppoe' => 'info',
                        'pptp' => 'warning',
                        'l2tp' => 'success',
                        'sstp' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('password')
                    ->label('Password')
                    ->width('150px')
                    ->placeholder('-')
                    ->limit(20)
                    ->copyable()
                    ->copyableState(fn (?string $state): string => (string) $state),
                Tables\Columns\TextColumn::make('profile')
                    ->label('Profile')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('remote_address')
                    ->label('IP')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('remote_ont_port')
                    ->label('Port ONT')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextInputColumn::make('comment')
                    ->label('Keterangan')
                    ->placeholder('-')
                    ->searchable()
                    ->width('320px')
                    ->extraAttributes(['style' => 'min-width: 320px;'])
                    ->extraInputAttributes(['style' => 'min-width: 300px;'])
                    ->toggleable()
                    ->updateStateUsing(fn (array $record, ?string $state): ?string => self::syncCommentToMikrotik($record, $state)),
                Tables\Columns\IconColumn::make('disabled')
                    ->boolean()
                    ->label('Dinonaktifkan')
                    ->sortable()
                    ->toggleable()
                    ->trueIcon('gmdi-check-circle')
                    ->falseIcon('gmdi-cancel'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mikrotik_config_id')
                    ->label('MikroTik')
                    ->options(fn (): array => MikrotikConfig::query()
                        ->orderByDesc('is_active')
                        ->orderBy('name')
                        ->orderBy('nagari')
                        ->get()
                        ->mapWithKeys(fn (MikrotikConfig $config) => [
                            $config->id => $config->name ?: "{$config->nagari} - {$config->location}",
                        ])
                        ->all())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('service')
                    ->label('Layanan')
                    ->options([
                        'pppoe' => 'PPPoE',
                        'pptp' => 'PPTP',
                        'l2tp' => 'L2TP',
                        'sstp' => 'SSTP',
                    ]),
                Tables\Filters\TernaryFilter::make('disabled')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Dinonaktifkan')
                    ->falseLabel('Aktif'),
            ])
            ->actions([
                Action::make('remoteOnt')
                    ->label('Remote ONT')
                    ->icon('gmdi-open-in-new')
                    ->color('info')
                    ->url(fn (array $record): string => route('mikrotik.ppp-secret.remote-ont', [
                        'config' => $record['mikrotik_config_id'],
                        'ip' => $record['remote_address'],
                        'port' => $record['remote_ont_port'],
                    ]))
                    ->openUrlInNewTab()
                    ->disabled(fn (array $record): bool => ! self::canRemoteOnt($record)),
                Action::make('remoteOntPublic')
                    ->label('Remote Public')
                    ->icon('gmdi-public')
                    ->color('success')
                    ->url(fn (array $record): string => route('mikrotik.ppp-secret.remote-ont-public', [
                        'config' => $record['mikrotik_config_id'],
                        'ip' => $record['remote_address'],
                    ]))
                    ->openUrlInNewTab()
                    ->disabled(fn (array $record): bool => ! self::canRemoteOnt($record)),
            ])
            ->bulkActions([
                //
            ]);
    }

    private static function syncCommentToMikrotik(array $record, ?string $comment): ?string
    {
        $previousComment = $record['comment'] ?? null;
        $secretId = $record['.id'] ?? null;
        $config = self::getMikrotikConfig($record);

        if (! $config || blank($secretId)) {
            Notification::make()
                ->title('Gagal Mengubah Keterangan')
                ->body('Konfigurasi MikroTik atau ID PPP secret tidak ditemukan.')
                ->danger()
                ->send();

            return $previousComment;
        }

        try {
            Mikrotik::updatePppSecret($config, $secretId, [
                'comment' => $comment ?? '',
            ]);

            Notification::make()
                ->title('Keterangan Diperbarui')
                ->body('Keterangan PPP secret berhasil dikirim ke MikroTik.')
                ->success()
                ->send();

            return $comment;
        } catch (\Exception $e) {
            Log::error('Failed to update PPP secret comment from table input', [
                'mikrotik_config_id' => $config->id,
                'secret_id' => $secretId,
                'comment' => $comment,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Gagal Mengubah Keterangan')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return $previousComment;
        }
    }

    private static function getMikrotikConfig(array $record): ?MikrotikConfig
    {
        if (filled($record['mikrotik_config_id'] ?? null)) {
            return MikrotikConfig::find($record['mikrotik_config_id']);
        }

        return session('ppp_secrets_mikrotik_config');
    }

    private static function canRemoteOnt(array $record): bool
    {
        return filled($record['mikrotik_config_id'] ?? null)
            && filter_var($record['remote_address'] ?? null, FILTER_VALIDATE_IP) !== false;
    }
}
