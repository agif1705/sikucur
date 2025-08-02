<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ModelHasRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('model_has_roles')->insert([
            'role_id' => 1,
            'model_type' => 'App\Models\User',
            'model_id' => 1
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => 2,
            'model_type' => 'App\Models\User',
            'model_id' => 2
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => 3,
            'model_type' => 'App\Models\User',
            'model_id' => 3
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => 4,
            'model_type' => 'App\Models\User',
            'model_id' => 4
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => 5,
            'model_type' => 'App\Models\User',
            'model_id' => 5
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => 6,
            'model_type' => 'App\Models\User',
            'model_id' => 6
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => 7,
            'model_type' => 'App\Models\User',
            'model_id' => 7
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => 8,
            'model_type' => 'App\Models\User',
            'model_id' => 8
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => 10,
            'model_type' => 'App\Models\User',
            'model_id' => 9
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => 10,
            'model_type' => 'App\Models\User',
            'model_id' => 10
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => 10,
            'model_type' => 'App\Models\User',
            'model_id' => 11
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => 11,
            'model_type' => 'App\Models\User',
            'model_id' => 12
        ]);
    }
}
