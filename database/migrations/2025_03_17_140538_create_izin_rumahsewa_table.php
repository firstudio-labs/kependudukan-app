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
        Schema::create('izin_rumahsewa', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('province_id');
            $table->bigInteger('district_id');
            $table->bigInteger('subdistrict_id');
            $table->bigInteger('village_id');
            $table->string('letter_number')->nullable();

            // Organizer data
            $table->bigInteger('nik');
            $table->string('full_name'); // Nama Penyelenggara
            $table->string('address'); // Alamat Penyelenggara
            $table->string('responsible_name'); // Nama Penanggung Jawab

            // Rental house details
            $table->string('rental_address'); // Alamat Rumah Sewa
            $table->string('street'); // Jalan
            $table->string('village_name'); // Kelurahan
            $table->string('alley_number'); // Gang/Nomor
            $table->longText('rt');
            $table->string('building_area'); // Luas Bangunan
            $table->integer('room_count'); // Jumlah Kamar
            $table->string('rental_type'); // Jenis Rumah/Kamar Sewa
            $table->date('valid_until')->nullable(); // Berlaku Ijin Sampai

            // Signing official
            $table->string('signing')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_rumahsewa');
    }
};
