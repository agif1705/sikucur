<?php

namespace App\Handlers;

use App\Contracts\WhatsAppCommandHandler;
use App\Models\Nagari;
use App\Models\Penduduk;
use App\Models\SuratPengantar;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SuratPengantarHandler implements WhatsAppCommandHandler
{
    public function handle($user, $chat, $data)
    {
        $user->loadMissing(['jabatan', 'roles', 'nagari']);

        if (! $this->canGenerateSuratLink($user)) {
            return [
                'success' => true,
                'message' => 'Perintah *surat* hanya untuk Kasi Pelayanan dan Staf Pelayanan.',
                'data' => [
                    'pegawai' => true,
                    'data' => $data,
                ],
            ];
        }

        $existing = SuratPengantar::where('petugas_id', $user->id)
            ->where('used', false)
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if ($existing) {
            $url = $this->generatePengantarUrl($existing->token, $existing->expired_at);
            $templateUrl = route('surat.pengantar.template');

            return [
                'success' => true,
                'message' => $this->messageWhatsApp($user->name, $user->nagari->name, $url, $templateUrl, $chat),
                'data' => [
                    'pegawai' => true,
                    'data' => $data,
                ],
            ];
        }

        $token = $this->generateUniqueToken();
        $expiredAt = now()->addMinutes(30);

        SuratPengantar::create([
            'nagari_id' => $user->nagari_id,
            'petugas_id' => $user->id,
            'token' => $token,
            'expired_at' => $expiredAt,
            'status' => SuratPengantar::STATUS_DRAFT,
            'used' => false,
        ]);

        $url = $this->generatePengantarUrl($token, $expiredAt);
        $templateUrl = route('surat.pengantar.template');

        return [
            'success' => true,
            'message' => $this->messageWhatsApp($user->name, $user->nagari->name, $url, $templateUrl, $chat),
            'data' => [
                'pegawai' => true,
                'data' => $data,
            ],
        ];
    }

    public function handleWarga(Penduduk $penduduk, string $chat, array $data): array
    {
        $existing = SuratPengantar::where('penduduk_id', $penduduk->id)
            ->whereNull('petugas_id')
            ->where('used', false)
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if ($existing) {
            $existing->fill([
                'pemohon_nik' => $penduduk->nik,
                'pemohon_nama' => $penduduk->name,
                'pemohon_alamat' => $penduduk->alamat,
                'pemohon_alamat_domisili' => $penduduk->alamat_domisili,
                'pemohon_telepon' => $penduduk->no_hp,
                'korong' => $penduduk->korong,
            ]);
            $existing->save();

            $url = $this->generatePengantarUrl($existing->token, $existing->expired_at);
            $templateUrl = route('surat.pengantar.template');

            return [
                'success' => true,
                'message' => $this->messageWhatsAppWarga($penduduk->name, $url, $templateUrl, $chat),
                'data' => [
                    'pegawai' => false,
                    'warga' => true,
                    'data' => $data,
                ],
            ];
        }

        $token = $this->generateUniqueToken();
        $expiredAt = now()->addMinutes(30);

        SuratPengantar::create([
            'nagari_id' => $penduduk->nagari_id,
            'penduduk_id' => $penduduk->id,
            'token' => $token,
            'expired_at' => $expiredAt,
            'status' => SuratPengantar::STATUS_DRAFT,
            'used' => false,
            'pemohon_nik' => $penduduk->nik,
            'pemohon_nama' => $penduduk->name,
            'pemohon_alamat' => $penduduk->alamat,
            'pemohon_alamat_domisili' => $penduduk->alamat_domisili,
            'pemohon_telepon' => $penduduk->no_hp,
            'korong' => $penduduk->korong,
        ]);

        $url = $this->generatePengantarUrl($token, $expiredAt);
        $templateUrl = route('surat.pengantar.template');

        return [
            'success' => true,
            'message' => $this->messageWhatsAppWarga($penduduk->name, $url, $templateUrl, $chat),
            'data' => [
                'pegawai' => false,
                'warga' => true,
                'data' => $data,
            ],
        ];
    }

    public function handleWargaTanpaNomor(string $chat, array $data): array
    {
        $nagariId = Nagari::query()->value('id') ?? 1;
        $senderPhone = $this->extractPhoneFromSender($data['sender'] ?? null);

        $existing = SuratPengantar::where('nagari_id', $nagariId)
            ->whereNull('penduduk_id')
            ->whereNull('petugas_id')
            ->where('used', false)
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if ($existing) {
            if (! $existing->pemohon_telepon && $senderPhone) {
                $existing->pemohon_telepon = $senderPhone;
                $existing->save();
            }

            $url = $this->generatePengantarUrl($existing->token, $existing->expired_at);
            $templateUrl = route('surat.pengantar.template');

            return [
                'success' => true,
                'message' => $this->messageWhatsAppWargaTanpaNomor($url, $templateUrl, $chat),
                'data' => [
                    'pegawai' => false,
                    'warga' => true,
                    'data' => $data,
                ],
            ];
        }

        $token = $this->generateUniqueToken();
        $expiredAt = now()->addMinutes(30);

        SuratPengantar::create([
            'nagari_id' => $nagariId,
            'token' => $token,
            'expired_at' => $expiredAt,
            'status' => SuratPengantar::STATUS_DRAFT,
            'used' => false,
            'pemohon_telepon' => $senderPhone,
        ]);

        $url = $this->generatePengantarUrl($token, $expiredAt);
        $templateUrl = route('surat.pengantar.template');

        return [
            'success' => true,
            'message' => $this->messageWhatsAppWargaTanpaNomor($url, $templateUrl, $chat),
            'data' => [
                'pegawai' => false,
                'warga' => true,
                'data' => $data,
            ],
        ];
    }

    private function generatePengantarUrl(string $token, $expiredAt): string
    {
        return URL::temporarySignedRoute('surat.pengantar.form', $expiredAt, [
            'token' => $token,
        ]);
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = Str::random(32);
            $exists = SuratPengantar::where('token', $token)->exists();
        } while ($exists);

        return $token;
    }

    private function canGenerateSuratLink($user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Kasi Pelayanan', 'Staf Pelayanan'])
         || in_array($user->jabatan?->name, ['Kasi Pelayanan', 'Staf Pelayanan'], true);
    }

    private function messageWhatsApp(string $name, string $nagari, string $url, string $templateUrl, string $chat): string
    {
        return "Halo {$name} di Nagari *{$nagari}*.\n".
         "Silakan isi *Surat Pengantar Wali Korong* melalui link berikut:\n{$url}\n\n".
         "Template kosong (PDF) dapat diunduh di:\n{$templateUrl}\n\n".
         "Link berlaku 30 menit. Jika habis, ketik lagi: {$chat}.";
    }

    private function messageWhatsAppWarga(string $name, string $url, string $templateUrl, string $chat): string
    {
        return "Halo {$name}.\n".
         "Nomor WhatsApp Anda terdaftar sebagai warga. Silakan isi *Surat Pengantar Wali Korong* melalui link berikut:\n{$url}\n\n".
         "Template kosong (PDF) dapat diunduh di:\n{$templateUrl}\n\n".
         "Link berlaku 30 menit. Jika habis, ketik lagi: {$chat}.";
    }

    private function messageWhatsAppWargaTanpaNomor(string $url, string $templateUrl, string $chat): string
    {
        return "Nomor WhatsApp Anda belum terhubung ke data penduduk.\n".
         "Silakan isi form *Surat Pengantar Wali Korong* melalui link berikut:\n{$url}\n\n".
         "Pastikan NIK yang diisi sudah terdaftar pada data penduduk.\n".
         "Jika NIK tidak ditemukan, proses akan ditolak dengan pesan: Daftarkan NIK/ kependudukan ada di Kantor Nagari.\n\n".
         "Template kosong (PDF) dapat diunduh di:\n{$templateUrl}\n\n".
         "Link berlaku 30 menit. Jika habis, ketik lagi: {$chat}.";
    }

    private function extractPhoneFromSender(?string $sender): ?string
    {
        if (! $sender) {
            return null;
        }

        $phone = Str::before($sender, '@');
        $digits = preg_replace('/\D+/', '', $phone);

        if (! $digits) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '62') && str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return $digits;
    }
}
