<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BeritaDesa;
use App\Models\User;

class BeritaDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user admin desa yang sudah ada
        $adminDesa = User::where('role', 'admin desa')->first();
        
        if ($adminDesa) {
            // Buat beberapa berita desa contoh
            BeritaDesa::create([
                'judul' => 'Pembangunan Jalan Desa',
                'deskripsi' => 'Pembangunan jalan desa untuk meningkatkan aksesibilitas warga.',
                'komentar' => 'Proyek ini akan selesai dalam 3 bulan.',
                'user_id' => $adminDesa->id,
                'province_id' => $adminDesa->province_id,
                'districts_id' => $adminDesa->districts_id,
                'sub_districts_id' => $adminDesa->sub_districts_id,
                'villages_id' => $adminDesa->villages_id,
            ]);

            BeritaDesa::create([
                'judul' => 'Pelatihan UMKM Desa',
                'deskripsi' => 'Program pelatihan untuk meningkatkan kemampuan wirausaha warga desa.',
                'komentar' => 'Pelatihan akan dilaksanakan setiap minggu.',
                'user_id' => $adminDesa->id,
                'province_id' => $adminDesa->province_id,
                'districts_id' => $adminDesa->districts_id,
                'sub_districts_id' => $adminDesa->sub_districts_id,
                'villages_id' => $adminDesa->villages_id,
            ]);

            BeritaDesa::create([
                'judul' => 'Festival Budaya Desa',
                'deskripsi' => 'Acara tahunan untuk melestarikan budaya dan tradisi desa.',
                'komentar' => 'Akan dihadiri oleh seluruh warga desa.',
                'user_id' => $adminDesa->id,
                'province_id' => $adminDesa->province_id,
                'districts_id' => $adminDesa->districts_id,
                'sub_districts_id' => $adminDesa->sub_districts_id,
                'villages_id' => $adminDesa->villages_id,
            ]);

            $this->command->info('Berita desa berhasil dibuat dengan struktur wilayah baru!');
        } else {
            $this->command->warn('Tidak ada user admin desa yang ditemukan. Silakan buat user admin desa terlebih dahulu.');
        }
    }
}
