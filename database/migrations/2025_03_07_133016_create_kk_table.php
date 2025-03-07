<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('kk', function (Blueprint $table) {
            $table->id();
            $table->string('kk')->unique();
            $table->string('full_name');
            $table->text('address');
            $table->string('postal_code');
            $table->string('rt');
            $table->string('rw');
            $table->integer('jml_anggota_kk');
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('province_id');
            $table->string('district_id');
            $table->string('sub_district_id');
            $table->string('village_id');
            $table->string('dusun')->nullable();
            $table->text('alamat_luar_negeri')->nullable();
            $table->string('kota')->nullable();
            $table->string('negara_bagian')->nullable();
            $table->string('negara')->nullable();
            $table->string('kode_pos_luar_negeri')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kk');
    }
};