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
        Schema::create('workflow_required_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_file_definition_id')->constrained('workflow_file_definitions')->onDelete('cascade');
            $table->string('column_name', 100);
            $table->enum('column_type', ['string', 'integer', 'decimal', 'date', 'boolean'])->default('string');
            $table->boolean('is_required')->default(true);
            $table->json('validation_rules')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_required_columns');
    }
};
