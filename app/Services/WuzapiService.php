<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WuzapiService
{
 protected string $baseUrl;
 protected string $token;

 public function __construct()
 {
  $this->baseUrl = rtrim(config('services.wuzapi.base_url', 'https://wuzapi.sikucur.com'));
  $this->token = (string) (config('services.wuzapi.token'));
 }

 protected function baseHeaders(): array
 {
  if ($this->token === '') {
   throw new \RuntimeException('WUZAPI token belum dikonfigurasi. Isi WUZAPI_TOKEN pada environment.');
  }

  return [
   'token' => $this->token,
  ];
 }

 protected function normalizePhone(string $phone): string
 {
  return preg_replace('/\D+/', '', $phone) ?: $phone;
 }

 protected function toDataUri(string $path, string $fallbackMime): string
 {
  $mime = mime_content_type($path) ?: $fallbackMime;
  return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
 }

 /**
  * Kirim file (PDF, Excel, dsb) via Wuzapi API
  */
 public function sendFile(string $phone, string $path, string $caption = '', bool $isForwarded = false, int $duration = 86400)
 {
  return $this->sendDocument($phone, $path, $caption, basename($path), $isForwarded, $duration);
 }

 public function sendText(string $phone, string $message, bool $isForwarded = false, int $duration = 86400)
 {
  $response = Http::withHeaders($this->baseHeaders())
   ->post($this->baseUrl . '/chat/send/text', [
    'Phone' => $this->normalizePhone($phone),
    'Body' => $message,
   ]);
  return $response->json();
 }

 /**
  * Kirim gambar via Wuzapi API
  */
 public function sendImage(string $phone, string $imagePath, string $caption = '', bool $isForwarded = false, int $duration = 86400)
 {
  if (!file_exists($imagePath)) {
   throw new \Exception("File gambar {$imagePath} tidak ditemukan");
  }

  $response = Http::withHeaders($this->baseHeaders())
   ->post($this->baseUrl . '/chat/send/image', [
    'Phone' => $this->normalizePhone($phone),
    'Image' => $this->toDataUri($imagePath, 'image/jpeg'),
    'Caption' => $caption,
   ]);

  return $response->json();
 }

 /**
  * Kirim dokumen via Wuzapi API
  */
 public function sendDocument(string $phone, string $documentPath, string $caption = '', string $filename = null, bool $isForwarded = false, int $duration = 3600)
 {
  if (!file_exists($documentPath)) {
   throw new \Exception("File dokumen {$documentPath} tidak ditemukan");
  }

  $filename = $filename ?: basename($documentPath);

  $payload = [
   'Phone' => $this->normalizePhone($phone),
   'Document' => $this->toDataUri($documentPath, 'application/octet-stream'),
   'FileName' => $filename,
  ];

  if ($caption !== '') {
   $payload['Caption'] = $caption;
  }

  $response = Http::withHeaders($this->baseHeaders())
   ->post($this->baseUrl . '/chat/send/document', $payload);

  return $response->json();
 }
}
