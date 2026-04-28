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
            $user = User::updateOrCreate(
                ['username' => $item['username']],
                [
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name'].'-'.$item['wilayah']),
                    'image' => null,
                    'emp_id' => $item['emp_id'],
                    'jabatan_id' => $jabatanId,
                    'nagari_id' => $nagariId,
                    'no_hp' => $item['no_hp'],
                    'no_ktp' => $item['no_ktp'],
                    'no_bpjs' => $item['no_bpjs'],
                    'email' => $item['email'],
                    'password' => Hash::make('123456'),
                    'password_recovery' => '123456',
                    'aktif' => true,
                ]
            );

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
                'name' => 'David',
                'username' => 'david.bunga',
                'wilayah' => 'Bunga Tanjung',
                'emp_id' => 13,
                'no_hp' => 6281100000001,
                'no_ktp' => 2100000000000001,
                'no_bpjs' => 2100000000000001,
                'email' => 'david.bunga@test.com',
            ],
            [
                'name' => 'Putra',
                'username' => 'putra.durian',
                'wilayah' => 'Durian Kadok',
                'emp_id' => 14,
                'no_hp' => 6281100000002,
                'no_ktp' => 2100000000000002,
                'no_bpjs' => 2100000000000002,
                'email' => 'putra.durian@test.com',
            ],
            [
                'name' => 'Pikal',
                'username' => 'pikal.sungai',
                'wilayah' => 'Sungai Janiah',
                'emp_id' => 15,
                'no_hp' => 6281100000003,
                'no_ktp' => 2100000000000003,
                'no_bpjs' => 2100000000000003,
                'email' => 'pikal.sungai@test.com',
            ],
            [
                'name' => 'David',
                'username' => 'david.lansano',
                'wilayah' => 'Lansano',
                'emp_id' => 16,
                'no_hp' => 6281100000004,
                'no_ktp' => 2100000000000004,
                'no_bpjs' => 2100000000000004,
                'email' => 'david.lansano@test.com',
            ],
        ];
    }
}
