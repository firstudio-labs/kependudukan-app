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
        Schema::create('sr_ktp', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('province_id');
            $table->bigInteger('district_id');
            $table->bigInteger('subdistrict_id');
            $table->bigInteger('village_id');
            $table->string('letter_number')->nullable();
            $table->string('application_type'); // Baru, Perpanjang, Pergantian

            // Citizen data (changed from JSON to specific data types)
            $table->bigInteger('nik');
            $table->string('full_name');

            // Additional data
            $table->bigInteger('kk'); // Changed from 'kk' for consistency
            $table->text('address');
            $table->longText('rt');
            $table->longText('rw');
            $table->string('hamlet'); // Dusun
            $table->bigInteger('village_name'); // Using village_name instead of duplicate village_id
            $table->bigInteger('subdistrict_name'); // Using subdistrict_name instead of duplicate subdistrict_id

            // Signing official
            $table->string('signing')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sr_ktp');
    }
};
