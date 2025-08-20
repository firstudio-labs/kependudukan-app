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
        // Drop tabel lama jika ada
        Schema::dropIfExists('berita_desas');
        
        Schema::create('berita_desas', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('gambar')->nullable();
            $table->text('deskripsi');
            $table->text('komentar')->nullable();
            $table->unsignedBigInteger('user_id');
            
            // Field wilayah yang lengkap dan konsisten dengan tabel users
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('districts_id')->nullable();
            $table->unsignedBigInteger('sub_districts_id')->nullable();
            $table->unsignedBigInteger('villages_id')->nullable();
            
            $table->timestamps();

            // Foreign key constraint hanya untuk user
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes untuk performa query
            $table->index(['province_id', 'districts_id', 'sub_districts_id', 'villages_id'], 'berita_wilayah_index');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_desas');
    }
};
