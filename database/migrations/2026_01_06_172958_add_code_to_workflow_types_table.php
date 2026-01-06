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
        Schema::table('workflow_types', function (Blueprint $table) {
            $table->string('code', 50)->nullable()->after('name')->unique();
        });

        // Update existing record with code
        DB::table('workflow_types')->where('name', 'Conciliación')->update(['code' => 'conciliacion']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_types', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
