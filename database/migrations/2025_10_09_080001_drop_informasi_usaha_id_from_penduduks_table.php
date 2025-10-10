<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            if (Schema::hasColumn('penduduk', 'informasi_usaha_id')) {
                // Drop kolom langsung tanpa mencoba drop constraint
                // MySQL akan otomatis menghapus constraint terkait
                $table->dropColumn('informasi_usaha_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            if (!Schema::hasColumn('penduduk', 'informasi_usaha_id')) {
                $table->unsignedBigInteger('informasi_usaha_id')->nullable()->after('tag_lokasi');
                // Optional: tambahkan kembali constraint/index bila diperlukan
                // $table->foreign('informasi_usaha_id')->references('id')->on('informasi_usahas')->nullOnDelete();
                // $table->unique(['informasi_usaha_id']);
            }
        });
    }
};


