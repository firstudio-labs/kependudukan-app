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
        Schema::create('ahli_waris', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('province_id');
            $table->bigInteger('district_id');
            $table->bigInteger('subdistrict_id');
            $table->bigInteger('village_id');
            $table->string('letter_number')->nullable();
            $table->json('nik')->nullable();
            $table->json('full_name')->nullable();
            $table->json('birth_place')->nullable();
            $table->json('birth_date')->nullable();
            $table->json('gender')->nullable();
            $table->json('religion')->nullable();
            $table->json('address')->nullable();
            $table->json('family_status')->nullable();
            $table->string('heir_name');
            $table->string('deceased_name');
            $table->string('death_place');
            $table->date('death_date');
            $table->integer('death_certificate_number')->nullable();
            $table->date('death_certificate_date')->nullable();
            $table->date('inheritance_letter_date')->nullable();
            $table->string('inheritance_type');
            $table->string('signing')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahli_waris');
    }
};
