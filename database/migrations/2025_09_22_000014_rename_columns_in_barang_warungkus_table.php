<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_warungkus', function (Blueprint $table) {
            if (Schema::hasColumn('barang_warungkus', 'klasifikasi_id')) {
                $table->dropColumn('klasifikasi_id');
            }
            if (Schema::hasColumn('barang_warungkus', 'klasifikasi_master_id')) {
                $table->dropColumn('klasifikasi_master_id');
            }
            if (Schema::hasColumn('barang_warungkus', 'jenis_id')) {
                $table->renameColumn('jenis_id', 'jenis_master_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('barang_warungkus', function (Blueprint $table) {
            // Restore columns if needed
            if (!Schema::hasColumn('barang_warungkus', 'klasifikasi_id')) {
                $table->unsignedBigInteger('klasifikasi_id')->nullable();
            }
            if (Schema::hasColumn('barang_warungkus', 'jenis_master_id')) {
                $table->renameColumn('jenis_master_id', 'jenis_id');
            }
        });
    }
};


