<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nagari;
use App\Models\VideoTv;
use App\Models\WdmsModel;
use Illuminate\Http\JsonResponse;

class TvAndroidController extends Controller
{
 public function index(string $slug): JsonResponse
 {
  $nagari = $this->resolveNagari($slug);

  if (!$nagari) {
   return response()->json([
    'message' => 'Nagari tidak ditemukan',
   ], 404);
  }

  return response()->json([
   'message' => 'Data TV berhasil diambil',
   'data' => [
    'nagari' => $this->mapNagari($nagari),
    'tv' => $this->mapTv($nagari),
    'absensi' => $this->mapAbsensi($nagari->sn_fingerprint),
    'videos' => $this->mapVideos($nagari->id),
    'gallery' => $this->mapGallery($nagari),
    'realtime' => $this->mapRealtimeConfig(),
   ],
   'meta' => [
    'generated_at' => now()->toIso8601String(),
   ],
  ]);
 }

 public function absensiHariIni(string $slug): JsonResponse
 {
  $nagari = $this->resolveNagari($slug);

  if (!$nagari) {
   return response()->json([
    'message' => 'Nagari tidak ditemukan',
   ], 404);
  }

  return response()->json([
   'message' => 'Data absensi berhasil diambil',
   'data' => $this->mapAbsensi($nagari->sn_fingerprint),
   'meta' => [
    'date' => now()->toDateString(),
   ],
  ]);
 }

 public function videos(string $slug): JsonResponse
 {
  $nagari = $this->resolveNagari($slug);

  if (!$nagari) {
   return response()->json([
    'message' => 'Nagari tidak ditemukan',
   ], 404);
  }

  return response()->json([
   'message' => 'Data video berhasil diambil',
   'data' => $this->mapVideos($nagari->id),
  ]);
 }

 public function gallery(string $slug): JsonResponse
 {
  $nagari = $this->resolveNagari($slug);

  if (!$nagari) {
   return response()->json([
    'message' => 'Nagari tidak ditemukan',
   ], 404);
  }

  return response()->json([
   'message' => 'Data galeri berhasil diambil',
   'data' => $this->mapGallery($nagari),
  ]);
 }

 public function realtimeConfig(string $slug): JsonResponse
 {
  $nagari = $this->resolveNagari($slug);

  if (!$nagari) {
   return response()->json([
    'message' => 'Nagari tidak ditemukan',
   ], 404);
  }

  return response()->json([
   'message' => 'Konfigurasi realtime berhasil diambil',
   'data' => $this->mapRealtimeConfig(),
  ]);
 }

 private function resolveNagari(string $slug): ?Nagari
 {
  return Nagari::with(['TvInformasi', 'galeri'])
   ->where('slug', $slug)
   ->first();
 }

 private function mapNagari(Nagari $nagari): array
 {
  return [
   'id' => $nagari->id,
   'name' => $nagari->name,
   'slug' => $nagari->slug,
   'logo_url' => $nagari->logo ? asset('storage/' . ltrim($nagari->logo, '/')) : null,
   'sn_fingerprint' => $nagari->sn_fingerprint,
  ];
 }

 private function mapTv(Nagari $nagari): array
 {
  $tv = $nagari->TvInformasi;

  return [
   'name' => $tv?->name,
   'running_text' => $tv?->running_text,
   'bupati_image_url' => $tv?->bupati_image ? asset('storage/' . ltrim($tv->bupati_image, '/')) : null,
   'bamus_image_url' => $tv?->bamus_image ? asset('storage/' . ltrim($tv->bamus_image, '/')) : null,
  ];
 }

 private function mapAbsensi(?string $snFingerprint): array
 {
  if (!$snFingerprint) {
   return [];
  }

  return WdmsModel::getAbsensiMasuk($snFingerprint)
   ->map(function ($item) {
    $status = $item['status'] ?? null;

    return [
     'user_id' => $item['user_id'] ?? null,
     'name' => $item['name'] ?? null,
     'slug' => $item['slug'] ?? null,
     'jabatan' => $item['jabatan'] ?? null,
     'time_only' => $item['time_only'] ?? null,
     'is_late' => (bool) ($item['is_late'] ?? false),
     'status' => $status,
     'status_label' => match ($status) {
      'HDDD' => 'Dinas Dalam Daerah',
      'HDLD' => 'Dinas Luar Daerah',
      'S' => 'Sakit',
      'I' => 'Izin',
      'C' => 'Cuti',
      'Hadir' => 'Hadir',
      default => $status,
     },
     'absensi_by' => $item['absensi_by'] ?? null,
     'image_url' => !empty($item['image'])
      ? asset('storage/' . ltrim($item['image'], '/'))
      : asset('storage/default-avatar.png'),
    ];
   })
   ->values()
   ->all();
 }

 private function mapVideos(int $nagariId): array
 {
  return VideoTv::query()
   ->where('nagari_id', $nagariId)
   ->where('is_active', true)
   ->orderBy('sort_order')
   ->orderByDesc('created_at')
   ->get()
   ->map(function (VideoTv $video) {
    return [
     'id' => $video->id,
     'title' => $video->title,
     'file_path' => $video->file_path,
     'url' => asset('storage/' . ltrim($video->file_path, '/')),
     'sort_order' => $video->sort_order,
     'is_active' => $video->is_active,
    ];
   })
   ->values()
   ->all();
 }

 private function mapGallery(Nagari $nagari): array
 {
  return $nagari->galeri
   ->sortByDesc('created_at')
   ->values()
   ->map(function ($item) {
    return [
     'id' => $item->id,
     'name' => $item->name,
     'image_url' => $item->image ? asset('storage/' . ltrim($item->image, '/')) : null,
     'created_at' => optional($item->created_at)?->toIso8601String(),
    ];
   })
   ->all();
 }

 private function mapRealtimeConfig(): array
 {
  return [
   'provider' => 'supabase',
   'url' => config('services.supabase.url'),
   'anon_key' => config('services.supabase.anon_key'),
   'channel' => config('services.supabase.channel', 'realtime_absensi_tv'),
   'events' => [
    [
     'schema' => 'public',
     'table' => 'iclock_transaction',
     'event' => 'INSERT',
    ],
    [
     'schema' => 'public',
     'table' => 'iclock_transaction',
     'event' => 'DELETE',
    ],
    [
     'schema' => 'public',
     'table' => 'laravel_rekap_absensi_pegawais',
     'event' => '*',
    ],
   ],
  ];
 }
}
