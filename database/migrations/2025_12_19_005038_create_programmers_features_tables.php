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
        // Endpoints for existing ApiServices
        Schema::create('endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_service_id')->constrained('api_services')->onDelete('cascade');
            $table->string('method')->default('GET'); // GET, POST, PUT, DELETE
            $table->string('url'); // Relative path e.g. /users
            $table->string('description')->nullable();
            $table->json('parameters')->nullable(); // Expected params structure
            $table->timestamps();
        });

        // Business Rules (Python Scripts)
        Schema::create('business_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('python_script')->nullable(); // The python code
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Workflows
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('trigger_type')->default('manual'); // manual, schedule, webhook
            $table->string('schedule')->nullable(); // Cron expression if scheduled
            $table->json('steps_json')->nullable(); // Logic flow definition
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Workflow Executions (History)
        Schema::create('workflow_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('workflows')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->longText('logs_json')->nullable(); // Execution logs
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_executions');
        Schema::dropIfExists('workflows');
        Schema::dropIfExists('business_rules');
        Schema::dropIfExists('endpoints');
    }
};
