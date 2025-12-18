<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('client_credentials', function (Blueprint $table) {
            $table->string('execution_frequency')->nullable()->after('is_active')
                ->comment('Cron format or predefined key (e.g., daily)');
            $table->string('alert_email')->nullable()->after('execution_frequency')
                ->comment('Email to receive execution alerts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_credentials', function (Blueprint $table) {
            //
        });
    }
};
