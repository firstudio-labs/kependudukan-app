<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsetTable extends Migration
{
    
    public function up()
    {
        Schema::create('aset', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('penduduk')->onDelete('set null');
            $table->string('nik_pemilik')->nullable();
            $table->string('nama_pemilik')->nullable();
            $table->string('nama_aset')->nullable();
            $table->text('address');
            $table->unsignedBigInteger('province_id');
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('sub_district_id');
            $table->unsignedBigInteger('village_id');
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->unsignedBigInteger('klasifikasi_id');
            $table->unsignedBigInteger('jenis_aset_id');
            $table->string('foto_aset_depan')->nullable();
            $table->string('foto_aset_samping')->nullable();
            $table->string('tag_lokasi')->nullable();
            $table->timestamps();

            // Add foreign key constraints if needed
            $table->foreign('klasifikasi_id')->references('id')->on('klasifikasi');
            $table->foreign('jenis_aset_id')->references('id')->on('jenis_aset');
        });
    }

    public function down(): void
    {
        Schema::table('aset', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}