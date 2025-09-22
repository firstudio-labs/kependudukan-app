<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usaha_desas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('jenis', ['BUMDES', 'Koperasi', 'Lainnya']);
            $table->string('nama');
            $table->string('ijin')->nullable();
            $table->year('tahun_didirikan')->nullable();
            $table->string('ketua')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usaha_desas');
    }
};


