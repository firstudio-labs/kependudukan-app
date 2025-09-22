<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informasi_usahas', function (Blueprint $table) {
            if (!Schema::hasColumn('informasi_usahas', 'kk')) {
                $table->string('kk', 32)->nullable()->after('penduduk_id');
                $table->index('kk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('informasi_usahas', function (Blueprint $table) {
            if (Schema::hasColumn('informasi_usahas', 'kk')) {
                $table->dropIndex(['kk']);
                $table->dropColumn('kk');
            }
        });
    }
};



