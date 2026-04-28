<?php

namespace App\Services;

use App\Models\PermohonanSurat;
use App\Models\SuratPengantar;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class SuratPengantarNotificationService
{
    public function __construct(private readonly GowaService $gowaService) {}

    public function notifyPengantarSubmitted(SuratPengantar $pengantar): void
    {
        $pengantar->loadMissing(['waliKorong.user']);

        $phone = $pengantar->waliKorong?->user?->no_hp;
        if (! $phone) {
            return;
        }

        $downloadUrl = URL::temporarySignedRoute('surat.pengantar.download', now()->addMinutes(30), [
            'token' => $pengantar->token,
        ]);

        $message = "Surat pengantar wali korong/RT baru telah diisi.\n"
            ."Nama: {$pengantar->pemohon_nama}\n"
            ."NIK: {$pengantar->pemohon_nik}\n"
            ."Korong: {$pengantar->korong}\n"
            ."Keperluan: {$pengantar->keperluan}\n"
            ."Unduh PDF: {$downloadUrl}";

        $this->send($phone, $message, 'pengantar_submitted', $pengantar->id);
    }

    public function notifyPermohonanCreated(PermohonanSurat $permohonan): void
    {
        $permohonan->loadMissing(['suratPengantar.waliKorong.user']);

        $phone = $permohonan->suratPengantar?->waliKorong?->user?->no_hp;
        if (! $phone) {
            return;
        }

        $message = "Permohonan surat baru sudah dibuat berdasarkan surat pengantar.\n"
            ."Nama: {$permohonan->pemohon_nama}\n"
            ."Korong: {$permohonan->suratPengantar?->korong}\n"
            ."Jenis Surat: {$permohonan->pemohon_judul_surat}\n"
            ."Nomor Permohonan: {$permohonan->nomor_permohonan}";

        $this->send($phone, $message, 'permohonan_created', $permohonan->id);
    }

    private function send(string $phone, string $message, string $context, int $recordId): void
    {
        try {
            $this->gowaService->sendText($phone, $message);
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengirim notifikasi WhatsApp surat.', [
                'context' => $context,
                'record_id' => $recordId,
                'phone' => $phone,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
