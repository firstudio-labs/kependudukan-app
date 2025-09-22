<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sarana_umums', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('kategori_sarana_id');
            $table->string('nama_sarana');
            $table->string('alamat')->nullable();
            $table->string('kontak')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('kategori_sarana_id')->references('id')->on('kategori_saranas')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sarana_umums');
    }
};


