<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'agif',
            'slug' => 'agif',
            'nagari_id' => 1,
            'emp_id' => 1,
            'Jabatan_id' => 1,
            'username' => 'agif',
            'email' => 'agif@test.com',
            'no_hp' => 6281282779593,
            'no_ktp' => 2171111705939001,
            'no_bpjs' => 2171111705939001,
            'alamat' => 'basung',
            'password' => '123456',
            'password_recovery' => '123456',
            'aktif' => true
        ]);

        User::create([
            'name' => 'Asrul Khairi, A.Md',
            'slug' => 'Asrul',
            'Jabatan_id' => 2,
            'image' => 'web_wali_nagari.jpg',
            'emp_id' => 2,
            'nagari_id' => 1,
            'username' => 'asrul',
            'email' => 'asrul@test.com',
            'no_hp' => 6281282771111,
            'no_ktp' => 2171111705931111,
            'no_bpjs' => 2171111705931111,
            'password' => '123456',
            'password_recovery' => '123456',
            'aktif' => true

        ]);
        User::create([
            'name' => 'Fadil Aulia Rahman, M.Kom',
            'slug' => 'Fadil',
            'Jabatan_id' => 3,
            'image' => 'web_fadil.JPG',
            'emp_id' => 3,
            'nagari_id' => 1,
            'username' => 'fadil',
            'no_hp' => 6285263339495,
            'no_ktp' => 2173333705933333,
            'no_bpjs' => 2173333705933333,
            'email' => 'fadil@test.com',
            'password' => '123456',
            'password_recovery' => '123456',
            'aktif' => true

        ]);
        User::create([
            'name' => 'Beta Pandu Yulita, M.H',
            'slug' => 'Andu',
            'Jabatan_id' => 4,
            'image' => 'web_andu.JPG',
            'emp_id' => 7,
            'nagari_id' => 1,
            'username' => 'beta',
            'email' => 'beta@test.com',
            'password' => '123456',
            'password_recovery' => '123456',
            'no_hp' => 6282244068642,
            'no_ktp' => 217222227059322222,
            'no_bpjs' => 217222227059322222,
            'aktif' => true
        ]);


        User::create([
            'name' => 'Yulia Frima Siska',
            'slug' => 'Siska',
            'Jabatan_id' => 5,
            'image' => 'web_siska.JPG',
            'emp_id' => 6,
            'nagari_id' => 1,
            'username' => 'siska',
            'no_hp' => 6282387107589,
            'no_ktp' => 2174444705934444,
            'no_bpjs' => 2174444705934444,
            'email' => 'siska@test.com',
            'password' => '123456',
            'password_recovery' => '123456',
            'aktif' => true

        ]);
        User::create([
            'name' => 'Febria Melita, S.Pd',
            'slug' => 'Melita',
            'Jabatan_id' => 6,
            'image' => 'web_melita.JPG',
            'emp_id' => 5,
            'nagari_id' => 1,
            'username' => 'melita',
            'no_hp' => 6285278989833,
            'no_ktp' => 2175555705935555,
            'no_bpjs' => 2175555705935555,
            'email' => 'melita@test.com',
            'password' => '123456',
            'password_recovery' => '123456',
            'aktif' => true

        ]);


        User::create([
            'name' => 'Khaira Maulida, S.H',
            'slug' => 'Era',
            'username' => 'era',
            'image' => 'web_era.JPG',
            'emp_id' => 4,
            'Jabatan_id' => 7,
            'nagari_id' => 1,
            'no_hp' => 6285376462498,
            'no_ktp' => 2100007705930000,
            'no_bpjs' => 2100007705930000,
            'email' => 'era@test.com',
            'password' => '123456',
            'password_recovery' => '123456',
            'aktif' => true

        ]);
        User::create([
            'name' => 'Khairil Anwar, S.Kom',
            'slug' => 'Khairil',
            'username' => 'khairil',
            'image' => 'web_khairil.JPG',
            'emp_id' => 11,
            'Jabatan_id' => 8,
            'nagari_id' => 1,
            'no_hp' => 6282174120443,
            'no_ktp' => 2199997705939999,
            'no_bpjs' => 2199997705939999,
            'email' => 'khairil@test.com',
            'password' => '123456',
            'password_recovery' => '123456',
            'aktif' => true

        ]);

        User::create([
            'name' => 'Irvandi',
            'slug' => 'Irvandi',
            'Jabatan_id' => 10,
            'image' => 'web_ipan.JPG',
            'emp_id' => 10,
            'nagari_id' => 1,
            'username' => 'irfan',
            'email' => 'irfan@test.com',
            'password' => '123456',
            'no_hp' => 6283852913299,
            'no_ktp' => 2176666705936666,
            'no_bpjs' => 2176666705936666,
            'password_recovery' => '123456',
            'aktif' => true

        ]);
        User::create([
            'name' => 'Ahmad Yusnadi, S.Sos',
            'slug' => 'Ahmad',
            'username' => 'ahmad',
            'image' => 'web_ahmad.JPG',
            'emp_id' => 8,
            'Jabatan_id' => 10,
            'nagari_id' => 1,
            'no_hp' => 6281276918816,
            'no_ktp' => 2177777705937777,
            'no_bpjs' => 2177777705937777,
            'email' => 'ahmad@test.com',
            'password' => '123456',
            'password_recovery' => '123456',
            'aktif' => true

        ]);
        User::create([
            'name' => 'Silvia Oktavina',
            'slug' => 'Silvia',
            'username' => 'silvia',
            'image' => 'web_sisil.JPG',
            'emp_id' => 9,
            'Jabatan_id' => 10,
            'no_hp' => 6282285282030,
            'nagari_id' => 1,
            'no_ktp' => 2188887705938888,
            'no_bpjs' => 2188887705938888,
            'email' => 'silvia@test.com',
            'password' => '123456',
            'password_recovery' => '123456',
            'aktif' => true

        ]);
        User::create([
            'name' => 'yulianis',
            'slug' => 'Yun',
            'username' => 'yulianis',
            'image' => 'web_niyun.JPG',
            'emp_id' => 12,
            'Jabatan_id' => 12,
            'nagari_id' => 1,
            'no_hp' => 6281266770054,
            'no_ktp' => 2100007705931328,
            'no_bpjs' => 210000770593154,
            'email' => 'yulianis@test.com',
            'password' => '123456',
            'password_recovery' => '123456',
            'aktif' => true

        ]);
    }
}
