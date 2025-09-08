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
        Schema::table('laporan_desas', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_desas', 'lokasi')) {
                $table->text('lokasi')->nullable()->after('gambar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_desas', function (Blueprint $table) {
            if (Schema::hasColumn('laporan_desas', 'lokasi')) {
                $table->dropColumn('lokasi');
            }
        });
    }
};
