<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->date('registration_date')->nullable()->after('art_registration_date')->comment('Fecha de alta como cliente');
            $table->string('client_type')->nullable()->after('registration_date')->comment('Tipo de cliente: potencial, activo, inactivo, etc.');
            $table->string('activity_code')->nullable()->after('economic_activity')->comment('Código de actividad económica (AFIP, CNAE, etc.)');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['registration_date', 'client_type', 'activity_code']);
        });
    }
};
