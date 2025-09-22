<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_saranas', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_sarana', [
                'Pendidikan', 'Tempat Ibadah', 'Sarana Kesehatan', 'TPS', 'Sanitasi',
                'Akses Digital', 'Gedung Serbaguna', 'Sarana Olahraga & Kesenian', 'Jalan Desa', 'Lainnya'
            ]);
            $table->string('kategori');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_saranas');
    }
};


