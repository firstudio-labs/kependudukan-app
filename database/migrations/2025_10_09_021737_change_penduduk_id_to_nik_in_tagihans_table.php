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
        Schema::table('tagihans', function (Blueprint $table) {
            // Drop the old penduduk_id column
            $table->dropColumn('penduduk_id');
            
            // Add new nik column
            $table->string('nik')->after('villages_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            // Drop the nik column
            $table->dropColumn('nik');
            
            // Add back the penduduk_id column
            $table->bigInteger('penduduk_id')->after('villages_id');
        });
    }
};