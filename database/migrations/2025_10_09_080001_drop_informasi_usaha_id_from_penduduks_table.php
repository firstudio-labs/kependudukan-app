<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penduduks', function (Blueprint $table) {
            if (Schema::hasColumn('penduduks', 'informasi_usaha_id')) {
                // Jika ada foreign key/index terkait, pastikan di-drop di migration sebelumnya atau di sini jika tahu nama constraint-nya
                // Contoh (sesuaikan jika constraint bernama khusus):
                // $table->dropForeign(['informasi_usaha_id']);
                // $table->dropUnique(['informasi_usaha_id']);
                try { $table->dropUnique(['informasi_usaha_id']); } catch (\Throwable $e) {}
                try { $table->dropForeign(['informasi_usaha_id']); } catch (\Throwable $e) {}
                $table->dropColumn('informasi_usaha_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penduduks', function (Blueprint $table) {
            if (!Schema::hasColumn('penduduks', 'informasi_usaha_id')) {
                $table->unsignedBigInteger('informasi_usaha_id')->nullable()->after('tag_lokasi');
                // Optional: tambahkan kembali constraint/index bila diperlukan
                // $table->foreign('informasi_usaha_id')->references('id')->on('informasi_usahas')->nullOnDelete();
                // $table->unique(['informasi_usaha_id']);
            }
        });
    }
};


