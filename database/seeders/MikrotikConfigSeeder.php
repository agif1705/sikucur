<?php

namespace Database\Seeders;

use App\Models\MikrotikConfig;
use Illuminate\Database\Seeder;

class MikrotikConfigSeeder extends Seeder
{
 /**
  * Run the database seeder.
  */
 public function run(): void
 {
  $configs = [
   [
    'nagari' => 'sikucur',
    'location' => 'kantor',
    'host' => 'id-26.hostddns.us',
    'user' => 'agif',
    'pass' => 'agif1705@gmail.com',
    'port' => 30668,
    'ssl' => false,
    'is_active' => true,
   ],
   [
    'nagari' => 'sikucur',
    'location' => 'backup',
    'host' => '192.168.1.2',
    'user' => 'admin',
    'pass' => '',
    'port' => 8728,
    'ssl' => false,
    'is_active' => false,
   ],
   [
    'nagari' => 'other-nagari',
    'location' => 'main',
    'host' => '192.168.2.1',
    'user' => 'admin',
    'pass' => '',
    'port' => 8728,
    'ssl' => false,
    'is_active' => true,
   ],
  ];

  foreach ($configs as $config) {
   MikrotikConfig::create($config);
  }
 }
}