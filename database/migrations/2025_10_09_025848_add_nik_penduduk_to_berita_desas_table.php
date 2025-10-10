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
        Schema::table('berita_desas', function (Blueprint $table) {
            $table->string('nik_penduduk', 16)->nullable()->after('user_id');
            $table->index('nik_penduduk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita_desas', function (Blueprint $table) {
            $table->dropIndex(['nik_penduduk']);
            $table->dropColumn('nik_penduduk');
        });
    }
};
