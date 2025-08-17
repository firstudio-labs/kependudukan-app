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
            $table->string('id_provinsi', 10)->nullable()->after('id_kabupaten');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita_desas', function (Blueprint $table) {
            $table->dropColumn('id_provinsi');
        });
    }
};
