<?php

namespace App\Services;

use DateTime;
use Exception;
use RouterOS\Query;
use RouterOS\Client;
use App\Models\MikrotikConfig;
use Illuminate\Support\Facades\Log;

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
  ]);
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

   if (!empty($existingUsers)) {
    // User already exists, return existing user info
    Log::info('MikroTik user already exists, returning existing user', [
     'config' => $config->nagari . '-' . $config->location,
     'username' => $username,
     'existing_user_id' => $existingUsers[0]['.id']
    ]);

    // Return in same format as add response for consistency
    return [
     'after' => [
      'ret' => $existingUsers[0]['.id']
     ],
     'existing_user' => true,
     'user_data' => $existingUsers[0]
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
    'config' => $config->nagari . '-' . $config->location,
    'username' => $username,
    'response' => $response
   ]);

   return $response;
  } catch (Exception $e) {
   Log::error('Failed to add MikroTik user', [
    'config' => $config->nagari . '-' . $config->location,
    'username' => $username,
    'error' => $e->getMessage()
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

   if (!empty($existingUsers)) {
    return [
     'after' => ['ret' => $existingUsers[0]['.id']],
     'existing_user' => true,
     'user_data' => $existingUsers[0]
    ];
   }

   // Calculate uptime
   $currentTime = new DateTime();
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
    'comment' => 'Voucher created at ' . $currentTime->format('Y-m-d H:i:s'),
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
    'config' => $config->nagari . '-' . $config->location,
    'username' => $username,
    'expiry_date' => $expiryDate->format('Y-m-d H:i:s'),
    'uptime_duration' => $this->formatUptimeForMikrotik($uptimeSeconds),
    'options' => $options
   ]);

   return $response;
  } catch (Exception $e) {
   Log::error('Failed to create MikroTik voucher', [
    'config' => $config->nagari . '-' . $config->location,
    'username' => $username,
    'error' => $e->getMessage()
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
   $parts[] = $days . 'd';
  }
  if ($hours > 0) {
   $parts[] = $hours . 'h';
  }
  if ($minutes > 0) {
   $parts[] = $minutes . 'm';
  }
  if ($remainingSeconds > 0 || empty($parts)) {
   $parts[] = $remainingSeconds . 's';
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
    'config' => $config->nagari . '-' . $config->location,
    'username' => $username,
    'user_id' => $userId
   ]);

   return $response;
  } catch (Exception $e) {
   Log::error('Failed to remove MikroTik user', [
    'config' => $config->nagari . '-' . $config->location,
    'username' => $username,
    'error' => $e->getMessage()
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
    'config' => $config->nagari . '-' . $config->location,
    'username' => $username,
    'user_id' => $userId
   ]);

   return $response;
  } catch (Exception $e) {
   Log::error('Failed to update MikroTik user password', [
    'config' => $config->nagari . '-' . $config->location,
    'username' => $username,
    'error' => $e->getMessage()
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
   if (!$enabled) {
    $this->removeActiveSession($config, $username);
   }

   Log::info('MikroTik user status updated successfully', [
    'config' => $config->nagari . '-' . $config->location,
    'username' => $username,
    'user_id' => $userId,
    'enabled' => $enabled
   ]);

   return $response;
  } catch (Exception $e) {
   Log::error('Failed to toggle MikroTik user status', [
    'config' => $config->nagari . '-' . $config->location,
    'username' => $username,
    'enabled' => $enabled,
    'error' => $e->getMessage()
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

   if (!empty($activeSessions)) {
    foreach ($activeSessions as $session) {
     $sessionId = $session['.id'];

     // Remove the active session
     $removeQuery = (new Query('/ip/hotspot/active/remove'))
      ->equal('.id', $sessionId);

     $client->query($removeQuery)->read();

     Log::info('Active session removed for user', [
      'config' => $config->nagari . '-' . $config->location,
      'username' => $username,
      'session_id' => $sessionId
     ]);
    }
   }
  } catch (Exception $e) {
   Log::warning('Failed to remove active session for user', [
    'config' => $config->nagari . '-' . $config->location,
    'username' => $username,
    'error' => $e->getMessage()
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
    'config' => $config->nagari . '-' . $config->location,
    'error' => $e->getMessage()
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
    'config' => $config->nagari . '-' . $config->location,
    'error' => $e->getMessage()
   ]);
   throw $e;
  }
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
    'config' => $config->nagari . '-' . $config->location,
    'error' => $e->getMessage()
   ]);
   return false;
  }
 }
}
