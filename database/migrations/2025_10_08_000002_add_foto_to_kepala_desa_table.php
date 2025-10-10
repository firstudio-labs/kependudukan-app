<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kepala_desa', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('kepala_desa', function (Blueprint $table) {
            if (Schema::hasColumn('kepala_desa', 'foto')) {
                $table->dropColumn('foto');
            }
        });
    }
};


