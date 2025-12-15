<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // --- Campos Clave de Sincronización con Plataforma H&S ---
            $table->string('cuit')->unique()->comment('Identificador único legal. USADO PARA BUSCAR/VINCULAR con la plataforma H&S.');
            $table->string('name')->comment('Nombre de fantasía. SINCRONIZADO con el campo "nombre" en H&S.');
            $table->string('company')->nullable()->comment('Razón Social. SINCRONIZADO con el campo "razon_social" en H&S.');
            
            // --- ID Externo para Vínculo Permanente ---
            $table->unsignedBigInteger('hs_platform_empresa_id')->nullable()->unique()->comment('ID de la tabla "empresas" en la plataforma H&S. Clave para la integración.');

            // --- Campos Exclusivos del CRM (Información Comercial y Fiscal) ---
            $table->string('website')->nullable()->comment('Sitio web del cliente. (Exclusivo del CRM)');
            $table->string('email')->nullable()->comment('Email de contacto principal/comercial. (Exclusivo del CRM)');
            $table->string('phone')->nullable()->comment('Teléfono de contacto principal/comercial. (Exclusivo del CRM)');
            
            // Domicilio Fiscal (Exclusivo del CRM)
            $table->string('fiscal_address_street')->nullable();
            $table->string('fiscal_address_zip_code')->nullable();
            $table->string('fiscal_address_city')->nullable();
            $table->string('fiscal_address_state')->nullable();
            $table->string('fiscal_address_country')->nullable();

            // --- Campos Comunes, pero gestionados principalmente en CRM ---
            // Estos podrían enviarse a H&S en la creación inicial si son necesarios allí.
            $table->string('economic_activity')->nullable()->comment('Actividad económica principal.');
            $table->string('art_provider')->nullable()->comment('Aseguradora de Riesgos del Trabajo (ART).');
            $table->date('art_registration_date')->nullable()->comment('Fecha de alta en la ART.');
            $table->string('hs_manager_name')->nullable()->comment('Nombre del responsable general de H&S.');
            $table->string('hs_manager_contact')->nullable()->comment('Contacto del responsable general de H&S.');
            
            // --- Campos de Gestión Interna del CRM ---
            $table->text('notes')->nullable()->comment('Notas comerciales y de seguimiento. (Exclusivo del CRM)');
            $table->boolean('active')->default(true);
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')->comment('Vendedor/agente asignado en el CRM.');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
