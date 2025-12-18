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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('api_service_id')->constrained()->onDelete('cascade');
            $table->string('status')->index(); // success, warning, error, pending
            $table->string('event_type'); // "Sync Facturas", "Token Refresh"
            $table->text('details')->nullable(); // Error message or JSON dump
            $table->timestamp('happened_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
