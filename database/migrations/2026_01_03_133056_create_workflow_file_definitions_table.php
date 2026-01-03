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
        Schema::create('workflow_file_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_type_id')->constrained('workflow_types')->onDelete('cascade');
            $table->string('file_identifier', 50);
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            $table->integer('order')->default(1);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_file_definitions');
    }
};
