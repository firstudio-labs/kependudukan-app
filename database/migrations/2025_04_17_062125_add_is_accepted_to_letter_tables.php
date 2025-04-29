<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAcceptedToLetterTables extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'ahli_waris',
            'izin_rumahsewa',
            'sr_ktp',
            'sr_kematian',
            'sr_kelahiran',
            'skck',
            'sr_izin_keramaian',
            'domisili_usaha',
            'domisili',
            'sr_kehilangan',
            'administration'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'is_accepted')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->boolean('is_accepted')->default(0);
                });
            }
        }
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'ahli_waris',
            'izin_rumahsewa',
            'sr_ktp',
            'sr_kematian',
            'sr_kelahiran',
            'skck',
            'sr_izin_keramaian',
            'domisili_usaha',
            'domisili',
            'sr_kehilangan',
            'administration'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'is_accepted')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('is_accepted');
                });
            }
        }
    }
}
