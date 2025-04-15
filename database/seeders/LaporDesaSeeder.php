<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporDesa;

class LaporDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $laporDesa = [
            [
                'ruang_lingkup' => 'Pemdes',
                'bidang' => 'Infrastruktur',
                'keterangan' => 'Perbaikan jalan desa',
            ],
            [
                'ruang_lingkup' => 'BPD',
                'bidang' => 'Pembangunan',
                'keterangan' => 'Usulan pembangunan balai desa',
            ],
        ];

        foreach ($laporDesa as $data) {
            LaporDesa::create($data);
        }
    }
}