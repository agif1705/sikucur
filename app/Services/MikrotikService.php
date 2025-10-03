<?php

namespace App\Services;

use Exception;
use RouterOS\Query;
use RouterOS\Client;
use App\Models\MikrotikConfig;
use Illuminate\Support\Facades\Log;

class MikrotikService
{
 private ?Client $client = null;
 private ?MikrotikConfig $config = null;

 /**
  * Set dynamic config based on nagari and location
  */
 public function setConfig(string $nagari, string $location): self
 {
  $this->config = MikrotikConfig::getConfig($nagari, $location);

  if (!$this->config) {
   throw new Exception("MikroTik config not found for nagari: {$nagari}, location: {$location}");
  }

  // Reset client to force reconnection with new config
  $this->client = null;

  return $this;
 }

 /**
  * Get MikroTik client instance
  */
 public function getClient(): Client
 {
  if (!$this->client) {
   // Use dynamic config if available, otherwise fallback to default config
   $this->client = new Client([
    'host' => $this->config->host,
    'user' => $this->config->user,
    'pass' => $this->config->pass,
    'port' => $this->config->port,
    'ssl' => $this->config->ssl,
   ]);
  }

  return $this->client;
 }

 /**
  * Add user to MikroTik hotspot
  */
 public function addHotspotUser(string $username, string $password, array $additionalParams = []): array
 {
  try {
   $query = (new Query('/ip/hotspot/user/add'))
    ->equal('name', $username)
    ->equal('password', $password);

   // Add additional parameters if provided
   foreach ($additionalParams as $key => $value) {
    $query->equal($key, $value);
   }

   $response = $this->getClient()->query($query)->read();

   Log::info('MikroTik user added successfully', [
    'username' => $username,
    'response' => $response
   ]);

   return $response;
  } catch (Exception $e) {
   Log::error('Failed to add MikroTik user', [
    'username' => $username,
    'error' => $e->getMessage()
   ]);
   throw $e;
  }
 }

 /**
  * Remove user from MikroTik hotspot
  */
 public function removeHotspotUser(string $username): array
 {
  try {
   // First find the user ID
   $findQuery = (new Query('/ip/hotspot/user/print'))
    ->where('name', $username);

   $users = $this->getClient()->query($findQuery)->read();

   if (empty($users)) {
    throw new Exception("User '{$username}' not found in MikroTik");
   }

   $userId = $users[0]['.id'];

   // Remove the user
   $removeQuery = (new Query('/ip/hotspot/user/remove'))
    ->equal('.id', $userId);

   $response = $this->getClient()->query($removeQuery)->read();

   Log::info('MikroTik user removed successfully', [
    'username' => $username,
    'user_id' => $userId
   ]);

   return $response;
  } catch (Exception $e) {
   Log::error('Failed to remove MikroTik user', [
    'username' => $username,
    'error' => $e->getMessage()
   ]);
   throw $e;
  }
 }

 /**
  * Get user details from MikroTik
  */
 public function getHotspotUser(string $username): ?array
 {
  try {
   $query = (new Query('/ip/hotspot/user/print'))
    ->where('name', $username);

   $users = $this->getClient()->query($query)->read();

   return !empty($users) ? $users[0] : null;
  } catch (Exception $e) {
   Log::error('Failed to get MikroTik user', [
    'username' => $username,
    'error' => $e->getMessage()
   ]);
   throw $e;
  }
 }

 /**
  * Disable user in MikroTik hotspot
  */
 public function disableHotspotUser(string $username): array
 {
  try {
   // First find the user ID
   $findQuery = (new Query('/ip/hotspot/user/print'))
    ->where('name', $username);

   $users = $this->getClient()->query($findQuery)->read();

   if (empty($users)) {
    throw new Exception("User '{$username}' not found in MikroTik");
   }

   $userId = $users[0]['.id'];

   // Disable the user
   $updateQuery = (new Query('/ip/hotspot/user/set'))
    ->equal('.id', $userId)
    ->equal('disabled', 'true');

   $response = $this->getClient()->query($updateQuery)->read();

   // Remove active sessions for this user
   $this->removeActiveSession($username);

   Log::info('MikroTik user disabled successfully', [
    'username' => $username,
    'user_id' => $userId
   ]);

   return $response;
  } catch (Exception $e) {
   Log::error('Failed to disable MikroTik user', [
    'username' => $username,
    'error' => $e->getMessage()
   ]);
   throw $e;
  }
 }


