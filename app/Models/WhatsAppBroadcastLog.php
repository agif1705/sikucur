<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppBroadcastLog extends Model
{
 use HasFactory;

 protected $fillable = [
  'whats_app_broadcast_id',
  'user_id',
  'penduduk_id',
  'recipient_type',
  'phone',
  'status', // 'sent', 'failed'
  'error_message',
  'response_data',
  'sent_at'
 ];

 protected $casts = [
  'response_data' => 'array',
  'sent_at' => 'datetime'
 ];

 public function broadcast(): BelongsTo
 {
  return $this->belongsTo(WhatsAppBroadcast::class, 'whats_app_broadcast_id');
 }

 public function user(): BelongsTo
 {
  return $this->belongsTo(User::class);
 }

 public function penduduk(): BelongsTo
 {
  return $this->belongsTo(Penduduk::class);
 }

 /**
  * Get the recipient (user or penduduk) based on recipient_type
  */
 public function recipient()
 {
  return $this->recipient_type === 'penduduk'
   ? $this->penduduk
   : $this->user;
 }

 /**
  * Get recipient name
  */
 public function getRecipientNameAttribute(): string
 {
  $recipient = $this->recipient();
  return $recipient ? $recipient->name : 'Tidak diketahui';
 }

 /**
  * Get recipient info (name + additional info)
  */
 public function getRecipientInfoAttribute(): string
 {
  if ($this->recipient_type === 'penduduk' && $this->penduduk) {
   return $this->penduduk->name . ' (Warga)';
  } elseif ($this->recipient_type === 'user' && $this->user) {
   $jabatan = $this->user->jabatan->name ?? 'Tidak ada jabatan';
   return $this->user->name . ' (' . $jabatan . ')';
  }

  return 'Tidak diketahui';
 }
}
