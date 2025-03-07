<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kk', function (Blueprint $table) {
            $table->id(); // Kolom id yang auto increment
            $table->bigInteger('kk')->unique(); // Nomor KK (unik, bigint)
            $table->string('full_name'); // Nama Lengkap (Kepala Keluarga)
            $table->string('address'); // Alamat
            $table->string('postal_code'); // Kode Pos
            $table->string('rt');
            $table->string('rw');
            $table->string('jml_anggota_kk');
            $table->string('telepon')->nullable();
            $table->string('email')->nullable(); // Email (nullable);
            $table->string('province_id'); // Provinsi
            $table->string('district_id'); // Kabupaten
            $table->string('sub_district_id'); // Kecamatan
            $table->string('village_id'); // Desa/Kelurahan
            $table->string('dusun')->nullable(); // Dusun/Dukuh/Kampung (nullable)
            $table->string('alamat_luar_negeri')->nullable(); // Alamat di Luar Negeri (nullable)
            $table->string('kota')->nullable(); // Alamat di Luar Negeri (nullable)
            $table->string('negara_bagian')->nullable(); // Alamat di Luar Negeri (nullable)
            $table->string('negara')->nullable(); // Alamat di Luar Negeri (nullable)
            $table->string('kode_pos_luar_negeri')->nullable(); // Alamat di Luar Negeri (nullable)
            $table->json('family_members')->nullable(); // Add this field for storing family members array
            $table->timestamps(); // Kolom created_at dan updated_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kk');
    }
};
