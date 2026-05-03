<?php

namespace App\Services;

use App\Models\MikrotikConfig;
use DateTime;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Query;

class MikrotikService
{
    /**
     * Get MikroTik client instance with specific config
     */
    private function getClientWithConfig(MikrotikConfig $config): Client
    {
        return new Client([
            'host' => $config->host,
            'user' => $config->user,
            'pass' => $config->pass,
            'port' => $config->port,
            'ssl' => $config->ssl,
            'ssl_options' => config('routeros-api.ssl_options', []),
            'legacy' => (bool) config('routeros-api.legacy', false),
            'timeout' => (int) config('routeros-api.timeout', 20),
            'socket_timeout' => (int) config('routeros-api.socket_timeout', 90),
            'socket_blocking' => (bool) config('routeros-api.socket_blocking', true),
            'socket_options' => config('routeros-api.socket_options', []),
            'attempts' => (int) config('routeros-api.attempts', 3),
            'delay' => (int) config('routeros-api.delay', 1),
        ]);
    }

    private function getRestBaseUrl(MikrotikConfig $config): string
    {
        if (filled($config->rest_url)) {
            return rtrim($config->rest_url, '/');
        }

        if (str_starts_with($config->host, 'http://') || str_starts_with($config->host, 'https://')) {
            return rtrim($config->host, '/');
        }

        if (filled($config->host)) {
            $scheme = $config->ssl ? 'https' : 'http';

            return "{$scheme}://{$config->host}";
        }

        if (filled(config('services.mikrotik.url'))) {
            return rtrim(config('services.mikrotik.url'), '/');
        }

        $scheme = $config->ssl ? 'https' : 'http';

        return "{$scheme}://{$config->host}";
    }

    private function restClient(MikrotikConfig $config): PendingRequest
    {
        $request = Http::withBasicAuth($config->user, $config->pass)
            ->acceptJson()
            ->asJson()
            ->connectTimeout((int) config('routeros-api.timeout', 20))
            ->timeout((int) config('routeros-api.rest_timeout', 30));

        if (! (bool) config('routeros-api.ssl_options.verify_peer', false)) {
            $request = $request->withoutVerifying();
        }

        return $request;
    }

    private function restUrl(MikrotikConfig $config, string $path): string
    {
        return $this->getRestBaseUrl($config).'/rest/'.ltrim($path, '/');
    }

    private function restGet(MikrotikConfig $config, string $path, array $query = []): array
    {
        $response = $this->restClient($config)
            ->get($this->restUrl($config, $path), $query);

        $response->throw();

        return $response->json() ?? [];
    }

    private function restPost(MikrotikConfig $config, string $path, array $data = []): array
    {
        $response = $this->restClient($config)
            ->post($this->restUrl($config, $path), $data);

        $response->throw();

        return $response->json() ?? [];
    }

    private function restPut(MikrotikConfig $config, string $path, array $data = []): array
    {
        $response = $this->restClient($config)
            ->put($this->restUrl($config, $path), $data);

        $response->throw();

        return $response->json() ?? [];
    }

    private function restPatch(MikrotikConfig $config, string $path, array $data = []): array
    {
        $response = $this->restClient($config)
            ->patch($this->restUrl($config, $path), $data);

        $response->throw();

        return $response->json() ?? [];
    }

    private function restDelete(MikrotikConfig $config, string $path): array
    {
        $response = $this->restClient($config)
            ->delete($this->restUrl($config, $path));

        $response->throw();

        return $response->json() ?? [];
    }

