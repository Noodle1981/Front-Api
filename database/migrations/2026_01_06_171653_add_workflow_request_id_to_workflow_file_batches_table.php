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
        Schema::table('workflow_file_batches', function (Blueprint $table) {
            $table->foreignId('workflow_request_id')->nullable()->after('user_id')->constrained('workflow_requests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_file_batches', function (Blueprint $table) {
            $table->dropForeign(['workflow_request_id']);
            $table->dropColumn('workflow_request_id');
        });
    }
};
