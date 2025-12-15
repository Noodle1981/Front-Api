<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('client_type');
            $table->string('client_status')->default('nuevo')->after('registration_date')->comment('Estado del cliente: nuevo, activo, inactivo');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('client_status');
            $table->string('client_type')->nullable()->after('registration_date')->comment('Tipo de cliente: potencial, activo, inactivo, etc.');
        });
    }
};
