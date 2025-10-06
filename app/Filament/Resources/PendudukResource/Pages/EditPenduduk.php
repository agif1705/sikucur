<?php

namespace App\Filament\Resources\PendudukResource\Pages;

use Filament\Actions;
use App\Facades\Mikrotik;
use App\Models\MikrotikConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\PendudukResource;

class EditPenduduk extends EditRecord
{
    protected static string $resource = PendudukResource::class;
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $config = MikrotikConfig::find($data['mikrotik_config_id']);
        Mikrotik::addHotspotUser(
            $config,
            $data['nik'],
            $data['no_hp'],
            ['comment' => $data['name']]
        );
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
                    dd($record->mikrotik_config_id);

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
    protected function getRedirectUrl(): string
    {
        // redirect ke list setelah create
        return $this->getResource()::getUrl('index');
    }
}
