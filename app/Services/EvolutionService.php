<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Drop-in pengganti GowaService, pakai Evolution API v2.
 * Signature method sama supaya consumer tidak berubah.
 */
class EvolutionService
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected ?string $instance;

    public function __construct()
    {
        $this->baseUrl  = rtrim(config('services.evolution.base_url', 'http://localhost:8080'), '/');
        $this->apiKey   = config('services.evolution.api_key');
        $this->instance = config('services.evolution.instance');
    }

    /** Normalisasi nomor: hanya digit, 0 depan -> 62. */
    protected function number(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }
        return $digits;
    }

    protected function client()
    {
        return Http::withHeaders(['apikey' => $this->apiKey]);
    }

    public function sendText(string $phone, string $message, bool $isForwarded = false, int $duration = 86400)
    {
        $response = $this->client()->post("{$this->baseUrl}/message/sendText/{$this->instance}", [
            'number' => $this->number($phone),
            'text'   => $message,
        ]);
        return $response->json();
    }

    /** Kirim media (base64) via Evolution API. */
    protected function sendMedia(string $phone, string $path, string $mediatype, string $caption = '', string $filename = null)
    {
        if (!file_exists($path)) {
            throw new \Exception("File {$path} tidak ditemukan");
        }

        $filename = $filename ?: basename($path);

        $response = $this->client()->post("{$this->baseUrl}/message/sendMedia/{$this->instance}", [
            'number'    => $this->number($phone),
            'mediatype' => $mediatype,
            'mimetype'  => mime_content_type($path) ?: 'application/octet-stream',
            'caption'   => $caption,
            'fileName'  => $filename,
            'media'     => base64_encode(file_get_contents($path)),
        ]);
        return $response->json();
    }

    public function sendFile(string $phone, string $path, string $caption = '', bool $isForwarded = false, int $duration = 86400)
    {
        return $this->sendMedia($phone, $path, 'document', $caption);
    }

    public function sendImage(string $phone, string $imagePath, string $caption = '', bool $isForwarded = false, int $duration = 86400)
    {
        return $this->sendMedia($phone, $imagePath, 'image', $caption);
    }

    public function sendDocument(string $phone, string $documentPath, string $caption = '', string $filename = null, bool $isForwarded = false, int $duration = 3600)
    {
        return $this->sendMedia($phone, $documentPath, 'document', $caption, $filename);
    }
}
