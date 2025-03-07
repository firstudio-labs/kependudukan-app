<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kk_id');
            $table->string('full_name');
            $table->string('family_status');
            $table->timestamps();

            $table->foreign('kk_id')->references('id')->on('kk')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('family_members');
    }
};
