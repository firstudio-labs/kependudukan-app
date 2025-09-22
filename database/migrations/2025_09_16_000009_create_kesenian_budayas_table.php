<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kesenian_budayas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('jenis', [
                'Kuda Lumping','Kobrosiswo','Warokan','Topeng ireng','Wayang','Janen/Sholawatan','Kosidah/Nasyidariyah','Dolilak','Rebana','Lainnya'
            ]);
            $table->string('nama');
            $table->string('tag_lokasi')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kontak')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kesenian_budayas');
    }
};


