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
        Schema::create('administration', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('province_id');
            $table->bigInteger('district_id');  // kabupaten
            $table->bigInteger('subdistrict_id'); // kecamatan
            $table->bigInteger('village_id');
            $table->string('letter_number')->nullable();
            $table->bigInteger('nik'); // Changed from string to bigInteger
            $table->string('full_name');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->bigInteger('gender'); // Changed from string to bigInteger
            $table->bigInteger('job_type_id'); // Changed from string to bigInteger
            $table->bigInteger('religion');
            $table->bigInteger('citizen_status'); // Changed from string to bigInteger
            $table->text('address');
            $table->string('signing')->nullable();
            $table->longText('rt'); // Changed from integer to longText
            $table->date('letter_date');
            $table->text('statement_content'); // menyatakan bahwa
            $table->text('purpose'); // digunakan untuk
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administration');
    }
};
