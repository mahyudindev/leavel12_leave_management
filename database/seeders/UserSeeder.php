<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        // HRD
        DB::table('users')->insert([
            'nik' => '1234567890',
            'name' => 'HRD Manager',
            'email' => 'hrd@example.com',
            // 'email_verified_at' => Carbon::now(),
            'password' => Hash::make('123'),
            'tanggal_masuk_kerja' => '2023-01-01',
            'departemen_id' => null,
            'jabatan_id' => null,
            'jumlah_cuti' => '12',
            'role' => 'hrd',
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Manager (IT)
        DB::table('users')->insert([
            'nik' => '9876543210',
            'name' => 'IT Manager',
            'email' => 'manager@example.com',
            // 'email_verified_at' => Carbon::now(),
            'password' => Hash::make('123'),
            'tanggal_masuk_kerja' => '2023-01-01',
            'departemen_id' => null,
            'jabatan_id' => null,
            'jumlah_cuti' => '10',
            'role' => 'manager',
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Pegawai (IT)
        DB::table('users')->insert([
            'nik' => '1122334455',
            'name' => 'Andre Mahadie',
            'email' => 'pegawai@example.com',
            // 'email_verified_at' => Carbon::now(),
            'password' => Hash::make('123'),
            'tanggal_masuk_kerja' => '2023-01-01',
            'departemen_id' => null,
            'jabatan_id' => null,
            'jumlah_cuti' => '10',
            'role' => 'pegawai',
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('users')->insert([
            'nik' => '1234432111',
            'name' => 'Mahyudin',
            'email' => 'mahyudin@gmail.com',
            // 'email_verified_at' => Carbon::now(),
            'password' => Hash::make('123'),
            'tanggal_masuk_kerja' => '2023-01-01',
            'departemen_id' => null,
            'jabatan_id' => null,
            'jumlah_cuti' => '10',
            'role' => 'pegawai',
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        
    }
}
