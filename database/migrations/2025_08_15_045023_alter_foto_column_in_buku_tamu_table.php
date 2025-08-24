<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('buku_tamu', function (Blueprint $table) {
            $table->text('foto')->change(); // Ubah dari VARCHAR ke TEXT
        });
    }

    public function down()
    {
        Schema::table('buku_tamu', function (Blueprint $table) {
            $table->string('foto')->change(); // Kembalikan ke VARCHAR
        });
    }
};
