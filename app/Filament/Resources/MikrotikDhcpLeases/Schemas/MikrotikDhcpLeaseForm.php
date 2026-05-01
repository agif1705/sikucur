<?php

namespace App\Filament\Resources\MikrotikDhcpLeases\Schemas;

use App\Facades\Mikrotik;
use App\Models\MikrotikConfig;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MikrotikDhcpLeaseForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('DHCP Lease')
                    ->description('Kelola static DHCP lease di MikroTik.')
                    ->columns(2)
                    ->schema([
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
                            ->live()
                            ->required(),
                        Forms\Components\TextInput::make('mac_address')
                            ->label('MAC Address')
                            ->placeholder('AA:BB:CC:DD:EE:FF')
                            ->required()
                            ->maxLength(17)
                            ->rules([
                                'regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
                            ])
                            ->helperText('MAC address tidak boleh sama pada MikroTik yang sama.'),
                        Forms\Components\TextInput::make('address')
                            ->label('IP Address')
                            ->placeholder('192.168.88.10')
                            ->rules(['nullable', 'ip'])

                            ->maxLength(255),
                        Select::make('server')
                            ->label('DHCP Server')
                            ->options(fn (Get $get) => self::dhcpServerOptions($get('mikrotik_config_id')))
                            ->searchable()
                            ->placeholder('Pilih DHCP server')
                            ->helperText('Contoh dari MikroTik: dhcp1, dhcp2, dhcp3.'),
                        Forms\Components\TextInput::make('host_name')
                            ->label('Host Name')
                            ->disabled()
                            ->visibleOn('edit')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('client_id')
                            ->label('Client ID')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('comment')
                            ->label('Nama / Comment')
                            ->columnSpanFull()
                            ->maxLength(65535),
                        Forms\Components\Toggle::make('blocked')
                            ->label('Block Access')
                            ->default(false),
                        Forms\Components\Toggle::make('dynamic')
                            ->label('Dynamic')
                            ->disabled()
                            ->visibleOn('edit'),
                        Forms\Components\TextInput::make('ret_id')
                            ->label('MikroTik ID')
                            ->disabled()
                            ->visibleOn('edit')
                            ->maxLength(255),
                    ]),
            ]);
    }

    private static function dhcpServerOptions(?int $mikrotikConfigId): array
    {
        if (blank($mikrotikConfigId)) {
            return [];
        }

        $config = MikrotikConfig::find($mikrotikConfigId);

        if (! $config) {
            return [];
        }

        try {
            return collect(Mikrotik::getDhcpServers($config))
                ->filter(fn (array $server) => filled($server['name'] ?? null))
                ->mapWithKeys(fn (array $server) => [
                    $server['name'] => sprintf(
                        '%s (%s)',
                        $server['name'],
                        $server['interface'] ?? $server['address-pool'] ?? 'DHCP'
                    ),
                ])
                ->all();
        } catch (\Exception) {
            return [];
        }
    }
}
