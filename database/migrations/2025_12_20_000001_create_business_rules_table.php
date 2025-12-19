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
        Schema::create('business_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type', 50)->default('extraction');
            $table->string('status', 20)->default('active');
            
            // Vinculación con Enterprise
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('api_service_id')->nullable();
            $table->unsignedBigInteger('endpoint_id')->nullable();
            
            // Código Python
            $table->text('python_code');
            $table->text('input_schema')->nullable();
            $table->text('output_schema')->nullable();
            
            // Metadata
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('last_executed_at')->nullable();
            $table->integer('execution_count')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_rules');
    }
};
