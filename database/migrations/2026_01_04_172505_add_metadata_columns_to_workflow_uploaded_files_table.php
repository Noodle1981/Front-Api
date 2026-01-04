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
        Schema::table('workflow_uploaded_files', function (Blueprint $table) {
            if (!Schema::hasColumn('workflow_uploaded_files', 'rows_count')) {
                $table->integer('rows_count')->nullable()->after('file_size');
            }
            if (!Schema::hasColumn('workflow_uploaded_files', 'columns_count')) {
                $table->integer('columns_count')->nullable()->after('rows_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_uploaded_files', function (Blueprint $table) {
            $table->dropColumn(['rows_count', 'columns_count']);
        });
    }
};
