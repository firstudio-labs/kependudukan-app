<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('family_member_documents', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16);
            $table->string('document_type'); 
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('extension')->nullable();
            $table->integer('file_size')->nullable(); 
            $table->string('tag_lokasi')->nullable();
            $table->timestamps();

            $table->index('nik');
            $table->unique(['nik', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_member_documents');
    }
};