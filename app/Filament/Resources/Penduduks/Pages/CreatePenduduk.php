<?php

namespace App\Filament\Resources\Penduduks\Pages;

use App\Facades\Mikrotik;
use App\Filament\Resources\Penduduks\PendudukResource;
use App\Models\HotspotSikucur;
use App\Models\MikrotikConfig;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreatePenduduk extends CreateRecord
{
    protected static string $resource = PendudukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['nagari_id'] = Auth::user()->nagari->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        $penduduk = $this->record;
        $data = $this->data;

        if (empty($data['mikrotik_config_id'])) {
            return;
        }

        DB::transaction(function () use ($penduduk, $data) {
            try {
                $config = MikrotikConfig::find($data['mikrotik_config_id']);

                if (! $config) {
                    throw new \Exception('MikroTik configuration not found');
                }

                $resultConfig = Mikrotik::addHotspotUser(
                    $config,
                    $penduduk->nik,
                    $penduduk->no_hp,
                    ['comment' => $penduduk->name],
                );

                if (empty($resultConfig) || ! isset($resultConfig['after']['ret'])) {
                    throw new \Exception('Invalid MikroTik response');
                }

                HotspotSikucur::create([
                    'penduduk_id' => $penduduk->id,
                    'mikrotik_config_id' => $data['mikrotik_config_id'],
                    'ret_id' => $resultConfig['after']['ret'],
                    'phone_mikrotik' => $penduduk->no_hp,
                    'mikrotik_id' => $resultConfig['after']['ret'],
                    'status' => true,
                    'activated_at' => now(),
                    'expired_at' => now()->addDays(360),
                ]);

                Notification::make()
                    ->title('Berhasil')
                    ->body('Penduduk dan akses hotspot berhasil dibuat')
                    ->success()
                    ->send();
            } catch (\Exception $exception) {
                Log::error('Failed to create hotspot after penduduk creation', [
                    'penduduk_id' => $penduduk->id,
                    'error' => $exception->getMessage(),
                ]);

                Notification::make()
                    ->title('Peringatan')
                    ->body('Penduduk berhasil dibuat, tapi gagal membuat akses hotspot')
                    ->warning()
                    ->send();

                throw $exception;
            }
        });
    }
}
