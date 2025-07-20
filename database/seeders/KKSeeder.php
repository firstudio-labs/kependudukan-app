<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KK;

class KKSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'kk' => '1234567890123456',
                'full_name' => 'Agus Setiawan',
                'address' => 'Jl. Merdeka No. 123',
                'postal_code' => '12345',
                'rt' => '001',
                'rw' => '002',
                'jml_anggota_kk' => 4,
                'province_id' => '32',
                'district_id' => '3201',
                'sub_district_id' => '320101',
                'village_id' => '32010101',
            ],
            [
                'kk' => '1234567890123457',
                'full_name' => 'Siti Nurhaliza',
                'address' => 'Jl. Sudirman No. 456',
                'postal_code' => '12346',
                'rt' => '002',
                'rw' => '003',
                'jml_anggota_kk' => 3,
                'province_id' => '32',
                'district_id' => '3201',
                'sub_district_id' => '320101',
                'village_id' => '32010101',
            ],
            [
                'kk' => '1234567890123458',
                'full_name' => 'Budi Santoso',
                'address' => 'Jl. Thamrin No. 789',
                'postal_code' => '12347',
                'rt' => '003',
                'rw' => '004',
                'jml_anggota_kk' => 5,
                'province_id' => '32',
                'district_id' => '3201',
                'sub_district_id' => '320101',
                'village_id' => '32010101',
            ],
        ];

        foreach ($data as $item) {
            KK::create($item);
        }
    }
} 