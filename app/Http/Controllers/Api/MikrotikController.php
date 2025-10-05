<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use App\Models\HotspotSikucur;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\MikrotikConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\MikrotikService;
use Illuminate\Validation\ValidationException;

class MikrotikController extends Controller
{
    private const SUCCESS_MESSAGE = 'Selamat menikmati layanan internet gratis dari Pemerintahan Nagari Sikucur. Agar lebih bijak dalam penggunaan Internet (internet positif). Semua pemakaian akses internet terpantau dalam server kami. Kami akan memblokir jika terjadi pemakaian internet yang tidak sesuai dengan etika penggunaan internet. Jika terjadi pemblokiran silahkan datang ke Kantor Nagari Sikucur untuk melakukan pemulihan.';
    private const CITIZEN_NOT_FOUND_MESSAGE = 'Anda bukan warga Nagari Sikucur. Jika ingin mengakses layanan internet silahkan daftar ke Kantor Nagari Sikucur.';
    private const MIKROTIK_ERROR_MESSAGE = 'Gagal menambahkan user ke sistem. Silahkan coba lagi.';
    private const BLOCKED_USER_MESSAGE = 'Akun internet Anda telah diblokir karena melanggar ketentuan penggunaan internet. Silahkan datang ke Kantor Nagari Sikucur untuk melakukan pemulihan akses.';
    private const EXPIRED_USER_MESSAGE = 'Akses internet Anda telah berakhir. Silahkan daftar ulang di Kantor Nagari Sikucur untuk mendapatkan akses internet.';


    private MikrotikService $mikrotikService;

    public function __construct(MikrotikService $mikrotikService)
    {
        $this->mikrotikService = $mikrotikService;
    }

