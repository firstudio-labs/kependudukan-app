<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            $table->unsignedBigInteger('informasi_usaha_id')->nullable()->after('tag_lokasi');
            $table->unique(['informasi_usaha_id']);
        });
    }

    public function down(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            $table->dropUnique(['informasi_usaha_id']);
            $table->dropColumn('informasi_usaha_id');
        });
    }
};


