<?php

namespace App\Handlers;

use App\Models\Voucher;
use App\Contracts\WhatsAppCommandHandler;
use App\Facades\Mikrotik;
use App\Models\HotspotSikucur;
use App\Models\MikrotikConfig;
use Illuminate\Support\Facades\Log;
use Exception;

class VoucherTamuHandler implements WhatsAppCommandHandler
{
    public function handle($user, $chat, $dataSender)
    {
        $nagari = $user->nagari->slug;
        $location = 'kantor';
        $config = MikrotikConfig::getConfig($nagari, $location);

        if (!$config) {
            return [
                'success' => false,
                'message' => 'Konfigurasi MikroTik tidak ditemukan. Silakan hubungi administrator.',
                'data' => ['data' => $dataSender]
            ];
        }

        if ($user->roles()->first()->name === 'super_admin' || $user->roles()->first()->name === 'WaliNagari') {
            try {
                return \DB::transaction(function () use ($user, $config, $dataSender) {
                    $voucherCode = $this->generateUniqueVoucherCode();
                    $name = "tamu-" . strtoupper(bin2hex(random_bytes(2)));
                    $expiresAt = now()->addHours(23)->setMinutes(0)->setSeconds(0);

                    // Simpan ke tabel voucher terlebih dahulu
                    $voucher = Voucher::create([
                        'name' => $name,
                        'code' => $voucherCode,
                        'created_by' => $user->id,
                        'active' => true,
                        'expires_at' => $expiresAt,
                        'mikrotik_config_id' => $config->id
                    ]);

                    // Coba tambahkan ke MikroTik dengan retry
                    $addHotspot = $this->addHotspotWithRetry(
                        $config,
                        $name,
                        $voucherCode,
                        $expiresAt,
                        $user->name,
                        3 // Max retry attempts
                    );

                    if (!$addHotspot['success']) {
                        // Jika gagal tambah ke MikroTik, hapus voucher dari database
                        $voucher->delete();

                        return [
                            'success' => false,
                            'message' => "Gagal membuat voucher di MikroTik setelah beberapa percobaan.\n\n" .
                                "Error: {$addHotspot['error']}\n\n" .
                                "Silakan coba lagi atau hubungi administrator jika masalah berlanjut.",
                            'data' => ['data' => $dataSender]
                        ];
                    }

                    $pesan = "✅ Voucher hotspot berhasil dibuat!\n\n";
                    $pesan .= "🎫 Kode Voucher: *{$voucherCode}*\n";
                    $pesan .= "👤 Nama Pengguna: *{$name}*\n";
                    $pesan .= "⏰ Masa Aktif: *23 Jam*\n";
                    $pesan .= "📅 Berakhir: " . $expiresAt->format('d-m-Y H:i') . "\n\n";
                    $pesan .= "📶 Silakan gunakan voucher ini untuk mengakses jaringan hotspot Nagari Sikucur.\n\n";
                    $pesan .= "Terima kasih! 🙏";

                    return [
                        'success' => true,
                        'message' => $pesan,
                        'data' => [
                            'pegawai' => true,
                            'data' => $dataSender,
                            'voucher_code' => $voucherCode,
                            'username' => $name,
                            'expires_at' => $expiresAt->toDateTimeString(),
                            'mikrotik_response' => $addHotspot['response'],
                            'retry_count' => $addHotspot['retry_count']
                        ]
                    ];
                });
            } catch (Exception $e) {
                Log::error('VoucherTamuHandler error', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return [
                    'success' => false,
                    'message' => "Terjadi kesalahan sistem saat membuat voucher.\n\n" .
                        "Silakan coba lagi dalam beberapa saat atau hubungi administrator.",
                    'data' => ['data' => $dataSender]
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => '❌ Anda tidak memiliki izin untuk menggunakan perintah ini.\n\n' .
                    'Perintah ini hanya dapat digunakan oleh Super Admin atau Wali Nagari.',
                'data' => ['data' => $dataSender]
            ];
        }
    }

    /**
     * Add hotspot voucher with retry mechanism
     */
    private function addHotspotWithRetry($config, $name, $voucherCode, $expiresAt, $userName, $maxRetries = 3): array
    {
        $lastError = '';

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::info("MikroTik voucher creation attempt {$attempt}", [
                    'config' => $config->nagari . '-' . $config->location,
                    'username' => $name,
                    'code' => $voucherCode
                ]);

                $response = Mikrotik::addHotspotVoucher(
                    $config,
                    $name,
                    $voucherCode,
                    $expiresAt,
                    [
                        'profile' => 'default',
                        'comment' => "Voucher Tamu {$name} - {$userName}"
                    ]
                );
                return [
                    'success' => true,
                    'response' => $response,
                    'retry_count' => $attempt
                ];
            } catch (Exception $e) {
                $lastError = $e->getMessage();

                Log::warning("MikroTik voucher creation failed on attempt {$attempt}", [
                    'username' => $name,
                    'error' => $lastError,
                    'attempt' => $attempt,
                    'max_retries' => $maxRetries
                ]);
                // Jika bukan attempt terakhir, tunggu sebentar sebelum retry
                if ($attempt < $maxRetries) {
                    sleep(2); // Wait 2 seconds before retry
                }
            }
        }

        // All attempts failed
        Log::error("MikroTik voucher creation failed after {$maxRetries} attempts", [
            'username' => $name,
            'final_error' => $lastError
        ]);

        return [
            'success' => false,
            'error' => $lastError,
            'retry_count' => $maxRetries
        ];
    }

    private function generateUniqueVoucherCode(int $maxAttempts = 10): string
    {
        $attempts = 0;

        do {
            $voucherCode = $this->generateVoucherCode(6);
            $attempts++;

            if (!$this->voucherCodeExists($voucherCode)) {
                return $voucherCode;
            }
        } while ($attempts < $maxAttempts);

        return $this->generateVoucherCode(3) . substr(time(), -1);
    }

    private function generateVoucherCode(int $length = 6): string
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
