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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('api_service_id')->constrained()->onDelete('cascade');
            
            $table->dateTime('date_at')->index();
            $table->decimal('amount', 15, 2);
            $table->string('currency')->default('ARS');
            $table->string('type'); // income, expense
            $table->string('description');
            $table->string('status')->default('pending'); // pending, verified, rejected
            
            $table->json('raw_data')->nullable(); // Para guardar toda la data original
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
