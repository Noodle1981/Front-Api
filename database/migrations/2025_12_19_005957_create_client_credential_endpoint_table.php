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
        Schema::create('client_credential_endpoint', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_credential_id')->constrained('client_credentials')->onDelete('cascade');
            $table->foreignId('endpoint_id')->constrained('endpoints')->onDelete('cascade');
            $table->unique(['client_credential_id', 'endpoint_id'], 'cred_endpoint_unique');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_credential_endpoint');
    }
};
