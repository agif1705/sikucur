<?php

namespace App\Filament\Resources\Penduduks\Pages;

use App\Facades\Mikrotik;
use App\Filament\Resources\Penduduks\PendudukResource;
use App\Models\MikrotikConfig;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EditPenduduk extends EditRecord
{
    protected static string $resource = PendudukResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $config = MikrotikConfig::find($data['mikrotik_config_id']);

        if ($config) {
            Mikrotik::addHotspotUser(
                $config,
                $data['nik'],
                $data['no_hp'],
                ['comment' => $data['name']],
            );
        }

        $data['nagari_id'] = Auth::user()->nagari->id;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus User Hotspot')
                ->modalDescription('User akan dihapus dari database dan MikroTik. Apakah Anda yakin?')
                ->action(function () {
                    $record = $this->getRecord();
                    $mikrotikSuccess = false;
                    $hotspot = $record->hotspotSikucur;

                    try {
                        if ($hotspot?->mikrotik_config_id) {
                            $config = MikrotikConfig::find($hotspot->mikrotik_config_id);

                            if ($config) {
                                Mikrotik::removeHotspotUser($config, (string) $record->nik);
                                $mikrotikSuccess = true;
                            }
                        }

                        Log::info('User removed from MikroTik successfully', [
                            'nik' => $record->nik,
                        ]);
                    } catch (\Exception $exception) {
                        Log::error('Failed to remove from MikroTik', [
                            'nik' => $record->nik,
                            'error' => $exception->getMessage(),
                        ]);
                    }

                    try {
                        if ($hotspot) {
                            $hotspot->delete();
                        }

                        $record->delete();

                        if ($mikrotikSuccess) {
                            Notification::make()
                                ->title('Berhasil Dihapus')
                                ->body('User berhasil dihapus dari MikroTik dan database.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Sebagian Berhasil')
                                ->body('User dihapus dari database, tapi gagal dihapus dari MikroTik. Silakan hapus manual dari router.')
                                ->warning()
                                ->send();
                        }

                        return redirect($this->getResource()::getUrl('index'));
                    } catch (\Exception $exception) {
                        Notification::make()
                            ->title('Gagal Menghapus')
                            ->body('Gagal menghapus user dari database: '.$exception->getMessage())
                            ->danger()
                            ->send();

                        throw $exception;
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
