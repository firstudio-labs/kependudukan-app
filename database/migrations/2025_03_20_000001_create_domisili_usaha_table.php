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
        Schema::create('domisili_usaha', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('province_id');
            $table->bigInteger('district_id');
            $table->bigInteger('subdistrict_id');
            $table->bigInteger('village_id');
            $table->string('letter_number')->nullable();
            $table->bigInteger('nik');
            $table->string('full_name');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->bigInteger('gender');
            $table->bigInteger('job_type_id');
            $table->bigInteger('religion');
            $table->bigInteger('citizen_status');
            $table->text('address');
            $table->longText('rt');
            $table->date('letter_date');
            $table->string('business_type');
            $table->text('business_address');
            $table->string('business_year');
            $table->text('purpose');
            $table->string('signing')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domisili_usaha');
    }
};
