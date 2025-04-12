<?php

namespace Database\Seeders;

use App\Models\Klasifikasi;
use Illuminate\Database\Seeder;

class KlasifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $klasifikasi = [
            [
                'kode' => 100,
                'jenis_klasifikasi' => 'Tanah',
                'keterangan' => 'Aset berupa tanah kosong atau lahan'
            ],
            [
                'kode' => 200,
                'jenis_klasifikasi' => 'Gedung',
                'keterangan' => 'Aset berupa bangunan gedung'
            ],
            [
                'kode' => 300,
                'jenis_klasifikasi' => 'Peralatan Kantor',
                'keterangan' => 'Aset berupa peralatan dan perlengkapan kantor'
            ],
            [
                'kode' => 400,
                'jenis_klasifikasi' => 'Kendaraan',
                'keterangan' => 'Aset berupa kendaraan dinas atau operasional'
            ],
            [
                'kode' => 500,
                'jenis_klasifikasi' => 'Elektronik',
                'keterangan' => 'Aset berupa perangkat elektronik'
            ],
            [
                'kode' => 600,
                'jenis_klasifikasi' => 'Furnitur',
                'keterangan' => 'Aset berupa furnitur dan perabotan'
            ],
            [
                'kode' => 700,
                'jenis_klasifikasi' => 'Infrastruktur',
                'keterangan' => 'Aset berupa infrastruktur desa'
            ],
            [
                'kode' => 800,
                'jenis_klasifikasi' => 'Inventaris',
                'keterangan' => 'Aset berupa barang inventaris lainnya'
            ],
            [
                'kode' => 900,
                'jenis_klasifikasi' => 'Aset Digital',
                'keterangan' => 'Aset berupa software, lisensi, dan produk digital'
            ],
            [
                'kode' => 1000,
                'jenis_klasifikasi' => 'Fasilitas Umum',
                'keterangan' => 'Aset berupa fasilitas untuk kepentingan umum'
            ],
        ];

        foreach ($klasifikasi as $item) {
            Klasifikasi::create($item);
        }
    }
}