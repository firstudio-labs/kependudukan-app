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
        // Super Admin (lengkap)
        DB::table('users')->insert([
            'nik' => 'superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'no_hp' => '081234567890',
            'alamat' => 'Jalan Utama No. 1, Pusat Kota',
            'province_id' => 11,
            'districts_id' => 1101,
            'sub_districts_id' => 110101,
            'villages_id' => 1101012001,
            'role' => 'superadmin',
            'status' => 'active',
            'image' => null,
            'foto_pengguna' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Admin Desa (lengkap)
        DB::table('users')->insert([
            'nik' => '3201999900000001',
            'username' => 'admindesa',
            'email' => 'admindesa@example.com',
            'password' => Hash::make('password'),
            'no_hp' => '081234567891',
            'alamat' => 'Jl. Desa Bahagia No. 10',
            'province_id' => 11,
            'districts_id' => 1101,
            'sub_districts_id' => 110101,
            'villages_id' => 1101012001, // contoh kode desa
            'role' => 'admin desa',
            'status' => 'active',
            'image' => null,
            'foto_pengguna' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Admin Kabupaten (lengkap)
        DB::table('users')->insert([
            'nik' => '3201888800000001',
            'username' => 'adminkabupaten',
            'email' => 'adminkabupaten@example.com',
            'password' => Hash::make('password'),
            'no_hp' => '081234567892',
            'alamat' => 'Kantor Kabupaten, Lantai 2',
            'province_id' => 11,
            'districts_id' => 1101,
            'sub_districts_id' => 110101,
            'villages_id' => 1101012001,
            'role' => 'admin kabupaten',
            'status' => 'active',
            'image' => null,
            'foto_pengguna' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Operator (lengkap)
        DB::table('users')->insert([
            'nik' => '3201777700000001',
            'username' => 'operator',
            'email' => 'operator@example.com',
            'password' => Hash::make('password'),
            'no_hp' => '081234567893',
            'alamat' => 'Kantor Desa, Ruang Operator',
            'province_id' => 11,
            'districts_id' => 1101,
            'sub_districts_id' => 110101,
            'villages_id' => 1101012001,
            'role' => 'operator',
            'status' => 'active',
            'image' => null,
            'foto_pengguna' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Guest (lengkap)
        DB::table('users')->insert([
            'nik' => 'guest',
            'username' => 'guest',
            'email' => 'guest@example.com',
            'password' => Hash::make('password'),
            'no_hp' => '081234567894',
            'alamat' => 'Tamu Desa',
            'province_id' => 11,
            'districts_id' => 1101,
            'sub_districts_id' => 110101,
            'villages_id' => 1101012001,
            'role' => 'guest',
            'status' => 'active',
            'image' => null,
            'foto_pengguna' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
