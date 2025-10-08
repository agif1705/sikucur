<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GowaService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl  = config('services.gowa.base_url', 'http://localhost:3000');
        $this->username = config('services.gowa.username');
        $this->password = config('services.gowa.password');
    }

    /**
     * Kirim file (PDF, Excel, dsb) via Gowa API
     */
    public function sendFile(string $phone, string $path, string $caption = '', bool $isForwarded = false, int $duration = 3600)
    {
        if (!file_exists($path)) {
            throw new \Exception("File {$path} tidak ditemukan");
        }

        $response = Http::withBasicAuth($this->username, $this->password)
            ->attach('file', fopen($path, 'r'), basename($path))
            ->post($this->baseUrl . '/send/file', [
                'phone'        => "{$phone}@s.whatsapp.net",
                'caption'      => $caption,
                'is_forwarded' => $isForwarded ? 'true' : 'false',
                'duration'     => $duration,
            ]);

        return $response->json();
    }
    public function sendText(string $phone, string $message, bool $isForwarded = false, int $duration = 3600)
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->post($this->baseUrl . '/send/message', [
                'phone' => "{$phone}@s.whatsapp.net",
                'message' => $message,
                'is_forwarded' => $isForwarded ? true : false,
                'duration' => $duration,
            ]);
        return $response->json();
    }

    /**
     * Kirim gambar via Gowa API
     */
    public function sendImage(string $phone, string $imagePath, string $caption = '', bool $isForwarded = false, int $duration = 3600)
    {
        if (!file_exists($imagePath)) {
            throw new \Exception("File gambar {$imagePath} tidak ditemukan");
        }

        $response = Http::withBasicAuth($this->username, $this->password)
            ->attach('file', fopen($imagePath, 'r'), basename($imagePath))
            ->post($this->baseUrl . '/send/image', [
                'phone'        => "{$phone}@s.whatsapp.net",
                'caption'      => $caption,
                'is_forwarded' => $isForwarded ? 'true' : 'false',
                'duration'     => $duration,
            ]);

        return $response->json();
    }

    /**
     * Kirim dokumen via Gowa API
     */
    public function sendDocument(string $phone, string $documentPath, string $caption = '', string $filename = null, bool $isForwarded = false, int $duration = 3600)
    {
        if (!file_exists($documentPath)) {
            throw new \Exception("File dokumen {$documentPath} tidak ditemukan");
        }

        $filename = $filename ?: basename($documentPath);

        $response = Http::withBasicAuth($this->username, $this->password)
            ->attach('file', fopen($documentPath, 'r'), $filename)
            ->post($this->baseUrl . '/send/document', [
                'phone'        => "{$phone}@s.whatsapp.net",
                'caption'      => $caption,
                'filename'     => $filename,
                'is_forwarded' => $isForwarded ? 'true' : 'false',
                'duration'     => $duration,
            ]);

        return $response->json();
    }
}
