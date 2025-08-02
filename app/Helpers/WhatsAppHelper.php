<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class WhatsAppHelper
{
    /**
     * Mengirim data ke API Fonnte
     */

    public static function sendToFonnte(string $target, string $massage): array
    {
        $baduo = " \n \n \n \n _Sent || via *Cv.Baduo Mitra Solustion*_";
        // $response = Http::withHeaders([
        //     'Authorization' => config('services.fonnte.token') // Ganti dengan token Anda
        // ])->post('https://api.fonnte.com/send', [
        //     'target' => $target,
        //     'message' => $massage . " " . $baduo,
        // ]);

        // // Handle response
        // if ($response->successful()) {
        //     // Request berhasil
        //     $responseData = $response->json();
        //     return $responseData; // atau proses sesuai kebutuhan
        // } else {
        //     // Request gagal
        //     $errorCode = $response->status();
        //     $errorMessage = $response->body();

        //     // Anda bisa throw exception atau handle error sesuai kebutuhan
        //     throw new \Exception("Request failed with status {$errorCode}: {$errorMessage}");
        // }


        try {
            $response = Http::withHeaders([
                'Authorization' => config('services.fonnte.token')
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $massage . " " . $baduo,
            ]);

            $response->throw();

            return ['success' => true, 'data' => $response->json()];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => method_exists($e, 'getCode') ? $e->getCode() : 500
            ];
        }
    }
}
