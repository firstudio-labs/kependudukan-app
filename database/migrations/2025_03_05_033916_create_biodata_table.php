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
        Schema::create('biodata', function (Blueprint $table) {
            $table->id(); // Kolom id sebagai primary key
            $table->bigInteger('nik')->unique(); // NIK, unique
            $table->bigInteger('kk'); // No KK
            $table->string('full_name'); // Nama lengkap
            $table->enum('gender', ['Laki-Laki', 'Perempuan'])->nullable(); // Jenis kelamin
            $table->longText('birth_date'); // Tanggal lahir
            $table->bigInteger('age'); // Umur
            $table->longText('birth_place'); // Tempat lahir
            $table->longText('address')->nullable(); // Alamat
            $table->unsignedBigInteger('province_id')->nullable(); // Provinsi (foreign key)
            $table->unsignedBigInteger('district_id')->nullable(); // Kabupaten (foreign key)
            $table->unsignedBigInteger('sub_district_id')->nullable(); // Kecamatan (foreign key)
            $table->unsignedBigInteger('village_id')->nullable(); // Desa (foreign key)
            $table->longText('rt')->nullable(); // RT
            $table->longText('rw')->nullable(); // RW
            $table->bigInteger('postal_code')->nullable(); // Kode POS
            $table->enum('citizen_status', ['WNA', 'WNI'])->nullable(); // Status kewarganegaraan
            $table->enum('birth_certificate', ['Ada', 'Tidak Ada'])->nullable(); // Akta lahir
            $table->longText('birth_certificate_no')->nullable(); // No akta lahir
            $table->enum('blood_type', ['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Tidak Tahu'])->nullable(); // Golongan darah
            $table->enum('religion', ['Islam', 'Kristen', 'Katholik', 'Hindu', 'Buddha', 'Kong Hu Cu', 'Lainya....'])->nullable(); // Agama
            $table->enum('marital_status', ['Belum Kawin', 'Kawin Tercatat', 'Kawin Belum Tercatat', 'Cerai Hidup Tercatat', 'Cerai Hidup Belum Tercatat', 'Cerai Mati'])->nullable(); // Status perkawinan
            $table->enum('marital_certificate', ['Ada', 'Tidak Ada'])->nullable(); // Akta perkawinan
            $table->longText('marital_certificate_no')->nullable(); // No akta perkawinan
            $table->longText('marriage_date')->nullable(); // Tanggal perkawinan
            $table->enum('divorce_certificate', ['Ada', 'Tidak Ada'])->nullable(); // Akta cerai
            $table->longText('divorce_certificate_no')->nullable(); // No akta cerai
            $table->longText('divorce_certificate_date')->nullable(); // Tanggal cerai
            $table->enum('family_status', ['KEPALA KELUARGA', 'ISTRI', 'ANAK', 'MERTUA', 'ORANG TUA', 'CUCU', 'FAMILI LAIN', 'LAINNYA']); // Status hubungan dalam keluarga
            $table->enum('mental_disorders', ['Ada', 'Tidak Ada'])->default('Tidak Ada'); // Kelainan fisik dan mental
            $table->longText('disabilities')->nullable(); // Penyandang cacat
            $table->enum('education_status', ['Tidak/Belum Sekolah', 'Belum tamat SD/Sederajat', 'Tamat SD', 'SLTP/SMP/Sederajat', 'SLTA/SMA/Sederajat', 'Diploma I/II', 'Akademi/Diploma III/ Sarjana Muda', 'Diploma IV/ Strata I/ Strata II', 'Strata III', 'Lainya...'])->nullable(); // Pendidikan terakhir
            $table->unsignedBigInteger('job_type_id'); // Jenis pekerjaan (foreign key)
            $table->string('nik_mother')->nullable(); // NIK ibu
            $table->string('mother')->nullable(); // Nama ibu
            $table->string('nik_father')->nullable(); // NIK ayah
            $table->string('father')->nullable(); // Nama ayah
            $table->longText('coordinate')->nullable(); // Koordinat lokasi

            // Timestamps
            $table->timestamps(); // created_at dan updated_at

            // Foreign key constraints

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biodata');
    }
};
