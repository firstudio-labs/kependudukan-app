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
        Schema::table('jenis_aset', function (Blueprint $table) {
            $table->unsignedBigInteger('klasifikasi_id')->nullable()->after('kode');
            $table->foreign('klasifikasi_id')->references('id')->on('klasifikasi')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_aset', function (Blueprint $table) {
            $table->dropForeign(['klasifikasi_id']);
            $table->dropColumn('klasifikasi_id');
        });
    }
};
