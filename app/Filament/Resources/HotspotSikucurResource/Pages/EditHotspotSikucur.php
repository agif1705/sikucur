<?php

namespace App\Filament\Resources\HotspotSikucurResource\Pages;

use Str;
use Filament\Actions;
use App\Facades\Mikrotik;
use App\Models\MikrotikConfig;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\HotspotSikucurResource;

class EditHotspotSikucur extends EditRecord
{
    protected static string $resource = HotspotSikucurResource::class;
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $config = MikrotikConfig::find($data['mikrotik_config_id']);
        try {
            $userName = Str::of($data['nik']);
            Mikrotik::toggleHotspotUser($config, $userName, $data['status']);
        } catch (\Exception $e) {
            Log::error('Failed to toggle user status in MikroTik', [
                'nik' => $data['nik'],
                'error' => $e->getMessage()
            ]);
        }
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
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

                    try {
                        $config = MikrotikConfig::find($record->mikrotik_config_id);
                        // Coba hapus dari MikroTik dulu
                        $nik = $record->penduduk->nik;
                        Mikrotik::removeHotspotUser($config, (string) $nik);
                        $mikrotikSuccess = true;
                        Log::info('User removed from MikroTik successfully', [
                            'nik' => $record->nik
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to remove from MikroTik', [
                            'nik' => $record->nik,
                            'error' => $e->getMessage()
                        ]);
                    }
                    try {
                        // Hapus dari database
                        $record->delete();
                        // Notifikasi berdasarkan hasil
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
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Menghapus')
                            ->body('Gagal menghapus user dari database: ' . $e->getMessage())
                            ->danger()
                            ->send();

                        throw $e;
                    }
                }),
        ];
    }
}
