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
    'name' => 'Wilayah Sikucur',
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
    'name' => 'Pasar Basung',
    'nagari' => 'sikucur',
    'location' => 'pasar Basung',
    'host' => 'id-37.hostddns.us',
    'user' => 'admin',
    'pass' => 'agif1705@gmail.com',
    'port' => 31084,
    'ssl' => false,
    'is_active' => true,
   ],
   [
    'name' => 'Nagari Lain',
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
