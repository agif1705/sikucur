<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array addHotspotUser(\App\Models\MikrotikConfig $config, string $username, string $password, array $additionalParams = [])
 * @method static array removeHotspotUser(\App\Models\MikrotikConfig $config, string $username)
 * @method static array updateHotspotUserPassword(\App\Models\MikrotikConfig $config, string $username, string $newPassword)
 * @method static array toggleHotspotUser(\App\Models\MikrotikConfig $config, string $username, bool $enabled = true)
 * @method static array getAllHotspotUsers(\App\Models\MikrotikConfig $config)
 * @method static array getActiveSessions(\App\Models\MikrotikConfig $config)
 * @method static bool testConnection(\App\Models\MikrotikConfig $config)
 */
class Mikrotik extends Facade
{
 protected static function getFacadeAccessor()
 {
  return \App\Services\MikrotikService::class;
 }
}
