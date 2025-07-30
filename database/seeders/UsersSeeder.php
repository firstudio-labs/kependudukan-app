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
        // Super Admin
        DB::table('users')->insert([
            'nik' => 'superadmin',
            'nama' => 'Super Administrator',
            'username' => 'superadmin',
            'password' => Hash::make('password'),
            'no_hp' => '081234567890',
            'role' => 'superadmin',
            'status' => 'active',
            'image' => null,
            'foto_pengguna' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Admin Desa
        DB::table('users')->insert([
            'nik' => 'admindesa',
            'nama' => 'Admin Desa',
            'username' => 'admindesa',
            'password' => Hash::make('password'),
            'no_hp' => '081234567891',
            'role' => 'admin desa',
            'status' => 'active',
            'image' => null,
            'foto_pengguna' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Admin Kabupaten
        DB::table('users')->insert([
            'nik' => 'adminkabupaten',
            'nama' => 'Admin Kabupaten',
            'username' => 'adminkabupaten',
            'password' => Hash::make('password'),
            'no_hp' => '081234567892',
            'role' => 'admin kabupaten',
            'status' => 'active',
            'image' => null,
            'foto_pengguna' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Operator
        DB::table('users')->insert([
            'nik' => 'operator',
            'nama' => 'Operator',
            'username' => 'operator',
            'password' => Hash::make('password'),
            'no_hp' => '081234567893',
            'role' => 'operator',
            'status' => 'active',
            'image' => null,
            'foto_pengguna' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Guest
        DB::table('users')->insert([
            'nik' => 'guest',
            'nama' => 'Guest User',
            'username' => 'guest',
            'password' => Hash::make('password'),
            'no_hp' => '081234567894',
            'role' => 'guest',
            'status' => 'active',
            'image' => null,
            'foto_pengguna' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
