<?php

namespace App\Handlers;

use App\Models\Voucher;
use App\Contracts\WhatsAppCommandHandler;
use App\Facades\Mikrotik;
use App\Models\HotspotSikucur;
use App\Models\MikrotikConfig;

class VoucherTamuHandler implements WhatsAppCommandHandler
{
    public function handle($user, $chat, $data)
    {
        $nagari = $user->nagari->slug;
        $location = 'kantor';
        $config = MikrotikConfig::getConfig($nagari, $location);
        if ($user->roles()->first()->name === 'super_admin' || $user->roles()->first()->name === 'WaliNagari') {
            $voucherCode = $this->generateUniqueVoucherCode();
            $name = "tamu-" . strtoupper(bin2hex(random_bytes(2)));
            $expiresAt = now()->addHours(23)->setMinutes(0)->setSeconds(0);
            // Simpan ke tabel voucher
            $voucher = Voucher::create(
                [
                    'name' => $name,
                    'code' => $voucherCode,
                    'created_by' => $user->id,
                    'active' => true,
                    'expires_at' => $expiresAt,
                    'mikrotik_config_id' => $config->id
                ]
            );
            $addHotspot = Mikrotik::addHotspotVoucher(
                $config,
                $name,
                $voucherCode,
                $expiresAt,
                [
                    'profile' => 'default',
                    'comment' => "Voucher Tamu {$name} - {$user->name}"
                ]
            );
            $pesan = "Berikut adalah voucher hotspot untuk tamu Wali Nagari:\n\n";
            $pesan .= "Kode Voucher: *{$voucherCode}*\n";
            $pesan .= "Nama Pengguna: *{$name}*\n";
            $pesan .= "Masa Aktif: *23 Jam* (berakhir pada " . $expiresAt->format('d-m-Y H:i') . ")\n\n";
            $pesan .= "Silakan gunakan voucher ini untuk mengakses jaringan hotspot dari pemerintahan Nagari Sikucur. Terima kasih.";
            // Simpan ke tabel hotspot_sikucur
            return [
                'success' => true,
                'message' => $pesan,
                'data' => [
                    'voucher_code' => $voucherCode,
                    'username' => $name,
                    'expires_at' => $expiresAt->toDateTimeString(),
                    'mikrotik_response' => $addHotspot

                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menggunakan perintah ini.',
                'data' => null
            ];
        }
    }
    private function generateUniqueVoucherCode(int $maxAttempts = 10): string
    {
        $attempts = 0;

        do {
            // Generate 4-character alphanumeric code
            $voucherCode = $this->generateVoucherCode(4);
            $attempts++;

            // Check if code already exists in database
            if (!$this->voucherCodeExists($voucherCode)) {
                return $voucherCode;
            }
        } while ($attempts < $maxAttempts);

        // If still not unique after max attempts, add timestamp suffix
        return $this->generateVoucherCode(3) . substr(time(), -1);
    }

    private function generateVoucherCode(int $length = 4): string
    {

        $characters = '1234567890ABCDEFGHJKLMNPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $code;
    }
    private function voucherCodeExists(string $code): bool
    {
        return Voucher::where('code', $code)->exists();
    }
}
