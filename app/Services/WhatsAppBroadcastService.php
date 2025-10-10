<?php

namespace App\Services;

use App\Models\User;
use App\Models\Penduduk;
use App\Models\WhatsAppBroadcast;
use App\Models\WhatsAppBroadcastLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class WhatsAppBroadcastService
{
 protected $gowaService;

 public function __construct(GowaService $gowaService)
 {
  $this->gowaService = $gowaService;
 }

 /**
  * Create a new broadcast
  */
 public function createBroadcast(array $data): WhatsAppBroadcast
 {
  $recipients = $this->getRecipients($data['target_type'], $data['target_ids'] ?? []);

  return WhatsAppBroadcast::create([
   'title' => $data['title'],
   'message' => $data['message'],
   'attachment_path' => $data['attachment_path'] ?? null,
   'attachment_type' => $data['attachment_type'] ?? null,
   'attachment_name' => $data['attachment_name'] ?? null,
   'sender_id' => $data['sender_id'],
   'target_type' => $data['target_type'],
   'target_ids' => $data['target_ids'] ?? null,
   'total_recipients' => $recipients->count(),
   'status' => 'draft'
  ]);
 }

 /**
  * Send broadcast to recipients
  */
 public function sendBroadcast(WhatsAppBroadcast $broadcast): bool
 {
  try {
   $broadcast->update([
    'status' => 'sending',
    'sent_at' => now()
   ]);

   $recipients = $this->getRecipients($broadcast->target_type, $broadcast->target_ids ?? []);
   $totalSent = 0;
   $totalFailed = 0;

   foreach ($recipients as $recipient) {
    // Check if recipient is valid based on type
    if ($recipient instanceof User) {
     if (empty($recipient->no_hp) || !$recipient->aktif) {
      $this->logBroadcast($broadcast, $recipient, false, 'User tidak aktif atau nomor HP kosong');
      $totalFailed++;
      continue;
     }
    } elseif ($recipient instanceof Penduduk) {
     if (empty($recipient->no_hp)) {
      $this->logBroadcast($broadcast, $recipient, false, 'Penduduk tidak memiliki nomor HP');
      $totalFailed++;
      continue;
     }
    } else {
     $totalFailed++;
     continue;
    }

    try {
     // Personalisasi pesan
     $personalMessage = $this->personalizeMessage($broadcast->message, $recipient);

     // Kirim pesan dengan atau tanpa attachment
     $result = null;
     if (!empty($broadcast->attachment_path)) {
      // Kirim dengan attachment
      $attachmentPath = storage_path('app/public/' . $broadcast->attachment_path);

      if (file_exists($attachmentPath)) {
       if ($broadcast->attachment_type === 'image') {
        $result = $this->gowaService->sendImage($recipient->no_hp, $attachmentPath, $personalMessage);
       } else {
        $result = $this->gowaService->sendDocument($recipient->no_hp, $attachmentPath, $personalMessage, $broadcast->attachment_name);
       }
      } else {
       // File tidak ditemukan, kirim teks saja
       $result = $this->gowaService->sendText($recipient->no_hp, $personalMessage);
      }
     } else {
      // Kirim teks saja
      $result = $this->gowaService->sendText($recipient->no_hp, $personalMessage);
     }

     if ($result['code'] === "SUCCESS") {
      $this->logBroadcast($broadcast, $recipient, true, null, $result);
      $totalSent++;
     } else {
      $this->logBroadcast($broadcast, $recipient, false, $result['error'] ?? 'Gagal kirim', $result);
      $totalFailed++;
     }
    } catch (\Exception $e) {
     $this->logBroadcast($broadcast, $recipient, false, $e->getMessage());
     $totalFailed++;
    }

    // Delay untuk menghindari rate limiting
    usleep(500000); // 0.5 detik
   }

   // Update status broadcast
   $broadcast->update([
    'total_sent' => $totalSent,
    'total_failed' => $totalFailed,
    'status' => $totalFailed === 0 ? 'completed' : ($totalSent > 0 ? 'completed' : 'failed'),
    'completed_at' => now()
   ]);

   return true;
  } catch (\Exception $e) {
   $broadcast->update([
    'status' => 'failed',
    'completed_at' => now()
   ]);

   return false;
  }
 }

 /**
  * Get recipients based on target type
  */
 public function getRecipients(string $targetType, array $targetIds = []): Collection
 {
  return match ($targetType) {
   'all' => User::where('aktif', true)
    ->whereNotNull('no_hp')
    ->whereRaw("CAST(no_hp AS TEXT) != ''")
    ->with(['nagari', 'jabatan'])
    ->get(),

   'nagari' => User::where('aktif', true)
    ->whereNotNull('no_hp')
    ->whereRaw("CAST(no_hp AS TEXT) != ''")
    ->whereIn('nagari_id', $targetIds)
    ->with(['nagari', 'jabatan'])
    ->get(),

   'jabatan' => User::where('aktif', true)
    ->whereNotNull('no_hp')
    ->whereRaw("CAST(no_hp AS TEXT) != ''")
    ->whereIn('jabatan_id', $targetIds)
    ->with(['nagari', 'jabatan'])
    ->get(),

   'penduduk' => Penduduk::whereNotNull('no_hp')
    ->whereRaw("CAST(no_hp AS TEXT) != ''")
    ->get(),

   'custom' => User::where('aktif', true)
    ->whereNotNull('no_hp')
    ->whereRaw("CAST(no_hp AS TEXT) != ''")
    ->whereIn('id', $targetIds)
    ->with(['nagari', 'jabatan'])
    ->get(),

   default => collect([])
  };
 }

 /**
  * Personalize message with user data or penduduk data
  */
 protected function personalizeMessage(string $message, $recipient): string
 {
  if ($recipient instanceof User) {
   $replacements = [
    '{name}' => $recipient->name,
    '{nama}' => $recipient->name,
    '{jabatan}' => $recipient->jabatan->name ?? 'Tidak ada jabatan',
    '{nagari}' => $recipient->nagari->name ?? 'Tidak ada nagari',
   ];
  } elseif ($recipient instanceof Penduduk) {
   $replacements = [
    '{name}' => $recipient->name,
    '{nama}' => $recipient->name,
    '{jabatan}' => 'Warga',
    '{nagari}' => $recipient->nagari->name ?? 'Tidak ada nagari',
   ];
  } else {
   $replacements = [
    '{name}' => $recipient->name ?? 'Tidak diketahui',
    '{nama}' => $recipient->name ?? 'Tidak diketahui',
    '{jabatan}' => 'Tidak diketahui',
    '{nagari}' => 'Tidak diketahui',
   ];
  }

  return str_replace(array_keys($replacements), array_values($replacements), $message);
 }

 /**
  * Log broadcast result
  */
 protected function logBroadcast(
  WhatsAppBroadcast $broadcast,
  $recipient, // User or Penduduk
  bool $status,
  ?string $errorMessage = null,
  ?array $responseData = null
 ): void {
  $logData = [
   'whats_app_broadcast_id' => $broadcast->id,
   'phone' => $recipient->no_hp,
   'status' => $status,
   'error_message' => $errorMessage,
   'response_data' => $responseData,
   'sent_at' => $status ? now() : null
  ];

  if ($recipient instanceof User) {
   $logData['user_id'] = $recipient->id;
   $logData['recipient_type'] = 'user';
  } elseif ($recipient instanceof Penduduk) {
   $logData['penduduk_id'] = $recipient->id;
   $logData['recipient_type'] = 'penduduk';
  }

  WhatsAppBroadcastLog::create($logData);
 }

 /**
  * Get broadcast statistics
  */
 public function getBroadcastStats(): array
 {
  $totalBroadcasts = WhatsAppBroadcast::count();
  $completedBroadcasts = WhatsAppBroadcast::where('status', 'completed')->count();
  $totalRecipients = WhatsAppBroadcast::sum('total_recipients');
  $totalSent = WhatsAppBroadcast::sum('total_sent');

  return [
   'total_broadcasts' => $totalBroadcasts,
   'completed_broadcasts' => $completedBroadcasts,
   'total_recipients' => $totalRecipients,
   'total_sent' => $totalSent,
   'success_rate' => $totalRecipients > 0 ? ($totalSent / $totalRecipients) * 100 : 0
  ];
 }
}
