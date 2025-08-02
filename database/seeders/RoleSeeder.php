<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            'name' => 'WaliNagari',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'Seketaris',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'Kaur Keuangan',
            'guard_name' => 'web',
        ]);

        DB::table('roles')->insert([
            'name' => 'Kaur Umum dan Perencanan',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'Kasi Kesejahteraan',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'Kasi Pemerintahan',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'Kasi Pelayanan',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'Wali Korong',
            'guard_name' =>  'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'Staf Pelayanan',
            'guard_name' => 'web',
        ]);
        DB::table('roles')->insert([
            'name' => 'HPL',
            'guard_name' => 'web',
        ]);
    }
}
