<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usaha_desas', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('ketua');
        });

        Schema::table('sarana_umums', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('kontak');
        });

        Schema::table('kesenian_budayas', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('kontak');
        });
    }

    public function down(): void
    {
        Schema::table('usaha_desas', function (Blueprint $table) {
            if (Schema::hasColumn('usaha_desas', 'foto')) {
                $table->dropColumn('foto');
            }
        });

        Schema::table('sarana_umums', function (Blueprint $table) {
            if (Schema::hasColumn('sarana_umums', 'foto')) {
                $table->dropColumn('foto');
            }
        });

        Schema::table('kesenian_budayas', function (Blueprint $table) {
            if (Schema::hasColumn('kesenian_budayas', 'foto')) {
                $table->dropColumn('foto');
            }
        });
    }
};


