<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_desas', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('gambar')->nullable();
            $table->longText('deskripsi');
            $table->text('alamat')->nullable();
            $table->string('tag_lokasi')->nullable(); // "lat,lng"
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('districts_id')->nullable();
            $table->unsignedBigInteger('sub_districts_id')->nullable();
            $table->unsignedBigInteger('villages_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['province_id', 'districts_id', 'sub_districts_id', 'villages_id'], 'agenda_wilayah_index');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_desas');
    }
};


