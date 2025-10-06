<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MikrotikConfig;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class VoucherController extends Controller
{
    public function index(Request $request, string $nagari = 'sikucur', string $location = 'main'): JsonResponse
    {
        $mikrotikConfig = MikrotikConfig::getConfig($nagari, $location);

        if (!$mikrotikConfig) {
            return response()->json([
                'success' => false,
                'message' => 'Mikrotik configuration not found for the specified nagari and location.'
            ], 404);
        }

        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:50',
            'pin' => 'required|string|max:50',
        ]);

        $name = $request->input('nama');
        $pin = $request->input('pin');

        // Cek apakah voucher ada dan aktif
        $voucher = \App\Models\Voucher::where('code', $pin)
            ->where('name', $name)
            ->where('active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();
        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired voucher.'
            ], 401);
        }
        return $this->apiResponse(true, 'Voucher valid. Hotspot user created successfully.', [
            'success' => true,
            'message' => 'Voucher valid. Hotspot user created successfully.',
            'data' => [
                'name' => $voucher->name,
                'pin' => $voucher->code,
                'expires_at' => $voucher->expires_at,
            ]
        ]);
    }
}
