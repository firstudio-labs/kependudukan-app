<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sarana_umums', function (Blueprint $table) {
            $table->string('tag_lokasi')->nullable()->after('nama_sarana');
        });
    }

    public function down(): void
    {
        Schema::table('sarana_umums', function (Blueprint $table) {
            $table->dropColumn('tag_lokasi');
        });
    }
};


