<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->roles() as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }
    }

    private function roles(): array
    {
        return [
            'super_admin',
            'WaliNagari',
            'Seketaris',
            'Kaur Keuangan',
            'Kaur Umum dan Perencanan',
            'Kasi Kesejahteraan',
            'Kasi Pemerintahan',
            'Kasi Pelayanan',
            'Wali Korong',
            'Staf Pelayanan',
            'HPL',
        ];
    }
}