    /**
     * Handle hotspot user registration and authentication
     */
    public function index(Request $request, string $nagari = 'sikucur', string $location = 'main'): JsonResponse
    {
        try {
            // Get MikroTik config based on nagari and location
            $mikrotikConfig = MikrotikConfig::getConfig($nagari, $location);

            if (!$mikrotikConfig) {
                Log::warning('MikroTik config not found, trying to get default config', [
                    'nagari' => $nagari,
                    'location' => $location
                ]);

                // Try to get default sikucur-main config as fallback
                $mikrotikConfig = MikrotikConfig::getConfig('sikucur', 'main');

                if (!$mikrotikConfig) {
                    // Try sikucur-kantor as second fallback
                    $mikrotikConfig = MikrotikConfig::getConfig('sikucur', 'kantor');
                }

                if (!$mikrotikConfig) {
                    throw new Exception('No MikroTik configuration found. Please contact administrator.');
                }
            }

            // Validate request data
            $validatedData = $this->validateRequest($request);

            // Find citizen by NIK
            $penduduk = $this->findCitizen($validatedData['nik']);
            if (!$penduduk) {
                return $this->buildErrorResponse(
                    self::CITIZEN_NOT_FOUND_MESSAGE,
                    $validatedData,
                    400
                );
            }

            // Check existing hotspot access
            $existingHotspot = $this->getExistingHotspot($penduduk->id);

            // Check if user is blocked (status = false)
            if ($existingHotspot && !$existingHotspot->status) {
                return $this->buildErrorResponse(
                    self::BLOCKED_USER_MESSAGE,
                    $validatedData,
                    403
                );
            }

            // Check if user has valid access (status = true AND not expired)
            if ($this->hasValidAccess($existingHotspot)) {
                return $this->buildSuccessResponse(
                    self::SUCCESS_MESSAGE,
                    $validatedData
                );
            }

            // Check if user exists but expired
            if ($existingHotspot && $existingHotspot->status && $existingHotspot->expired_at && $existingHotspot->expired_at->isPast()) {
                return $this->buildErrorResponse(
                    self::EXPIRED_USER_MESSAGE,
                    $validatedData,
                    403
                );
            }

            // Create new hotspot user
            return $this->createHotspotUser($penduduk, $validatedData, $mikrotikConfig);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Data yang dikirim tidak valid.', [
                'code' => 422,
                'errors' => $e->errors(),
            ]);
        } catch (Exception $e) {
            Log::error('MikrotikController error: ' . $e->getMessage(), [
                'nagari' => $nagari ?? 'unknown',
                'location' => $location ?? 'unknown',
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return $this->apiResponse(false, self::MIKROTIK_ERROR_MESSAGE, [
                'code' => 500,
            ]);
        }
    }

    /**
     * Validate incoming request
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'nik' => 'required|string|size:16', // Exactly 16 characters
            'phone' => 'required|string|min:10|max:15',
        ]);
    }

    /**
     * Find citizen by NIK
     */
    private function findCitizen(string $nik): ?Penduduk
    {
        return Penduduk::where('nik', $nik)->first();
    }

    /**
     * Get existing hotspot record for citizen
     */
    private function getExistingHotspot(int $pendudukId): ?HotspotSikucur
    {
        return HotspotSikucur::where('penduduk_id', $pendudukId)->first();
    }

    /**
     * Check if citizen has valid access (status = true AND not expired)
     */
    private function hasValidAccess(?HotspotSikucur $hotspot): bool
    {
        return $hotspot &&
            $hotspot->status &&
            $hotspot->expired_at &&
            $hotspot->expired_at->isFuture();
    }

    /**
     * Create new hotspot user in MikroTik and database
     */
    private function createHotspotUser(Penduduk $penduduk, array $validatedData, MikrotikConfig $mikrotikConfig): JsonResponse
    {
        return DB::transaction(function () use ($penduduk, $validatedData, $mikrotikConfig) {
            try {
                // Create user in MikroTik using service with config
                $mikrotikResponse = $this->mikrotikService->addHotspotUser(
                    $mikrotikConfig,
                    $penduduk->nik,
                    $validatedData['phone']
                );

                // Validate MikroTik response
                if (empty($mikrotikResponse) || !isset($mikrotikResponse['after']['ret'])) {
                    throw new Exception('Failed to create user in MikroTik: Invalid response');
                }

                $mikrotikUserId = $mikrotikResponse['after']['ret'];

                // Save to database
                $this->saveHotspotRecord($penduduk->id, $mikrotikUserId, $validatedData['phone'], $mikrotikConfig->id);

                Log::info('Hotspot user created successfully', [
                    'nik' => $penduduk->nik,
                    'mikrotik_config' => $mikrotikConfig->nagari . '-' . $mikrotikConfig->location,
                    'mikrotik_user_id' => $mikrotikUserId
                ]);

                return $this->buildSuccessResponse(
                    self::SUCCESS_MESSAGE,
                    $validatedData,
                    $mikrotikResponse
                );
            } catch (Exception $e) {
                Log::error('Failed to create hotspot user: ' . $e->getMessage(), [
                    'nik' => $penduduk->nik,
                    'mikrotik_config_id' => $mikrotikConfig->id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Save hotspot record to database
     */
    private function saveHotspotRecord(int $pendudukId, string $mikrotikUserId, string $phone, int $mikrotikConfigId): HotspotSikucur
    {
        return HotspotSikucur::create([
            'penduduk_id' => $pendudukId,
            'mikrotik_config_id' => $mikrotikConfigId,
            'ret_id' => $mikrotikUserId,
            'phone_mikrotik' => $phone,
            'mikrotik_id' => $mikrotikUserId,
            'status' => true,
            'activated_at' => now(),
            'expired_at' => now()->addDays(360),
        ]);
    }

    /**
     * Build success response
     */
    private function buildSuccessResponse(string $message, array $validatedData, ?array $mikrotikResponse = null): JsonResponse
    {
        return $this->apiResponse(true, $message, [
            'code' => 200,
            'internet' => true,
            'username' => $validatedData['nik'],
            'password' => $validatedData['phone'],
            'responseMikrotik' => $mikrotikResponse,
        ]);
    }

    /**
     * Build error response
     */
    private function buildErrorResponse(string $message, array $validatedData, int $code, ?array $mikrotikResponse = null): JsonResponse
    {
        return $this->apiResponse(false, $message, [
            'code' => $code,
            'internet' => false,
            'username' => $validatedData['nik'],
            'password' => $validatedData['phone'],
            'responseMikrotik' => $mikrotikResponse,
        ]);
    }
}
