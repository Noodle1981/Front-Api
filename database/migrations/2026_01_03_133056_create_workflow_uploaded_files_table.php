<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_uploaded_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_file_batch_id')->constrained('workflow_file_batches')->onDelete('cascade');
            $table->foreignId('workflow_file_definition_id')->constrained('workflow_file_definitions')->onDelete('cascade');
            $table->string('original_filename', 255);
            $table->string('stored_filename', 255);
            $table->string('file_path', 500);
            $table->bigInteger('file_size')->comment('Size in bytes');
            $table->integer('rows_count')->nullable();
            $table->enum('validation_status', ['pending', 'valid', 'invalid'])->default('pending');
            $table->json('validation_errors')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_uploaded_files');
    }
};
