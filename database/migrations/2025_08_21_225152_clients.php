<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Identificador público para sistemas externos');

            // --- NUEVO: Lógica Sede vs Anexo ---
            // Si está vacío (NULL) es la CASA CENTRAL. Si tiene un ID, es un ANEXO de esa central.
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('clients')
                ->onDelete('cascade')
                ->comment('Jerarquía: NULL = Sede, ID = Anexo/Sucursal');

            $table->string('branch_name')->nullable()->comment('Nombre de la sucursal si es anexo (Ej: Local Centro)');

            // --- Identificación Fiscal ---
            // CAMBIO IMPORTANTE: Quitamos 'unique()' para permitir cargar sucursales con mismo CUIT
            $table->string('cuit', 11)->index()->comment('CUIT (sin guiones). No es único para permitir sucursales.');

            $table->string('company')->comment('Razón Social (Nombre legal en facturas)');
            $table->string('fantasy_name')->nullable()->comment('Nombre de fantasía / Marca comercial');

            // --- Clasificación Contable y Datos de Negocio ---
            $table->string('tax_condition')->nullable()->comment('Condición frente al IVA');

            // --- NUEVO: Lo que pediste recién ---
            $table->string('industry')->nullable()->comment('Rubro (Ej: Gastronomía, Salud)');
            $table->integer('employees_count')->nullable()->unsigned()->comment('Cantidad de empleados');

            // --- Contacto Principal ---
            $table->string('email')->nullable()->comment('Email para notificaciones');
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            // --- Domicilio Fiscal ---
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();

            // --- Gestión Interna ---
            $table->text('internal_notes')->nullable();
            $table->unsignedBigInteger('external_reference_id')->nullable()->index();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Contador asignado');

            $table->boolean('active')->default(true); // Mantengo 'active' como en tu código original

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
