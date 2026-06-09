<?php

namespace App\Filament\Resources\PppSecrets\Pages;

use App\Facades\Mikrotik;
use App\Filament\Resources\PppSecrets\PppSecretResource;
use App\Filament\Resources\PppSecrets\Tables\PppSecretsTable;
use App\Models\MikrotikConfig;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListPppSecrets extends ListRecords
{
    protected static string $resource = PppSecretResource::class;

    public int|string $remoteOntPort = 1710;

    public function table(Table $table): Table
    {
        return PppSecretsTable::configure(
            $table
                ->records(fn (?string $search, array $filters, ?string $sortColumn, ?string $sortDirection): Collection => $this->getPppSecretRecords(
                    search: $search,
                    filters: $filters,
                    sortColumn: $sortColumn,
                    sortDirection: $sortDirection,
                ))
                ->recordAction(null)
                ->recordUrl(null)
        );
    }

    private function getPppSecretRecords(
        ?string $search = null,
        array $filters = [],
        ?string $sortColumn = null,
        ?string $sortDirection = null,
    ): Collection {
        $selectedConfigId = $filters['mikrotik_config_id']['value'] ?? null;
        $remoteOntPort = $this->normalizeRemoteOntPort($this->remoteOntPort);
        $config = null;
        $records = collect();
        $errors = [];

        foreach ($this->candidateMikrotikConfigs($selectedConfigId) as $candidateConfig) {
            try {
                session(['ppp_secrets_mikrotik_config' => $candidateConfig]);

                $records = collect(Mikrotik::getAllPppSecrets($candidateConfig))
                    ->map(fn (array $secret, int $index): array => $this->normalizePppSecretRecord(
                        secret: $secret,
                        index: $index,
                        config: $candidateConfig,
                        remoteOntPort: $remoteOntPort,
                    ));

                $config = $candidateConfig;

                break;
            } catch (\Exception $e) {
                $errors[] = "{$candidateConfig->name}: {$e->getMessage()}";

                Log::error('Failed to fetch PPP secrets', [
                    'mikrotik_config_id' => $candidateConfig->id,
                    'mikrotik_config' => $candidateConfig->name,
                    'error' => $e->getMessage(),
                ]);

                if (filled($selectedConfigId)) {
                    break;
                }
            }
        }

        if (! $config) {
            if ($errors !== []) {
                Notification::make()
                    ->title('Gagal Mengambil Data')
                    ->body('Gagal mengambil data PPP secrets dari MikroTik: '.implode('; ', $errors))
                    ->danger()
                    ->send();
            }

            return collect();
        }

        session(['ppp_secrets_mikrotik_config' => $config]);

        if (filled($service = $filters['service']['value'] ?? null)) {
            $records = $records->where('service', $service);
        }

        if (($disabledFilter = $filters['disabled']['value'] ?? null) !== null) {
            $records = $records->where('disabled', filter_var($disabledFilter, FILTER_VALIDATE_BOOLEAN));
        }

        if (filled($search)) {
            $search = str($search)->lower()->toString();

            $records = $records->filter(fn (array $record): bool => str_contains(strtolower((string) $record['name']), $search)
             || str_contains(strtolower((string) $record['service']), $search)
             || str_contains(strtolower((string) $record['remote_address']), $search)
             || str_contains(strtolower((string) $record['comment']), $search)
             || str_contains(strtolower((string) $record['password']), $search));
        }

        if (filled($sortColumn)) {
            $records = $records->sortBy(
                fn (array $record): mixed => $record[$sortColumn] ?? null,
                SORT_REGULAR,
                $sortDirection === 'desc',
            );
        }

        return $records->values();
    }

    private function candidateMikrotikConfigs(null|int|string $mikrotikConfigId = null): Collection
    {
        if (filled($mikrotikConfigId)) {
            return MikrotikConfig::query()
                ->whereKey($mikrotikConfigId)
                ->get();
        }

        return MikrotikConfig::query()
            ->orderByDesc('is_active')
            ->orderByRaw('rest_url is null')
            ->orderBy('name')
            ->orderBy('nagari')
            ->get();
    }

    private function normalizeRemoteOntPort(mixed $port): string
    {
        $port = (int) ($port ?: 1710);

        if ($port < 1 || $port > 65535) {
            return '1710';
        }

        return (string) $port;
    }

    private function normalizePppSecretRecord(array $secret, int $index, MikrotikConfig $config, string $remoteOntPort): array
    {
        $remoteOntHost = $this->remoteOntHost($config);

        return [
            '__key' => (string) ($secret['.id'] ?? $secret['name'] ?? $index),
            '.id' => $secret['.id'] ?? null,
            'mikrotik_config_id' => $config->id,
            'mikrotik_config_name' => $config->name ?: "{$config->nagari} - {$config->location}",
            'name' => $secret['name'] ?? '',
            'service' => $secret['service'] ?? '',
            'password' => $secret['password'] ?? '',
            'profile' => $secret['profile'] ?? '',
            'remote_address' => $secret['remote-address'] ?? '',
            'remote_ont_host' => $remoteOntHost,
            'remote_ont_port' => $remoteOntPort,
            'comment' => $secret['comment'] ?? '',
            'disabled' => ($secret['disabled'] ?? false) === true || ($secret['disabled'] ?? null) === 'true',
        ];
    }

    private function remoteOntHost(MikrotikConfig $config): string
    {
        $url = filled($config->rest_url) ? $config->rest_url : $config->host;

        return parse_url($url, PHP_URL_HOST) ?: str($url)->replace(['http://', 'https://'], '')->before('/')->toString();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refreshData')
                ->label('Refresh Data')
                ->icon('gmdi-refresh')
                ->action(function (): void {
                    Notification::make()
                        ->title('Data Diperbarui')
                        ->body('Data PPP secrets berhasil diperbarui dari MikroTik')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('exportExcel')
                ->label('Export Excel')
                ->icon('gmdi-download')
                ->action(fn (): StreamedResponse => $this->exportExcel()),
            Actions\Action::make('syncFromMikrotik')
                ->label('Sinkron dari MikroTik')
                ->icon('gmdi-sync')
                ->form([
                    Select::make('mikrotik_config_id')
                        ->label('MikroTik')
                        ->options(fn () => self::mikrotikOptions())
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $config = MikrotikConfig::findOrFail($data['mikrotik_config_id']);

                    try {
                        $secrets = Mikrotik::getAllPppSecrets($config);

                        Notification::make()
                            ->title('Sinkron Berhasil')
                            ->body('Total '.count($secrets).' PPP secret ditemukan. Data ditampilkan secara real-time dari MikroTik.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Log::error('Failed to sync PPP secrets from MikroTik', [
                            'mikrotik_config_id' => $config->id,
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->title('Sinkron Gagal')
                            ->body('Gagal mengambil PPP secrets dari MikroTik: '.$e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('addPppSecret')
                ->label('Tambah PPP Secret')
                ->icon('gmdi-add')
                ->form([
                    Select::make('mikrotik_config_id')
                        ->label('MikroTik')
                        ->options(fn () => self::mikrotikOptions())
                        ->searchable()
                        ->live()
                        ->required(),
                    TextInput::make('name')
                        ->label('Nama')
                        ->placeholder('contoh: user1')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->required()
                        ->maxLength(255),
                    Select::make('service')
                        ->label('Layanan')
                        ->options([
                            'pppoe' => 'PPPoE',
                            'pptp' => 'PPTP',
                            'l2tp' => 'L2TP',
                            'sstp' => 'SSTP',
                        ])
                        ->default('pppoe')
                        ->required(),
                    Select::make('profile')
                        ->label('Profile')
                        ->options(fn (Get $get): array => self::pppProfileOptions($get('mikrotik_config_id')))
                        ->searchable()
                        ->preload()
                        ->required(),
                    Textarea::make('comment')
                        ->label('Keterangan')
                        ->placeholder('Masukkan keterangan (opsional)')
                        ->rows(3),
                    Toggle::make('disabled')
                        ->label('Nonaktifkan')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    $config = MikrotikConfig::findOrFail($data['mikrotik_config_id']);

                    try {
                        $secretData = [
                            'name' => $data['name'],
                            'password' => $data['password'],
                            'service' => $data['service'],
                            'profile' => $data['profile'],
                            'comment' => $data['comment'] ?? null,
                            'disabled' => $data['disabled'] ?? false,
                        ];

                        $response = Mikrotik::addPppSecret($config, $secretData);

                        Notification::make()
                            ->title('Berhasil')
                            ->body("PPP secret '{$data['name']}' berhasil ditambahkan")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Log::error('Failed to add PPP secret', [
                            'mikrotik_config_id' => $config->id,
                            'name' => $data['name'],
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->title('Gagal')
                            ->body('Gagal menambahkan PPP secret: '.$e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    private function exportExcel(): StreamedResponse
    {
        $records = collect($this->getTableRecords())->values();
        $fileName = 'ppp-secrets-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($records): void {
            echo '<html><head><meta charset="UTF-8"></head><body>';
            echo '<table border="1">';
            echo '<thead><tr>';

            foreach ($this->exportHeadings() as $heading) {
                echo '<th>'.e($heading).'</th>';
            }

            echo '</tr></thead><tbody>';

            foreach ($records as $record) {
                echo '<tr>';

                foreach ($this->exportRow($record) as $value) {
                    echo '<td style="mso-number-format:\'@\';">'.e($value).'</td>';
                }

                echo '</tr>';
            }

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
            'Layanan',
            'Password',
            'Profile',
            'IP',
            'Host Remote ONT',
            'Port Remote ONT',
            'URL Remote ONT',
            'Keterangan',
            'Status',
            'MikroTik ID',
        ];
    }

    private function exportRow(array $record): array
    {
        return [
            $record['mikrotik_config_name'] ?? $this->mikrotikConfigName($record['mikrotik_config_id'] ?? null),
            $record['name'] ?? '',
            $record['service'] ?? '',
            $record['password'] ?? '',
            $record['profile'] ?? '',
            $record['remote_address'] ?? '',
            $record['remote_ont_host'] ?? '',
            $record['remote_ont_port'] ?? '',
            $this->remoteOntUrl($record),
            $record['comment'] ?? '',
            ($record['disabled'] ?? false) ? 'Dinonaktifkan' : 'Aktif',
            $record['.id'] ?? '',
        ];
    }

    private function mikrotikConfigName(null|int|string $mikrotikConfigId): string
    {
        if (blank($mikrotikConfigId)) {
            return '';
        }

        $config = MikrotikConfig::find($mikrotikConfigId);

        if (! $config) {
            return '';
        }

        return $config->name ?: "{$config->nagari} - {$config->location}";
    }

    private function remoteOntUrl(array $record): string
    {
        if (blank($record['remote_ont_host'] ?? null) || blank($record['remote_ont_port'] ?? null)) {
            return '';
        }

        return "http://{$record['remote_ont_host']}:{$record['remote_ont_port']}";
    }

    protected static function mikrotikOptions(): array
    {
        return MikrotikConfig::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->orderBy('nagari')
            ->get()
            ->mapWithKeys(fn (MikrotikConfig $config) => [
                $config->id => $config->name ?: "{$config->nagari} - {$config->location}",
            ])
            ->all();
    }

    protected static function pppProfileOptions(null|int|string $mikrotikConfigId): array
    {
        if (blank($mikrotikConfigId)) {
            return [];
        }

        $config = MikrotikConfig::find($mikrotikConfigId);

        if (! $config) {
            return [];
        }

        try {
            return collect(Mikrotik::getPppProfiles($config))
                ->pluck('name', 'name')
                ->filter(fn (?string $profile): bool => filled($profile))
                ->all();
        } catch (\Exception $e) {
            Log::error('Failed to fetch PPP profiles for form options', [
                'mikrotik_config_id' => $config->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
