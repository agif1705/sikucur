<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaliKorong extends Model
{
 protected $fillable = [
  'nagari_id',
  'user_id',
  'name',
  'wilayah',
 ];

 public function nagari(): BelongsTo
 {
  return $this->belongsTo(Nagari::class);
 }

 public function user(): BelongsTo
 {
  return $this->belongsTo(User::class);
 }

 public static function wilayahToName(): array
 {
  return [
   'Bunga Tanjung' => 'Boy',
   'Durian Kadok' => 'Putra',
   'Sungai Janiah' => 'Pikal',
   'Lansano' => 'David',
  ];
 }
}