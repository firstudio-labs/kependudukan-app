<?php

namespace Database\Seeders;

use App\Models\JenisAset;
use Illuminate\Database\Seeder;

class JenisAsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jenisAset = [
            [
                'kode' => 101,
                'jenis_aset' => 'Tanah',
                'keterangan' => 'Aset berupa tanah yang dimiliki oleh desa',
            ],
            [
                'kode' => 102,
                'jenis_aset' => 'Bangunan',
                'keterangan' => 'Aset berupa bangunan milik desa seperti kantor desa, balai desa, dll',
            ],
            [
                'kode' => 103,
                'jenis_aset' => 'Kendaraan',
                'keterangan' => 'Aset berupa kendaraan operasional desa',
            ],
            [
                'kode' => 104,
                'jenis_aset' => 'Peralatan Kantor',
                'keterangan' => 'Peralatan elektronik dan non-elektronik untuk operasional kantor desa',
            ],
            [
                'kode' => 105,
                'jenis_aset' => 'Peralatan Pertanian',
                'keterangan' => 'Alat-alat pertanian milik desa untuk dimanfaatkan warga',
            ],
            [
                'kode' => 106,
                'jenis_aset' => 'Infrastruktur',
                'keterangan' => 'Aset berupa jalan, jembatan, irigasi, dan infrastruktur lainnya',
            ],
            [
                'kode' => 107,
                'jenis_aset' => 'Fasilitas Kesehatan',
                'keterangan' => 'Aset untuk pelayanan kesehatan seperti puskesmas pembantu dan posyandu',
            ],
            [
                'kode' => 108,
                'jenis_aset' => 'Fasilitas Pendidikan',
                'keterangan' => 'Aset untuk pelayanan pendidikan seperti gedung PAUD dan perpustakaan desa',
            ],
            [
                'kode' => 109,
                'jenis_aset' => 'Peralatan Kesenian',
                'keterangan' => 'Peralatan untuk kegiatan seni dan budaya desa',
            ],
            [
                'kode' => 110,
                'jenis_aset' => 'Inventaris Lainnya',
                'keterangan' => 'Aset desa lainnya yang tidak termasuk dalam kategori di atas',
            ],
        ];

        foreach ($jenisAset as $aset) {
            JenisAset::create($aset);
        }
    }
}