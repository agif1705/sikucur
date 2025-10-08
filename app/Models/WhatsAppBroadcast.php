<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppBroadcast extends Model
{
 use HasFactory;

 protected $fillable = [
  'title',
  'message',
  'attachment_path',
  'attachment_type',
  'attachment_name',
  'sender_id',
  'target_type', // 'all', 'nagari', 'jabatan', 'custom', 'penduduk'
  'target_ids', // JSON array of IDs when target_type is not 'all'
  'total_recipients',
  'total_sent',
  'total_failed',
  'status', // 'draft', 'sending', 'completed', 'failed'
  'sent_at',
  'completed_at'
 ];

 protected $casts = [
  'target_ids' => 'array',
  'sent_at' => 'datetime',
  'completed_at' => 'datetime'
 ];

 public function sender(): BelongsTo
 {
  return $this->belongsTo(User::class, 'sender_id');
 }

 public function logs(): HasMany
 {
  return $this->hasMany(WhatsAppBroadcastLog::class);
 }

 public function getStatusBadgeAttribute(): string
 {
  return match ($this->status) {
   'draft' => 'Draft',
   'sending' => 'Mengirim...',
   'completed' => 'Selesai',
   'failed' => 'Gagal',
   default => 'Unknown'
  };
 }

 public function getSuccessRateAttribute(): float
 {
  if ($this->total_recipients == 0) return 0;
  return ($this->total_sent / $this->total_recipients) * 100;
 }
}
