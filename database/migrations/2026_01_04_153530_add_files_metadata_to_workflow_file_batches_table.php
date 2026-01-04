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
            $table->json('files_metadata')->nullable()->after('validation_errors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_file_batches', function (Blueprint $table) {
            $table->dropColumn('files_metadata');
        });
    }
};
