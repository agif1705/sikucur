<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MikrotikConfig extends Model
{
 use HasFactory;

 protected $fillable = [
  'nagari',
  'location',
  'host',
  'user',
  'pass',
  'port',
  'ssl',
  'is_active',
 ];

 protected $casts = [
  'port' => 'integer',
  'ssl' => 'boolean',
  'is_active' => 'boolean',
 ];

 /**
  * Scope untuk config yang aktif
  */
 public function scopeActive($query)
 {
  return $query->where('is_active', true);
 }

 /**
  * Get config berdasarkan nagari dan location
  */
 public static function getConfig(string $nagari, string $location): ?self
 {
  return self::active()
   ->where('nagari', $nagari)
   ->where('location', $location)
   ->first();
 }
}
