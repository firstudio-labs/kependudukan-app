<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informasi_usahas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('penduduk_id')->nullable();
            $table->string('nama_usaha');
            $table->enum('kelompok_usaha', [
                'UMKM',
                'BUMDES',
                'Mandiri/Perseorangan',
                'KUB (Kelompok Usaha Bersama)',
                'Korporasi/Perusahaan'
            ]);
            $table->text('alamat')->nullable();
            $table->string('tag_lokasi')->nullable();
            $table->string('foto')->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('districts_id')->nullable();
            $table->unsignedBigInteger('sub_districts_id')->nullable();
            $table->unsignedBigInteger('villages_id')->nullable();
            $table->timestamps();

            $table->index(['villages_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informasi_usahas');
    }
};


