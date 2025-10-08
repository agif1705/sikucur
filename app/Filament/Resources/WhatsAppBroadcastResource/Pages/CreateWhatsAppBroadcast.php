<?php

namespace App\Filament\Resources\WhatsAppBroadcastResource\Pages;

use App\Filament\Resources\WhatsAppBroadcastResource;
use App\Services\WhatsAppBroadcastService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateWhatsAppBroadcast extends CreateRecord
{
    protected static string $resource = WhatsAppBroadcastResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Broadcast berhasil dibuat! Anda bisa mengirimnya sekarang.';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hitung jumlah recipients saat create
        $broadcastService = app(WhatsAppBroadcastService::class);
        $recipients = $this->getRecipientsCount($data);
        $data['total_recipients'] = $recipients;

        return $data;
    }

    private function getRecipientsCount(array $data): int
    {
        $broadcastService = app(WhatsAppBroadcastService::class);
        $recipients = $broadcastService->getRecipients($data['target_type'], $data['target_ids'] ?? []);
        return $recipients->count();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Preview Penerima')
                ->icon('heroicon-m-eye')
                ->color('info')
                ->modalHeading('Preview Penerima Broadcast')
                ->modalContent(function () {
                    $data = $this->form->getState();
                    if (empty($data['target_type'])) {
                        return view('filament.components.empty-preview');
                    }

                    $broadcastService = app(WhatsAppBroadcastService::class);
                    $recipients = $broadcastService->getRecipients($data['target_type'], $data['target_ids'] ?? []);

                    return view('filament.components.broadcast-preview', [
                        'recipients' => $recipients,
                        'count' => $recipients->count()
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup'),
        ];
    }
}
