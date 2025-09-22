<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warungku_masters', function (Blueprint $table) {
            $table->id();
            $table->enum('klasifikasi', ['barang', 'jasa']);
            $table->string('jenis');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warungku_masters');
    }
};


