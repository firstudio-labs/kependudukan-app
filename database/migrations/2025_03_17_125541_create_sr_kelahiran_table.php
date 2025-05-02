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
        Schema::create('sr_kelahiran', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('province_id');
            $table->bigInteger('district_id');
            $table->bigInteger('subdistrict_id');
            $table->bigInteger('village_id');
            $table->string('letter_number')->nullable();

            // Father data (single fields)
            $table->bigInteger('father_nik')->nullable();
            $table->string('father_full_name')->nullable();
            $table->string('father_birth_place')->nullable();
            $table->date('father_birth_date')->nullable();
            $table->bigInteger('father_job')->nullable();
            $table->bigInteger('father_religion')->nullable();
            $table->text('father_address')->nullable();

            // Mother data (single fields)
            $table->bigInteger('mother_nik')->nullable();
            $table->string('mother_full_name')->nullable();
            $table->string('mother_birth_place')->nullable();
            $table->date('mother_birth_date')->nullable();
            $table->bigInteger('mother_job')->nullable();
            $table->bigInteger('mother_religion')->nullable();
            $table->text('mother_address')->nullable();

            // Child data
            $table->string('child_name')->nullable();
            $table->bigInteger('child_gender')->nullable();
            $table->date('child_birth_date')->nullable();
            $table->string('child_birth_place')->nullable();
            $table->bigInteger('child_religion')->nullable();
            $table->string('child_address')->nullable();
            $table->integer('child_order')->nullable();

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
        Schema::dropIfExists('sr_kelahiran');
    }
};
