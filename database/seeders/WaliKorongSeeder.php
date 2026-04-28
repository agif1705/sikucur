<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\User;
use App\Models\WaliKorong;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class WaliKorongSeeder extends Seeder
{
 public function run(): void
 {
  $nagariId = 1;
  $jabatanId = Jabatan::where('name', 'Wali Korong')->value('id') ?? 9;

  Role::firstOrCreate([
   'name' => 'Wali Korong',
   'guard_name' => 'web',
  ]);

  foreach ($this->waliKorongs() as $item) {
   $user = User::query()
    ->where('username', $item['username'])
    ->orWhere('no_hp', (string) $item['no_hp'])
    ->orWhere('email', $item['email'])
    ->orWhere('no_ktp', (string) $item['no_ktp'])
    ->first();

   if (! $user) {
    $user = new User;
    $user->password = Hash::make('123456');
   }

   $user->fill([
    'name' => $item['name'],
    'username' => $item['username'],
    'slug' => Str::slug($item['name'] . '-' . $item['wilayah']),
    'image' => null,
    'emp_id' => $item['emp_id'],
    'jabatan_id' => $jabatanId,
    'nagari_id' => $nagariId,
    'no_hp' => (string) $item['no_hp'],
    'no_ktp' => (string) $item['no_ktp'],
    'no_bpjs' => (string) $item['no_bpjs'],
    'email' => $item['email'],
    'password_recovery' => '123456',
    'aktif' => true,
   ]);

   $user->save();

   $user->syncRoles(['Wali Korong']);

   WaliKorong::updateOrCreate(
    [
     'nagari_id' => $nagariId,
     'wilayah' => $item['wilayah'],
    ],
    [
     'name' => $item['name'],
     'user_id' => $user->id,
    ]
   );
  }
 }

 private function waliKorongs(): array
 {
  return [
   [
    'name' => 'Boy Candra',
    'username' => 'boy',
    'wilayah' => 'Bunga Tanjung',
    'emp_id' => 13,
    'no_hp' => 6281100000001,
    'no_ktp' => 2100000000000001,
    'no_bpjs' => 2100000000000001,
    'email' => 'boy@sikucur.com',
   ],
   [
    'name' => 'Almaidi Saputra, S.E',
    'username' => 'putra',
    'wilayah' => 'Durian Kadok',
    'emp_id' => 14,
    'no_hp' => 6281100000002,
    'no_ktp' => 2100000000000002,
    'no_bpjs' => 2100000000000002,
    'email' => 'putra@sikucur.com',
   ],
   [
    'name' => 'Zulpikal',
    'username' => 'zulpikal',
    'wilayah' => 'Sungai Janiah',
    'emp_id' => 15,
    'no_hp' => 6281100000003,
    'no_ktp' => 2100000000000003,
    'no_bpjs' => 2100000000000003,
    'email' => 'zulpikal@sikucur.com',
   ],
   [
    'name' => 'Devit Yudia Putra, S.Pt',
    'username' => 'david',
    'wilayah' => 'Lansano',
    'emp_id' => 16,
    'no_hp' => 6281100000004,
    'no_ktp' => 2100000000000004,
    'no_bpjs' => 2100000000000004,
    'email' => 'david@sikucur.com',
   ],
  ];
 }
}
