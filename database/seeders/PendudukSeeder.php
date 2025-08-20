<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PendudukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Penduduk 1 (lengkap untuk alur approval)
        DB::table('penduduk')->insertOrIgnore([
            'nik' => '3201234567890001',
            'password' => Hash::make('password'),
            'no_hp' => '081200000001',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Penduduk 2 (tambahan)
        DB::table('penduduk')->insertOrIgnore([
            'nik' => '3201234567890002',
            'password' => Hash::make('password'),
            'no_hp' => '081200000002',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}


