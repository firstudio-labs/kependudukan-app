<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abdes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('jenis', ['Perencanaan','Realisasi']);
            $table->enum('kategori', [
                'Pemerintahan Desa', 'Pembangunan Desa', 'Pembinaan Desa', 'Pemberdayaan Masyarakat', 'Penanggulangan Bencana dan Darurat Desa'
            ]);
            $table->decimal('jumlah_anggaran', 15, 2);
            $table->decimal('total_anggaran', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abdes');
    }
};


