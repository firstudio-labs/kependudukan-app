<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'nik' => 'superadmin',
            'password' => Hash::make('password'),
            'no_hp' => '081234567890',
            'role' => 'superadmin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Data untuk admin
        DB::table('users')->insert([
            'nik' => 'admin',
            'password' => Hash::make('password'),
            'no_hp' => '081234567891',
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Data untuk operator
        DB::table('users')->insert([
            'nik' => 'operator',
            'password' => Hash::make('password'),
            'no_hp' => '081234567892',
            'role' => 'operator',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

}
