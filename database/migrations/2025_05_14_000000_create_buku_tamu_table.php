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
        Schema::create('buku_tamu', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('alamat');
            $table->string('no_telepon');
            $table->string('email')->nullable();
            $table->string('keperluan');
            $table->text('pesan')->nullable();
            $table->text('tanda_tangan')->nullable();
            $table->string('province_id');
            $table->string('district_id');
            $table->string('sub_district_id');
            $table->string('village_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_tamu');
    }
};