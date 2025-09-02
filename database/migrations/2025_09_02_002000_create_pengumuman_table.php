<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('gambar')->nullable();
            $table->text('deskripsi');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('districts_id')->nullable();
            $table->unsignedBigInteger('sub_districts_id')->nullable();
            $table->unsignedBigInteger('villages_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Gunakan index terpisah agar nama tidak terlalu panjang di MySQL
            $table->index('province_id', 'pengumuman_prov_idx');
            $table->index('districts_id', 'pengumuman_dist_idx');
            $table->index('sub_districts_id', 'pengumuman_subdist_idx');
            $table->index('villages_id', 'pengumuman_vill_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};