    /**
     * Add user to MikroTik hotspot
     */
    public function addHotspotUser(MikrotikConfig $config, string $username, string $password, array $additionalParams = []): array
    {
        try {
            $client = $this->getClientWithConfig($config);

            // First check if user already exists
            $findQuery = (new Query('/ip/hotspot/user/print'))
                ->where('name', $username);

            $existingUsers = $client->query($findQuery)->read();

            if (! empty($existingUsers)) {
                // User already exists, return existing user info
                Log::info('MikroTik user already exists, returning existing user', [
                    'config' => $config->nagari.'-'.$config->location,
                    'username' => $username,
                    'existing_user_id' => $existingUsers[0]['.id'],
                ]);

                // Return in same format as add response for consistency
                return [
                    'after' => [
                        'ret' => $existingUsers[0]['.id'],
                    ],
                    'existing_user' => true,
                    'user_data' => $existingUsers[0],
                ];
            }

            // User doesn't exist, create new user
            $query = (new Query('/ip/hotspot/user/add'))
                ->equal('name', $username)
                ->equal('password', $password);

            // Add additional parameters if provided
            foreach ($additionalParams as $key => $value) {
                $query->equal($key, $value);
            }

            $response = $client->query($query)->read();

            Log::info('MikroTik user added successfully', [
                'config' => $config->nagari.'-'.$config->location,
                'username' => $username,
                'response' => $response,
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to add MikroTik user', [
                'config' => $config->nagari.'-'.$config->location,
                'username' => $username,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function addHotspotVoucher(MikrotikConfig $config, string $username, string $password, DateTime $expiryDate, array $voucherOptions = []): array
    {
        try {
            $client = $this->getClientWithConfig($config);

            // Check if user already exists
            $findQuery = (new Query('/ip/hotspot/user/print'))
                ->where('name', $username);

            $existingUsers = $client->query($findQuery)->read();

            if (! empty($existingUsers)) {
                return [
                    'after' => ['ret' => $existingUsers[0]['.id']],
                    'existing_user' => true,
                    'user_data' => $existingUsers[0],
                ];
            }

            // Calculate uptime
            $currentTime = new DateTime;
            $uptimeSeconds = $expiryDate->getTimestamp() - $currentTime->getTimestamp();

            if ($uptimeSeconds <= 0) {
                throw new Exception('Expiry date must be in the future');
            }

            // Build query with voucher parameters
            $query = (new Query('/ip/hotspot/user/add'))
                ->equal('name', $username)
                ->equal('password', $password)
                ->equal('limit-uptime', $this->formatUptimeForMikrotik($uptimeSeconds));

            // Default voucher settings
            $defaultOptions = [
                'profile' => 'default', // User profile
                'comment' => 'Voucher created at '.$currentTime->format('Y-m-d H:i:s'),
            ];

            // Merge with provided options
            $options = array_merge($defaultOptions, $voucherOptions);

            // Add voucher-specific parameters
            foreach ($options as $key => $value) {
                $query->equal($key, $value);
            }

            // Optional: Add bandwidth or data limits
            if (isset($voucherOptions['limit-bytes-total'])) {
                $query->equal('limit-bytes-total', $voucherOptions['limit-bytes-total']);
            }

            if (isset($voucherOptions['limit-bytes-in'])) {
                $query->equal('limit-bytes-in', $voucherOptions['limit-bytes-in']);
            }

            if (isset($voucherOptions['limit-bytes-out'])) {
                $query->equal('limit-bytes-out', $voucherOptions['limit-bytes-out']);
            }

            $response = $client->query($query)->read();

            Log::info('MikroTik voucher created successfully', [
                'config' => $config->nagari.'-'.$config->location,
                'username' => $username,
                'expiry_date' => $expiryDate->format('Y-m-d H:i:s'),
                'uptime_duration' => $this->formatUptimeForMikrotik($uptimeSeconds),
                'options' => $options,
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to create MikroTik voucher', [
                'config' => $config->nagari.'-'.$config->location,
                'username' => $username,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function formatUptimeForMikrotik(int $seconds): string
    {
        if ($seconds <= 0) {
            return '0s'; // Jika sudah expired, set 0 detik
        }

        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        $parts = [];

        if ($days > 0) {
            $parts[] = $days.'d';
        }
        if ($hours > 0) {
            $parts[] = $hours.'h';
        }
        if ($minutes > 0) {
            $parts[] = $minutes.'m';
        }
        if ($remainingSeconds > 0 || empty($parts)) {
            $parts[] = $remainingSeconds.'s';
        }

        return implode('', $parts);
    }

    /**
     * Remove user from MikroTik hotspot
     */
    public function removeHotspotUser(MikrotikConfig $config, string $username): array
    {
        try {
            $client = $this->getClientWithConfig($config);

            // First find the user ID
            $findQuery = (new Query('/ip/hotspot/user/print'))
                ->where('name', $username);

            $users = $client->query($findQuery)->read();

            if (empty($users)) {
                throw new Exception("User '{$username}' not found in MikroTik");
            }

            $userId = $users[0]['.id'];

            // Remove the user
            $removeQuery = (new Query('/ip/hotspot/user/remove'))
                ->equal('.id', $userId);

            $response = $client->query($removeQuery)->read();

            Log::info('MikroTik user removed successfully', [
                'config' => $config->nagari.'-'.$config->location,
                'username' => $username,
                'user_id' => $userId,
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to remove MikroTik user', [
                'config' => $config->nagari.'-'.$config->location,
                'username' => $username,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update user password in MikroTik
     */
    public function updateHotspotUserPassword(MikrotikConfig $config, string $username, string $newPassword): array
    {
        try {
            $client = $this->getClientWithConfig($config);

            // First find the user ID
            $findQuery = (new Query('/ip/hotspot/user/print'))
                ->where('name', $username);

            $users = $client->query($findQuery)->read();

            if (empty($users)) {
                throw new Exception("User '{$username}' not found in MikroTik");
            }

            $userId = $users[0]['.id'];

            // Update the password
            $updateQuery = (new Query('/ip/hotspot/user/set'))
                ->equal('.id', $userId)
                ->equal('password', $newPassword);

            $response = $client->query($updateQuery)->read();

            Log::info('MikroTik user password updated successfully', [
                'config' => $config->nagari.'-'.$config->location,
                'username' => $username,
                'user_id' => $userId,
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to update MikroTik user password', [
                'config' => $config->nagari.'-'.$config->location,
                'username' => $username,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Enable/disable user in MikroTik
     */
    public function toggleHotspotUser(MikrotikConfig $config, string $username, bool $enabled = true): array
    {
        try {
            $client = $this->getClientWithConfig($config);

            // First find the user ID
            $findQuery = (new Query('/ip/hotspot/user/print'))
                ->where('name', $username);

            $users = $client->query($findQuery)->read();

            if (empty($users)) {
                throw new Exception("User '{$username}' not found in MikroTik");
            }

            $userId = $users[0]['.id'];

            // Enable/disable the user
            $updateQuery = (new Query('/ip/hotspot/user/set'))
                ->equal('.id', $userId)
                ->equal('disabled', $enabled ? 'false' : 'true');

            $response = $client->query($updateQuery)->read();

            // If disabling user, remove active sessions
            if (! $enabled) {
                $this->removeActiveSession($config, $username);
            }

            Log::info('MikroTik user status updated successfully', [
                'config' => $config->nagari.'-'.$config->location,
                'username' => $username,
                'user_id' => $userId,
                'enabled' => $enabled,
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to toggle MikroTik user status', [
                'config' => $config->nagari.'-'.$config->location,
                'username' => $username,
                'enabled' => $enabled,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function removeActiveSession(MikrotikConfig $config, string $username): void
    {
        try {
            $client = $this->getClientWithConfig($config);

            // Find active sessions for this user
            $findActiveQuery = (new Query('/ip/hotspot/active/print'))
                ->where('user', $username);

            $activeSessions = $client->query($findActiveQuery)->read();

            if (! empty($activeSessions)) {
                foreach ($activeSessions as $session) {
                    $sessionId = $session['.id'];

                    // Remove the active session
                    $removeQuery = (new Query('/ip/hotspot/active/remove'))
                        ->equal('.id', $sessionId);

                    $client->query($removeQuery)->read();

                    Log::info('Active session removed for user', [
                        'config' => $config->nagari.'-'.$config->location,
                        'username' => $username,
                        'session_id' => $sessionId,
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::warning('Failed to remove active session for user', [
                'config' => $config->nagari.'-'.$config->location,
                'username' => $username,
                'error' => $e->getMessage(),
            ]);
            // Don't throw exception here, just log the warning
            // User disable should still succeed even if session removal fails
        }
    }

    /**
     * Get all hotspot users
     */
    public function getAllHotspotUsers(MikrotikConfig $config): array
    {
        try {
            $client = $this->getClientWithConfig($config);
            $query = new Query('/ip/hotspot/user/print');

            $users = $client->query($query)->read();

            return $users;
        } catch (Exception $e) {
            Log::error('Failed to get all MikroTik users', [
                'config' => $config->nagari.'-'.$config->location,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get active sessions
     */
    public function getActiveSessions(MikrotikConfig $config): array
    {
        try {
            $client = $this->getClientWithConfig($config);
            $query = new Query('/ip/hotspot/active/print');

            $sessions = $client->query($query)->read();

            return $sessions;
        } catch (Exception $e) {
            Log::error('Failed to get active sessions', [
                'config' => $config->nagari.'-'.$config->location,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get all DHCP leases from MikroTik.
     */
    public function getDhcpLeases(MikrotikConfig $config): array
    {
        try {
            return $this->getDhcpLeasesViaRest($config);
        } catch (Exception $restException) {
            Log::warning('Failed to get DHCP leases via REST, falling back to RouterOS API', [
                'config' => $config->nagari.'-'.$config->location,
                'rest_url' => $this->getRestBaseUrl($config),
                'error' => $restException->getMessage(),
            ]);
        }

        try {
            $client = $this->getClientWithConfig($config);
            $query = (new Query('/ip/dhcp-server/lease/print'))
                ->equal('.proplist', '.id,mac-address,address,active-address,server,host-name,client-id,comment,dynamic,disabled,blocked,block-access,status,last-seen');

            return $client->query($query)->read();
        } catch (Exception $e) {
            Log::error('Failed to get DHCP leases from MikroTik', [
                'config' => $config->nagari.'-'.$config->location,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getDhcpServers(MikrotikConfig $config): array
    {
        try {
            return $this->restGet($config, 'ip/dhcp-server', [
                '.proplist' => '.id,name,interface,address-pool,lease-time,disabled,dynamic,invalid',
            ]);
        } catch (Exception) {
            return $this->restGet($config, 'ip/dhcp-server/print', [
                '.proplist' => '.id,name,interface,address-pool,lease-time,disabled,dynamic,invalid',
            ]);
        }
    }

    /**
     * Get simple queues from MikroTik, excluding queues that act as parents.
     */
    public function getSimpleQueues(MikrotikConfig $config): array
    {
        try {
            return $this->filterParentQueues($this->getSimpleQueuesViaRest($config));
        } catch (Exception $restException) {
            Log::warning('Failed to get simple queues via REST, falling back to RouterOS API', [
                'config' => $config->nagari.'-'.$config->location,
                'rest_url' => $this->getRestBaseUrl($config),
                'error' => $restException->getMessage(),
            ]);
        }

        try {
            $client = $this->getClientWithConfig($config);
            $query = (new Query('/queue/simple/print'))
                ->equal('.proplist', $this->simpleQueueProperties());

            return $this->filterParentQueues($client->query($query)->read());
        } catch (Exception $e) {
            Log::error('Failed to get simple queues from MikroTik', [
                'config' => $config->nagari.'-'.$config->location,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function getSimpleQueuesViaRest(MikrotikConfig $config): array
    {
        $properties = $this->simpleQueueProperties();

        try {
            return $this->restGet($config, 'queue/simple', [
                '.proplist' => $properties,
            ]);
        } catch (Exception) {
            return $this->restPost($config, 'queue/simple/print', [
                '.proplist' => explode(',', $properties),
            ]);
        }
    }

    private function simpleQueueProperties(): string
    {
        return '.id,name,target,dst,parent,packet-marks,priority,queue,limit-at,max-limit,burst-limit,burst-threshold,burst-time,rate,bytes,total-bytes,packets,total-packets,comment,dynamic,disabled,invalid';
    }

    private function filterParentQueues(array $queues): array
    {
        $parentNames = collect($queues)
            ->pluck('parent')
            ->filter(fn ($parent) => filled($parent) && $parent !== 'none')
            ->map(fn ($parent) => trim((string) $parent))
            ->unique()
            ->values()
            ->all();

        return collect($queues)
            ->reject(fn (array $queue) => in_array((string) ($queue['name'] ?? ''), $parentNames, true))
            ->values()
            ->all();
    }

    /**
     * Update a simple queue in MikroTik.
     */
    public function updateSimpleQueue(MikrotikConfig $config, string $queueId, array $data): array
    {
        try {
            return $this->updateSimpleQueueViaRest($config, $queueId, $data);
        } catch (Exception $restException) {
            Log::warning('Failed to update simple queue via REST, falling back to RouterOS API', [
                'config' => $config->nagari.'-'.$config->location,
                'queue_id' => $queueId,
                'error' => $restException->getMessage(),
            ]);
        }

        try {
            $client = $this->getClientWithConfig($config);
            $query = (new Query('/queue/simple/set'))
                ->equal('.id', $queueId);

            foreach ($this->simpleQueueWritableParams($data) as $key => $value) {
                $query->equal($key, $value);
            }

            $response = $client->query($query)->read();

            Log::info('Simple queue updated successfully', [
                'config' => $config->nagari.'-'.$config->location,
                'queue_id' => $queueId,
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to update simple queue', [
                'config' => $config->nagari.'-'.$config->location,
                'queue_id' => $queueId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function updateSimpleQueueViaRest(MikrotikConfig $config, string $queueId, array $data): array
    {
        return $this->restPatch($config, 'queue/simple/'.$queueId, $this->simpleQueueWritableParams($data));
    }

    private function simpleQueueWritableParams(array $data): array
    {
        $params = [];

        if (array_key_exists('name', $data)) {
            $params['name'] = trim((string) $data['name']);
        }

        return array_filter($params, fn ($value) => filled($value));
    }

    /**
     * Point the Remote ONT dst-nat rule to a selected client IP.
     */
    public function updateRemoteOntNat(
        MikrotikConfig $config,
        string $targetIp,
        ?string $comment = 'REMOTE-Client-Ont',
        string $dstPort = '1709',
        ?string $dstAddress = '192.168.200.1'
    ): array {
        try {
            return $this->updateRemoteOntNatViaRest($config, $targetIp, $comment, $dstPort, $dstAddress);
        } catch (Exception $restException) {
            Log::warning('Failed to update Remote ONT NAT via REST, falling back to RouterOS API', [
                'config' => $config->nagari.'-'.$config->location,
                'target_ip' => $targetIp,
                'comment' => $comment,
                'dst_port' => $dstPort,
                'dst_address' => $dstAddress,
                'error' => $restException->getMessage(),
            ]);
        }

        try {
            $client = $this->getClientWithConfig($config);
            $query = (new Query('/ip/firewall/nat/print'))
                ->equal('.proplist', '.id,comment,chain,action,protocol,dst-address,dst-port,to-addresses,disabled');

            $rule = $this->findRemoteOntNatRule($client->query($query)->read(), $comment, $dstPort, $dstAddress);

            if (! $rule) {
                throw new Exception("NAT rule dengan comment '{$comment}', dst-address {$dstAddress}, dan dst-port {$dstPort} tidak ditemukan.");
            }

            $updateQuery = (new Query('/ip/firewall/nat/set'))
                ->equal('.id', $rule['.id'])
                ->equal('to-addresses', $targetIp);

            $response = $client->query($updateQuery)->read();

            Log::info('Remote ONT NAT updated successfully', [
                'config' => $config->nagari.'-'.$config->location,
                'rule_id' => $rule['.id'],
                'target_ip' => $targetIp,
                'dst_port' => $dstPort,
                'dst_address' => $dstAddress,
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to update Remote ONT NAT', [
                'config' => $config->nagari.'-'.$config->location,
                'target_ip' => $targetIp,
                'comment' => $comment,
                'dst_port' => $dstPort,
                'dst_address' => $dstAddress,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function updateRemoteOntNatViaRest(MikrotikConfig $config, string $targetIp, ?string $comment, string $dstPort, ?string $dstAddress): array
    {
        $rules = $this->restGet($config, 'ip/firewall/nat', [
            '.proplist' => '.id,comment,chain,action,protocol,dst-address,dst-port,to-addresses,disabled',
        ]);

        $rule = $this->findRemoteOntNatRule($rules, $comment, $dstPort, $dstAddress);

        if (! $rule) {
            throw new Exception("NAT rule dengan comment '{$comment}', dst-address {$dstAddress}, dan dst-port {$dstPort} tidak ditemukan.");
        }

        return $this->restPatch($config, 'ip/firewall/nat/'.$rule['.id'], [
            'to-addresses' => $targetIp,
        ]);
    }

    private function findRemoteOntNatRule(array $rules, ?string $comment, string $dstPort, ?string $dstAddress): ?array
    {
        return collect($rules)
            ->first(function (array $rule) use ($comment, $dstPort, $dstAddress): bool {
                $ruleComment = (string) ($rule['comment'] ?? '');
                $ruleDstAddress = (string) ($rule['dst-address'] ?? '');
                $ruleDstPort = (string) ($rule['dst-port'] ?? '');

                return (blank($comment) || str_contains($ruleComment, $comment))
                    && self::remoteOntNatAddressMatches($ruleComment, $ruleDstAddress, $dstAddress)
                    && self::remoteOntNatPortMatches($ruleComment, $ruleDstPort, $dstPort);
            });
    }

    private static function remoteOntNatAddressMatches(string $comment, string $ruleDstAddress, ?string $dstAddress): bool
    {
        if (blank($dstAddress)) {
            return true;
        }

        if ($ruleDstAddress === $dstAddress) {
            return true;
        }

        return blank($ruleDstAddress) && str_contains($comment, $dstAddress);
    }

    private static function remoteOntNatPortMatches(string $comment, string $ruleDstPort, string $dstPort): bool
    {
        return $ruleDstPort === $dstPort;
    }

    private function getDhcpLeasesViaRest(MikrotikConfig $config): array
    {
        $properties = '.id,mac-address,address,active-address,server,host-name,client-id,comment,dynamic,disabled,blocked,block-access,status,last-seen';

        try {
            return $this->restGet($config, 'ip/dhcp-server/lease', [
                '.proplist' => $properties,
            ]);
        } catch (Exception) {
            return $this->restPost($config, 'ip/dhcp-server/lease/print', [
                '.proplist' => explode(',', $properties),
            ]);
        }
    }

    /**
     * Add or reuse a DHCP lease in MikroTik.
     */
    public function addDhcpLease(MikrotikConfig $config, array $data): array
    {
        try {
            return $this->addDhcpLeaseViaRest($config, $data);
        } catch (Exception $restException) {
            Log::warning('Failed to add DHCP lease via REST, falling back to RouterOS API', [
                'config' => $config->nagari.'-'.$config->location,
                'mac_address' => $data['mac_address'] ?? $data['mac-address'] ?? null,
                'error' => $restException->getMessage(),
            ]);
        }

        try {
            $client = $this->getClientWithConfig($config);
            $macAddress = $this->normalizeMacAddress($data['mac_address'] ?? $data['mac-address'] ?? '');

            $findQuery = (new Query('/ip/dhcp-server/lease/print'))
                ->where('mac-address', $macAddress)
                ->equal('.proplist', '.id,mac-address,address,server,host-name,client-id,comment,dynamic,disabled,blocked,block-access');

            $existingLeases = $client->query($findQuery)->read();

            if (! empty($existingLeases)) {
                $leaseId = $existingLeases[0]['.id'];

                if (($existingLeases[0]['dynamic'] ?? 'false') === 'true') {
                    $makeStaticQuery = (new Query('/ip/dhcp-server/lease/make-static'))
                        ->equal('.id', $leaseId);

                    $client->query($makeStaticQuery)->read();
                }

                $this->updateDhcpLease($config, $leaseId, $data);

                return [
                    'after' => [
                        'ret' => $leaseId,
                    ],
                    'existing_lease' => true,
                    'lease_data' => $existingLeases[0],
                ];
            }

            $query = (new Query('/ip/dhcp-server/lease/add'))
                ->equal('mac-address', $macAddress);

            foreach ($this->dhcpLeaseWritableParams($data) as $key => $value) {
                $query->equal($key, $value);
            }

            $response = $client->query($query)->read();

            Log::info('DHCP lease added successfully', [
                'config' => $config->nagari.'-'.$config->location,
                'mac_address' => $macAddress,
                'response' => $response,
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to add DHCP lease', [
                'config' => $config->nagari.'-'.$config->location,
                'mac_address' => $data['mac_address'] ?? $data['mac-address'] ?? null,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function addDhcpLeaseViaRest(MikrotikConfig $config, array $data): array
    {
        $macAddress = $this->normalizeMacAddress($data['mac_address'] ?? $data['mac-address'] ?? '');
        $existingLeases = $this->restGet($config, 'ip/dhcp-server/lease', [
            'mac-address' => $macAddress,
            '.proplist' => '.id,mac-address,address,server,host-name,client-id,comment,dynamic,disabled,blocked,block-access',
        ]);

        if (! empty($existingLeases)) {
            $lease = $existingLeases[0];
            $leaseId = $lease['.id'];

            if (($lease['dynamic'] ?? 'false') === 'true') {
                $this->restPost($config, 'ip/dhcp-server/lease/make-static', [
                    '.id' => $leaseId,
                ]);
            }

            $updatedLease = $this->updateDhcpLeaseViaRest($config, $leaseId, $data);

            return [
                'after' => [
                    'ret' => $leaseId,
                ],
                'existing_lease' => true,
                'lease_data' => $updatedLease ?: $lease,
            ];
        }

        $payload = array_merge(
            ['mac-address' => $macAddress],
            $this->dhcpLeaseWritableParams($data)
        );

        $lease = $this->restPut($config, 'ip/dhcp-server/lease', $payload);

        return [
            'after' => [
                'ret' => $lease['.id'] ?? null,
            ],
            'lease_data' => $lease,
        ];
    }

    /**
     * Update a DHCP lease in MikroTik.
     */
    public function updateDhcpLease(MikrotikConfig $config, string $leaseId, array $data): array
    {
        try {
            return $this->updateDhcpLeaseViaRest($config, $leaseId, $data);
        } catch (Exception $restException) {
            Log::warning('Failed to update DHCP lease via REST, falling back to RouterOS API', [
                'config' => $config->nagari.'-'.$config->location,
                'lease_id' => $leaseId,
                'error' => $restException->getMessage(),
            ]);
        }

        try {
            $client = $this->getClientWithConfig($config);
            $query = (new Query('/ip/dhcp-server/lease/set'))
                ->equal('.id', $leaseId);

            if (! empty($data['mac_address']) || ! empty($data['mac-address'])) {
                $query->equal('mac-address', $this->normalizeMacAddress($data['mac_address'] ?? $data['mac-address']));
            }

            foreach ($this->dhcpLeaseWritableParams($data) as $key => $value) {
                $query->equal($key, $value);
            }

            $response = $client->query($query)->read();

            Log::info('DHCP lease updated successfully', [
                'config' => $config->nagari.'-'.$config->location,
                'lease_id' => $leaseId,
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to update DHCP lease', [
                'config' => $config->nagari.'-'.$config->location,
                'lease_id' => $leaseId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function updateDhcpLeaseViaRest(MikrotikConfig $config, string $leaseId, array $data): array
    {
        $payload = $this->dhcpLeaseWritableParams($data);

        if (! empty($data['mac_address']) || ! empty($data['mac-address'])) {
            $payload['mac-address'] = $this->normalizeMacAddress($data['mac_address'] ?? $data['mac-address']);
        }

        try {
            return $this->restPatch($config, 'ip/dhcp-server/lease/'.$leaseId, $payload);
        } catch (RequestException $e) {
            if ($e->response->status() !== 404 || empty($payload['mac-address'])) {
                throw $e;
            }

            $freshLeaseId = $this->findDhcpLeaseIdByMacViaRest($config, $payload['mac-address']);

            if (! $freshLeaseId) {
                throw $e;
            }

            return $this->restPatch($config, 'ip/dhcp-server/lease/'.$freshLeaseId, $payload);
        }
    }

    private function findDhcpLeaseIdByMacViaRest(MikrotikConfig $config, string $macAddress): ?string
    {
        $leases = $this->restGet($config, 'ip/dhcp-server/lease', [
            'mac-address' => $this->normalizeMacAddress($macAddress),
            '.proplist' => '.id,mac-address',
        ]);

        return $leases[0]['.id'] ?? null;
    }

    /**
     * Remove a DHCP lease from MikroTik.
     */
    public function removeDhcpLease(MikrotikConfig $config, string $leaseId): array
    {
        try {
            return $this->restDelete($config, 'ip/dhcp-server/lease/'.$leaseId);
        } catch (Exception $restException) {
            Log::warning('Failed to remove DHCP lease via REST, falling back to RouterOS API', [
                'config' => $config->nagari.'-'.$config->location,
                'lease_id' => $leaseId,
                'error' => $restException->getMessage(),
            ]);
        }

        try {
            $client = $this->getClientWithConfig($config);
            $query = (new Query('/ip/dhcp-server/lease/remove'))
                ->equal('.id', $leaseId);

            $response = $client->query($query)->read();

            Log::info('DHCP lease removed successfully', [
                'config' => $config->nagari.'-'.$config->location,
                'lease_id' => $leaseId,
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to remove DHCP lease', [
                'config' => $config->nagari.'-'.$config->location,
                'lease_id' => $leaseId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function dhcpLeaseWritableParams(array $data): array
    {
        $params = [
            'address' => $data['address'] ?? null,
            'server' => $data['server'] ?? null,
            'client-id' => $data['client_id'] ?? $data['client-id'] ?? null,
        ];

        $params = array_filter($params, fn ($value) => filled($value));

        if (array_key_exists('comment', $data)) {
            $params['comment'] = (string) ($data['comment'] ?? '');
        }

        if (array_key_exists('disabled', $data)) {
            $params['disabled'] = $data['disabled'] ? 'true' : 'false';
        }

        if (array_key_exists('blocked', $data)) {
            $params['block-access'] = $data['blocked'] ? 'yes' : 'no';
        }

        return $params;
    }

    private function normalizeMacAddress(string $macAddress): string
    {
        $macAddress = strtoupper(str_replace('-', ':', trim($macAddress)));
        $hexOnly = preg_replace('/[^0-9A-F]/', '', $macAddress);

        if (strlen($hexOnly) === 12) {
            return implode(':', str_split($hexOnly, 2));
        }

        return $macAddress;
    }

    /**
     * Check connection to MikroTik
     */
    public function testConnection(MikrotikConfig $config): bool
    {
        try {
            $client = $this->getClientWithConfig($config);
            $query = new Query('/system/identity/print');
            $client->query($query)->read();

            return true;
        } catch (Exception $e) {
            Log::error('MikroTik connection test failed', [
                'config' => $config->nagari.'-'.$config->location,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
