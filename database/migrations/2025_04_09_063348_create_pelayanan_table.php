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
        Schema::create('pelayanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('province_id');
            $table->string('district_id');
            $table->string('sub_district_id');
            $table->string('village_id');
            $table->string('alamat');
            $table->unsignedBigInteger('keperluan');  // Changed to unsignedBigInteger to match Keperluan id type
            $table->integer('no_antrian')->nullable();
            $table->timestamps();

            // Add foreign key constraint now that types match
            $table->foreign('keperluan')->references('id')->on('keperluan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelayanan');
    }
};
