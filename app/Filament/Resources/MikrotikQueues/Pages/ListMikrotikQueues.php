<?php

namespace App\Filament\Resources\MikrotikQueues\Pages;

use App\Facades\Mikrotik;
use App\Filament\Resources\MikrotikQueues\MikrotikQueueResource;
use App\Models\MikrotikConfig;
use App\Models\MikrotikQueue;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListMikrotikQueues extends ListRecords
{
    protected static string $resource = MikrotikQueueResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
                        $queues = Mikrotik::getSimpleQueues($config);

                        $synced = DB::transaction(function () use ($config, $queues): int {
                            MikrotikQueue::where('mikrotik_config_id', $config->id)->delete();

                            $rows = collect($queues)
                                ->filter(fn (array $queue) => filled($queue['name'] ?? null))
                                ->map(fn (array $queue) => [
                                    'mikrotik_config_id' => $config->id,
                                    'ret_id' => $queue['.id'] ?? null,
                                    'name' => $queue['name'],
                                    'target' => $queue['target'] ?? $queue['target-addresses'] ?? null,
                                    'dst' => $queue['dst'] ?? null,
                                    'parent' => self::emptyParentToNull($queue['parent'] ?? null),
                                    'packet_marks' => $queue['packet-marks'] ?? null,
                                    'priority' => $queue['priority'] ?? null,
                                    'queue_type' => $queue['queue'] ?? null,
                                    'limit_at' => $queue['limit-at'] ?? null,
                                    'max_limit' => $queue['max-limit'] ?? null,
                                    'burst_limit' => $queue['burst-limit'] ?? null,
                                    'burst_threshold' => $queue['burst-threshold'] ?? null,
                                    'burst_time' => $queue['burst-time'] ?? null,
                                    'rate' => $queue['rate'] ?? null,
                                    'bytes' => $queue['bytes'] ?? null,
                                    'total_bytes' => $queue['total-bytes'] ?? null,
                                    'packets' => $queue['packets'] ?? null,
                                    'total_packets' => $queue['total-packets'] ?? null,
                                    'comment' => $queue['comment'] ?? null,
                                    'dynamic' => self::mikrotikBool($queue['dynamic'] ?? false),
                                    'disabled' => self::mikrotikBool($queue['disabled'] ?? false),
                                    'invalid' => self::mikrotikBool($queue['invalid'] ?? false),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ])
                                ->unique(fn (array $queue) => $queue['ret_id'] ?: $queue['name'])
                                ->values();

                            $rows->chunk(500)->each(fn ($chunk) => MikrotikQueue::insert($chunk->all()));

                            return $rows->count();
                        });

                        Notification::make()
                            ->title('Sinkron Berhasil')
                            ->body("Data lama dihapus, {$synced} queue terbaru berhasil disimpan. Queue parent tidak ikut disimpan.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Log::error('Failed to sync simple queues from MikroTik', [
                            'mikrotik_config_id' => $config->id,
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->title('Sinkron Gagal')
                            ->body('Gagal mengambil queue dari MikroTik: '.$e->getMessage())
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
                        ->options(fn () => self::mikrotikOptions())
                        ->searchable()
                        ->placeholder('Semua MikroTik'),
                ])
                ->action(fn (array $data): StreamedResponse => $this->exportExcel($data['mikrotik_config_id'] ?? null)),
        ];
    }

    private static function mikrotikOptions(): array
    {
        return MikrotikConfig::query()
            ->orderBy('name')
            ->orderBy('nagari')
            ->get()
            ->mapWithKeys(fn (MikrotikConfig $config) => [
                $config->id => $config->name ?: "{$config->nagari} - {$config->location}",
            ])
            ->all();
    }

    private static function emptyParentToNull(?string $parent): ?string
    {
        if (blank($parent) || $parent === 'none') {
            return null;
        }

        return $parent;
    }

    private static function mikrotikBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return $value === 'true' || $value === 'yes' || $value === '1' || $value === 1;
    }

    private function exportExcel(?int $mikrotikConfigId = null): StreamedResponse
    {
        $fileName = 'mikrotik-queues-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($mikrotikConfigId): void {
            echo '<html><head><meta charset="UTF-8"></head><body>';
            echo '<table border="1">';
            echo '<thead><tr>';

            foreach ($this->exportHeadings() as $heading) {
                echo '<th>'.e($heading).'</th>';
            }

            echo '</tr></thead><tbody>';

            MikrotikQueue::query()
                ->with('mikrotikConfig')
                ->when($mikrotikConfigId, fn ($query) => $query->where('mikrotik_config_id', $mikrotikConfigId))
                ->orderBy('mikrotik_config_id')
                ->orderBy('name')
                ->chunk(500, function ($queues): void {
                    foreach ($queues as $queue) {
                        echo '<tr>';

                        foreach ($this->exportRow($queue) as $value) {
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
            'Nama Queue',
            'Target',
            'Max Limit',
            'Rate',
            'Limit At',
            'Parent',
            'Queue Type',
            'Priority',
            'Bytes',
            'Total Bytes',
            'Packets',
            'Total Packets',
            'Comment',
            'Dynamic',
            'Disabled',
            'Invalid',
            'MikroTik ID',
            'Updated At',
        ];
    }

    private function exportRow(MikrotikQueue $queue): array
    {
        return [
            $queue->mikrotikConfig?->name,
            $queue->name,
            $queue->target,
            $queue->max_limit,
            $queue->rate,
            $queue->limit_at,
            $queue->parent,
            $queue->queue_type,
            $queue->priority,
            $queue->bytes,
            $queue->total_bytes,
            $queue->packets,
            $queue->total_packets,
            $queue->comment,
            $queue->dynamic ? 'Ya' : 'Tidak',
            $queue->disabled ? 'Ya' : 'Tidak',
            $queue->invalid ? 'Ya' : 'Tidak',
            $queue->ret_id,
            $queue->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
