<?php

namespace Database\Seeders;

use App\Models\JadwalUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JadwalUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::all()->count();
        for ($i = 0; $i < $user; $i++) {
            JadwalUser::create([
                'user_id' => $i + 1,
                'shift_id' => 1,
                'nagari_id' => 1
            ]);
        }
    }
}
