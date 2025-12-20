<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar si la columna 'name' ya existe
        if (!Schema::hasColumn('client_credentials', 'name')) {
            Schema::table('client_credentials', function (Blueprint $table) {
                $table->string('name')->nullable();
            });
        }

        // Para SQLite no se puede cambiar columna, solo verificamos que exista
        // En MySQL/PostgreSQL sí se podría usar ->change()
        if (DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('client_credentials', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('client_credentials', 'name')) {
            Schema::table('client_credentials', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
};
