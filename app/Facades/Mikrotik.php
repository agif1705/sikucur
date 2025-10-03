<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Services\MikrotikService setConfig(string $nagari, string $location)
 * @method static \RouterOS\Client getClient()
 * @method static array addHotspotUser(string $username, string $password, array $additionalParams = [])
 * @method static array removeHotspotUser(string $username)
 * @method static array|null getHotspotUser(string $username)
 * @method static array disableHotspotUser(string $username)
 * @method static array enableHotspotUser(string $username)
 * @method static array updateHotspotUserPassword(string $username, string $newPassword)
 * @method static array toggleHotspotUser(string $username, bool $enabled = true)
 * @method static array getAllHotspotUsers()
 * @method static array getActiveSessions()
 * @method static bool testConnection()
 */
class Mikrotik extends Facade
{
 protected static function getFacadeAccessor()
 {
  return \App\Services\MikrotikService::class;
 }
}
