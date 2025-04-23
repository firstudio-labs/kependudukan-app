<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_desas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lapor_desa_id');
            $table->string('judul_laporan');
            $table->text('deskripsi_laporan');
            $table->string('gambar')->nullable();
            $table->string('tag_lokasi')->nullable();
            $table->enum('status', ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'])->default('Menunggu');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('village_id');
            $table->timestamps();

            $table->foreign('lapor_desa_id')->references('id')->on('lapor_desas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_desas');
    }
};
