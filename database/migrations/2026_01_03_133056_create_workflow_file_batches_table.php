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
        Schema::create('workflow_file_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_type_id')->constrained('workflow_types')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('batch_code', 50)->unique();
            $table->enum('status', ['pending', 'validated', 'executing', 'completed', 'failed'])->default('pending');
            $table->json('validation_errors')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index(['client_id', 'branch_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_file_batches');
    }
};
