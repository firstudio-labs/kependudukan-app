<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('abdes', function (Blueprint $table) {
            if (Schema::hasColumn('abdes', 'total_anggaran')) {
                $table->dropColumn('total_anggaran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('abdes', function (Blueprint $table) {
            $table->decimal('total_anggaran', 15, 2)->nullable();
        });
    }
};


