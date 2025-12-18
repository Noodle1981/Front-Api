<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('recipient_email');
            $table->string('subject');
            $table->string('type'); // 'test', 'api_error', 'api_success', 'system'
            $table->text('content')->nullable();
            $table->string('status'); // 'sent', 'failed'
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // client_id, credential_id, etc.
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
