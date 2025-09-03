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
}
