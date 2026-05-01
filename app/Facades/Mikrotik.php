<?php

namespace App\Facades;

use App\Services\MikrotikService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array addHotspotUser(\App\Models\MikrotikConfig $config, string $username, string $password, array $additionalParams = [])
 * @method static array removeHotspotUser(\App\Models\MikrotikConfig $config, string $username)
 * @method static array updateHotspotUserPassword(\App\Models\MikrotikConfig $config, string $username, string $newPassword)
 * @method static array toggleHotspotUser(\App\Models\MikrotikConfig $config, string $username, bool $enabled = true)
 * @method static array getAllHotspotUsers(\App\Models\MikrotikConfig $config)
 * @method static array getActiveSessions(\App\Models\MikrotikConfig $config)
 * @method static array getDhcpServers(\App\Models\MikrotikConfig $config)
 * @method static array getDhcpLeases(\App\Models\MikrotikConfig $config)
 * @method static array getSimpleQueues(\App\Models\MikrotikConfig $config)
 * @method static array updateSimpleQueue(\App\Models\MikrotikConfig $config, string $queueId, array $data)
 * @method static array updateRemoteOntNat(\App\Models\MikrotikConfig $config, string $targetIp, string $comment = 'REMOTE-Client-Ont', string $dstPort = '1709', string $dstAddress = '192.168.200.1')
 * @method static array addDhcpLease(\App\Models\MikrotikConfig $config, array $data)
 * @method static array updateDhcpLease(\App\Models\MikrotikConfig $config, string $leaseId, array $data)
 * @method static array removeDhcpLease(\App\Models\MikrotikConfig $config, string $leaseId)
 * @method static bool testConnection(\App\Models\MikrotikConfig $config)
 */
class Mikrotik extends Facade
{
    protected static function getFacadeAccessor()
    {
        return MikrotikService::class;
    }
}
