<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@gmail.com',
                'email_verified_at' => Carbon::now(),
                'no_hp' => '081234567890',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Kurir Andi',
                'email' => 'kurir1@gmail.com',
                'email_verified_at' => Carbon::now(),
                'no_hp' => '082345678901',
                'role' => 'kurir',
                'password' => Hash::make('password123'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Kurir Budi',
                'email' => 'kurir2@gmail.com',
                'email_verified_at' => Carbon::now(),
                'no_hp' => '083456789012',
                'role' => 'kurir',
                'password' => Hash::make('password123'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}