 /**
  * Enable user in MikroTik hotspot
  */
 public function enableHotspotUser(string $username): array
 {
  try {
   // First find the user ID
   $findQuery = (new Query('/ip/hotspot/user/print'))
    ->where('name', $username);

   $users = $this->getClient()->query($findQuery)->read();

   if (empty($users)) {
    throw new Exception("User '{$username}' not found in MikroTik");
   }

   $userId = $users[0]['.id'];

   // Enable the user
   $updateQuery = (new Query('/ip/hotspot/user/set'))
    ->equal('.id', $userId)
    ->equal('disabled', 'false');

   $response = $this->getClient()->query($updateQuery)->read();

   Log::info('MikroTik user enabled successfully', [
    'username' => $username,
    'user_id' => $userId
   ]);

   return $response;
  } catch (Exception $e) {
   Log::error('Failed to enable MikroTik user', [
    'username' => $username,
    'error' => $e->getMessage()
   ]);
   throw $e;
  }
 }

 /**
  * Update user password in MikroTik
  */
 public function updateHotspotUserPassword(string $username, string $newPassword): array
 {
  try {
   // First find the user ID
   $findQuery = (new Query('/ip/hotspot/user/print'))
    ->where('name', $username);

   $users = $this->getClient()->query($findQuery)->read();

   if (empty($users)) {
    throw new Exception("User '{$username}' not found in MikroTik");
   }

   $userId = $users[0]['.id'];

   // Update the password
   $updateQuery = (new Query('/ip/hotspot/user/set'))
    ->equal('.id', $userId)
    ->equal('password', $newPassword);

   $response = $this->getClient()->query($updateQuery)->read();

   Log::info('MikroTik user password updated successfully', [
    'username' => $username,
    'user_id' => $userId
   ]);

   return $response;
  } catch (Exception $e) {
   Log::error('Failed to update MikroTik user password', [
    'username' => $username,
    'error' => $e->getMessage()
   ]);
   throw $e;
  }
 }

 /**
  * Enable/disable user in MikroTik
  */
 public function toggleHotspotUser(string $username, bool $enabled = true): array
 {
  try {
   // First find the user ID
   $findQuery = (new Query('/ip/hotspot/user/print'))
    ->where('name', $username);

   $users = $this->getClient()->query($findQuery)->read();

   if (empty($users)) {
    throw new Exception("User '{$username}' not found in MikroTik");
   }

   $userId = $users[0]['.id'];

   // Enable/disable the user
   $updateQuery = (new Query('/ip/hotspot/user/set'))
    ->equal('.id', $userId)
    ->equal('disabled', $enabled ? 'false' : 'true');

   $response = $this->getClient()->query($updateQuery)->read();

   // If disabling user, remove active sessions
   if (!$enabled) {
    $this->removeActiveSession($username);
   }

   Log::info('MikroTik user status updated successfully', [
    'username' => $username,
    'user_id' => $userId,
    'enabled' => $enabled
   ]);

   return $response;
  } catch (Exception $e) {
   Log::error('Failed to toggle MikroTik user status', [
    'username' => $username,
    'enabled' => $enabled,
    'error' => $e->getMessage()
   ]);
   throw $e;
  }
 }
 private function removeActiveSession(string $username): void
 {
  try {
   // Find active sessions for this user
   $findActiveQuery = (new Query('/ip/hotspot/active/print'))
    ->where('user', $username);

   $activeSessions = $this->getClient()->query($findActiveQuery)->read();

   if (!empty($activeSessions)) {
    foreach ($activeSessions as $session) {
     $sessionId = $session['.id'];

     // Remove the active session
     $removeQuery = (new Query('/ip/hotspot/active/remove'))
      ->equal('.id', $sessionId);

     $this->getClient()->query($removeQuery)->read();

     Log::info('Active session removed for user', [
      'username' => $username,
      'session_id' => $sessionId
     ]);
    }
   }
  } catch (Exception $e) {
   Log::warning('Failed to remove active session for user', [
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
 public function getAllHotspotUsers(): array
 {
  try {
   $query = new Query('/ip/hotspot/user/print');

   $users = $this->getClient()->query($query)->read();

   return $users;
  } catch (Exception $e) {
   Log::error('Failed to get all MikroTik users', [
    'error' => $e->getMessage()
   ]);
   throw $e;
  }
 }

 /**
  * Get active sessions
  */
 public function getActiveSessions(): array
 {
  try {
   $query = new Query('/ip/hotspot/active/print');

   $sessions = $this->getClient()->query($query)->read();

   return $sessions;
  } catch (Exception $e) {
   Log::error('Failed to get active sessions', [
    'error' => $e->getMessage()
   ]);
   throw $e;
  }
 }

 /**
  * Check connection to MikroTik
  */
 public function testConnection(): bool
 {
  try {
   $query = new Query('/system/identity/print');
   $this->getClient()->query($query)->read();

   return true;
  } catch (Exception $e) {
   Log::error('MikroTik connection test failed', [
    'error' => $e->getMessage()
   ]);
   return false;
  }
 }
}