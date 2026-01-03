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
        Schema::table('workflow_executions', function (Blueprint $table) {
            $table->foreignId('workflow_file_batch_id')->nullable()->after('workflow_id')->constrained('workflow_file_batches')->onDelete('cascade');
            $table->longText('json_sent')->nullable()->after('logs_json');
            $table->longText('json_response')->nullable()->after('json_sent');
            $table->integer('execution_time_ms')->nullable()->after('json_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_executions', function (Blueprint $table) {
            $table->dropForeign(['workflow_file_batch_id']);
            $table->dropColumn(['workflow_file_batch_id', 'json_sent', 'json_response', 'execution_time_ms']);
        });
    }
};
