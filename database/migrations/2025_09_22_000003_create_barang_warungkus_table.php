<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_warungkus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('informasi_usaha_id');
            $table->string('nama_produk');
            $table->unsignedBigInteger('klasifikasi_id')->nullable();
            $table->unsignedBigInteger('jenis_id')->nullable();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 15, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->index(['informasi_usaha_id']);
            $table->index(['klasifikasi_id']);
            $table->index(['jenis_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_warungkus');
    }
};


