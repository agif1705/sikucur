<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_absensi::pegawai","view_any_absensi::pegawai","create_absensi::pegawai","update_absensi::pegawai","restore_absensi::pegawai","restore_any_absensi::pegawai","replicate_absensi::pegawai","delete_absensi::pegawai","delete_any_absensi::pegawai","force_delete_absensi::pegawai","force_delete_any_absensi::pegawai","view_nagari","view_any_nagari","create_nagari","update_nagari","restore_nagari","restore_any_nagari","replicate_nagari","delete_nagari","delete_any_nagari","force_delete_nagari","force_delete_any_nagari","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_tv::galeri","view_any_tv::galeri","create_tv::galeri","update_tv::galeri","restore_tv::galeri","restore_any_tv::galeri","replicate_tv::galeri","delete_tv::galeri","delete_any_tv::galeri","force_delete_tv::galeri","force_delete_any_tv::galeri","view_tv::informasi","view_any_tv::informasi","create_tv::informasi","update_tv::informasi","restore_tv::informasi","restore_any_tv::informasi","replicate_tv::informasi","delete_tv::informasi","delete_any_tv::informasi","force_delete_tv::informasi","force_delete_any_tv::informasi","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","page_AbsensiDinasLuarDaerah","page_AttendaceUser","page_ComentsPage","widget_AbsensiHariLibur"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
