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
        Schema::table('berita_desas', function (Blueprint $table) {
            // Ubah tipe data field wilayah agar sesuai dengan tabel users
            $table->unsignedBigInteger('id_provinsi')->nullable()->change();
            $table->unsignedBigInteger('id_kabupaten')->nullable()->change();
            $table->unsignedBigInteger('id_kecamatan')->nullable()->change();
            $table->unsignedBigInteger('id_desa')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita_desas', function (Blueprint $table) {
            // Kembalikan ke tipe data semula
            $table->string('id_provinsi', 10)->nullable()->change();
            $table->integer('id_kabupaten')->nullable()->change();
            $table->integer('id_kecamatan')->nullable()->change();
            $table->integer('id_desa')->nullable()->change();
        });
    }
};
