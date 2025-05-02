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
        Schema::create('sr_kematian', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('province_id');
            $table->bigInteger('district_id');
            $table->bigInteger('subdistrict_id');
            $table->bigInteger('village_id');
            $table->string('letter_number')->nullable();

            // Citizen data (changed from array to individual fields)
            $table->bigInteger('nik');  // Changed from json nullable to bigInteger not nullable
            $table->string('full_name'); // Changed from json nullable to string not nullable
            $table->string('birth_place'); // Changed from json nullable to string not nullable
            $table->date('birth_date'); // Changed from json nullable to date not nullable
            $table->bigInteger('gender'); // Changed from json nullable to string not nullable
            $table->bigInteger('job_type_id'); // Changed from json nullable to string not nullable
            $table->bigInteger('religion'); // Changed from json nullable to string not nullable
            $table->bigInteger('citizen_status'); // Changed from json nullable to string not nullable
            $table->text('address'); // Changed from json nullable to text not nullable

            // Death certificate specific fields
            $table->string('info'); // Dasar Keterangan
            $table->longText('rt')->nullable(); // RT Asal Surat
            $table->date('rt_letter_date')->nullable(); // Tanggal Surat RT
            $table->string('death_cause')->nullable(); // Penyebab Kematian
            $table->string('death_place')->nullable(); // Tempat Kematian
            $table->string('reporter_name')->nullable(); // Nama Pelapor
            $table->string('reporter_relation')->nullable(); // Hubungan Pelapor
            $table->date('death_date')->nullable(); // Tanggal Meninggal
            $table->string('signing')->nullable(); // Pejabat Penandatangan

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sr_kematian');
    }
};
