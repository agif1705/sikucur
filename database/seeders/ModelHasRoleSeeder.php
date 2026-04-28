<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ModelHasRoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->roleMap() as $username => $roleName) {
            $user = User::where('username', $username)->first();
            if (! $user) {
                continue;
            }

            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $user->syncRoles([$roleName]);
        }
    }

    private function roleMap(): array
    {
        return [
            'agif' => 'super_admin',
            'asrul' => 'WaliNagari',
            'fadil' => 'Seketaris',
            'beta' => 'Kaur Keuangan',
            'siska' => 'Kaur Umum dan Perencanan',
            'melita' => 'Kasi Kesejahteraan',
            'era' => 'Kasi Pemerintahan',
            'khairil' => 'Kasi Pelayanan',
            'irfan' => 'Staf Pelayanan',
            'ahmad' => 'Staf Pelayanan',
            'silvia' => 'Staf Pelayanan',
            'yulianis' => 'HPL',
            'boy.bunga' => 'Wali Korong',
            'putra.durian' => 'Wali Korong',
            'pikal.sungai' => 'Wali Korong',
            'david.lansano' => 'Wali Korong',
        ];
    }
}