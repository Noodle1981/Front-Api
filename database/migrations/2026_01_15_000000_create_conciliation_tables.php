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
        // 1. Resúmenes por turno (arqueo_por_turno)
        Schema::create('conciliation_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_execution_id')->constrained()->onDelete('cascade');

            // Datos del turno
            $table->date('fecha');
            $table->string('dia', 20);
            $table->string('turno', 20);
            $table->string('encargado', 100)->nullable();
            $table->dateTime('apertura')->nullable();
            $table->dateTime('cierre')->nullable();
            $table->decimal('horas_trabajadas', 10, 4)->nullable();

            // Ventas generales
            $table->decimal('ventas_totales', 15, 2)->default(0);
            $table->integer('cantidad_comensales')->default(0);
            $table->decimal('ticket_promedio', 12, 2)->default(0);
            $table->integer('cantidad_tickets')->default(0);
            $table->decimal('propina', 12, 2)->default(0);

            // MercadoPago
            $table->decimal('mp_ventas_real', 15, 2)->default(0);
            $table->decimal('mp_ventas_sistema', 15, 2)->default(0);
            $table->decimal('mp_conciliado', 15, 2)->default(0);
            $table->decimal('mp_no_conciliado', 15, 2)->default(0);
            $table->decimal('mp_diferencia', 15, 2)->default(0);
            $table->decimal('mp_porcentaje', 8, 4)->default(0);
            $table->string('mp_estado', 50)->nullable();

            // Getnet
            $table->decimal('getnet_ventas_real', 15, 2)->default(0);
            $table->decimal('getnet_ventas_sistema', 15, 2)->default(0);
            $table->decimal('getnet_conciliado', 15, 2)->default(0);
            $table->decimal('getnet_no_conciliado', 15, 2)->default(0);
            $table->decimal('getnet_diferencia', 15, 2)->default(0);
            $table->decimal('getnet_porcentaje', 8, 4)->default(0);
            $table->string('getnet_estado', 50)->nullable();

            // Efectivo
            $table->decimal('efectivo_total', 15, 2)->default(0);
            $table->decimal('efectivo_apertura_caja', 15, 2)->default(0);
            $table->decimal('efectivo_recuento', 15, 2)->default(0);
            $table->decimal('efectivo_diferencia', 15, 2)->default(0);
            $table->decimal('efectivo_porcentaje', 8, 4)->default(0);
            $table->string('efectivo_estado', 50)->nullable();

            // Cuenta Corriente y Otros
            $table->decimal('cta_cte_total', 15, 2)->default(0);
            $table->decimal('otros', 15, 2)->default(0);

            // Facturación
            $table->decimal('descuentos', 15, 2)->default(0);
            $table->decimal('ventas_facturadas', 15, 2)->default(0);
            $table->decimal('ideal_facturacion', 15, 2)->default(0);
            $table->decimal('diferencia_facturacion', 15, 2)->default(0);
            $table->decimal('porcentaje_facturacion', 8, 4)->default(0);

            // Ventas por hora (24 slots)
            $table->json('ventas_por_hora')->nullable();

            $table->timestamps();

            // Índices
            $table->index(['workflow_execution_id', 'fecha']);
            $table->index('turno');

            // Clave única para evitar duplicados
            $table->unique(['fecha', 'turno', 'encargado'], 'unique_summary_turno');
        });

        // 2. Transacciones Getnet (getnet_conciliado)
        Schema::create('conciliation_getnet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_execution_id')->constrained()->onDelete('cascade');

            $table->string('nro_establecimiento', 50)->nullable();
            $table->string('nombre_establecimiento', 200)->nullable();
            $table->dateTime('fecha_operacion')->nullable();
            $table->string('billetera', 50)->nullable();
            $table->string('marca', 50)->nullable();
            $table->string('tipo_tarjeta', 50)->nullable();
            $table->string('tarjeta_ultimos4', 10)->nullable();
            $table->string('tipo_transaccion', 50)->nullable();
            $table->string('canal', 50)->nullable();
            $table->string('modo_canal', 50)->nullable();
            $table->string('codigo_pos', 50)->nullable();
            $table->string('estado_venta', 50)->nullable();
            $table->string('cod_transaccion', 100)->nullable();
            $table->string('cod_transaccion_externo', 100)->nullable();
            $table->string('nro_cupon', 50)->nullable();
            $table->string('cod_autorizacion', 50)->nullable();
            $table->string('plan_cuotas', 20)->nullable();
            $table->string('moneda', 10)->default('ARS');
            $table->decimal('monto_bruto', 15, 2)->default(0);
            $table->decimal('arancel', 12, 2)->default(0);
            $table->decimal('iva_arancel', 12, 2)->default(0);
            $table->decimal('propina', 12, 2)->default(0);
            $table->decimal('monto_neto', 15, 2)->default(0);
            $table->date('fecha_liquidacion')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->string('cod_liquidacion', 100)->nullable();
            $table->string('turno', 20)->nullable();
            $table->string('estado_conciliacion', 50)->nullable();
            $table->string('tipo_match', 100)->nullable();
            $table->string('id_venta_sistema', 50)->nullable();
            $table->date('fecha_solo')->nullable();

            $table->timestamps();

            // Índices
            $table->index(['workflow_execution_id', 'fecha_operacion']);
            $table->index('estado_conciliacion');
            $table->index('turno');

            // Clave única - cod_transaccion es único en Getnet
            $table->unique('cod_transaccion', 'unique_getnet_transaction');
        });

        // 3. Transacciones MercadoPago (mp_conciliado)
        Schema::create('conciliation_mp_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_execution_id')->constrained()->onDelete('cascade');

            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
            $table->string('id_operacion_mp', 50)->nullable();
            $table->decimal('monto_neto', 15, 2)->default(0);
            $table->string('tipo_movimiento', 100)->nullable();
            $table->string('medio_pago', 100)->nullable();
            $table->string('metodo_pago', 100)->nullable();
            $table->integer('cuotas')->nullable();
            $table->string('estado', 50)->nullable();
            $table->string('turno', 20)->nullable();
            $table->string('estado_conciliacion', 50)->nullable();
            $table->string('tipo_match', 100)->nullable();
            $table->string('id_venta_sistema', 50)->nullable();
            $table->date('fecha_solo')->nullable();

            $table->timestamps();

            // Índices
            $table->index(['workflow_execution_id', 'fecha']);
            $table->index('estado_conciliacion');
            $table->index('turno');

            // Clave única - id_operacion_mp es único en MercadoPago
            $table->unique('id_operacion_mp', 'unique_mp_transaction');
        });

        // 4. Ventas del sistema (sistema_conciliado + ventas_sistema)
        Schema::create('conciliation_system_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_execution_id')->constrained()->onDelete('cascade');

            $table->string('id_ticket', 50)->nullable();
            $table->dateTime('fecha_hora')->nullable();
            $table->time('hora')->nullable();
            $table->integer('items_count')->default(0);
            $table->decimal('monto_total', 15, 2)->default(0);
            $table->string('tipo_venta', 50)->nullable();
            $table->string('estado_venta', 50)->nullable();
            $table->string('metodo_pago', 100)->nullable();
            $table->string('turno', 20)->nullable();
            $table->string('estado_conciliacion', 50)->nullable();
            $table->string('tipo_match', 100)->nullable();
            $table->string('id_operacion_conciliado', 50)->nullable();
            $table->date('fecha_solo')->nullable();
            $table->boolean('conciliado')->default(false);
            $table->string('source', 20)->default('sistema'); // 'sistema' o 'conciliado'

            $table->timestamps();

            // Índices
            $table->index(['workflow_execution_id', 'fecha_hora']);
            $table->index('estado_conciliacion');
            $table->index('turno');

            // Clave única - id_ticket + source
            $table->unique(['id_ticket', 'source'], 'unique_system_sale');
        });

        // 5. Movimientos de caja (caja_adicion)
        Schema::create('conciliation_cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_execution_id')->constrained()->onDelete('cascade');

            $table->date('fecha_contable')->nullable();
            $table->dateTime('fecha_modificacion')->nullable();
            $table->string('origen', 50)->nullable();
            $table->string('clase', 100)->nullable();
            $table->string('proveedor_para', 200)->nullable();
            $table->decimal('monto', 15, 2)->default(0);
            $table->text('comentario')->nullable();
            $table->string('usuario', 200)->nullable();
            $table->string('tipo', 20)->nullable(); // Ingreso/Egreso
            $table->string('forma_pago', 50)->nullable();
            $table->string('cuenta_contable', 100)->nullable();
            $table->string('turno', 20)->nullable();

            // Hash único para identificar el movimiento
            $table->string('unique_hash', 64)->nullable();

            $table->timestamps();

            // Índices
            $table->index(['workflow_execution_id', 'fecha_contable']);
            $table->index('tipo');
            $table->index('turno');

            // Clave única basada en hash
            $table->unique('unique_hash', 'unique_cash_movement');
        });

        // 6. Turnos procesados (turnos_procesados)
        Schema::create('conciliation_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_execution_id')->constrained()->onDelete('cascade');

            $table->date('fecha_apertura')->nullable();
            $table->time('hora_apertura')->nullable();
            $table->date('fecha_cierre')->nullable();
            $table->time('hora_cierre')->nullable();
            $table->string('turno', 20)->nullable();
            $table->string('encargado', 100)->nullable();
            $table->integer('cantidad_comensales')->default(0);
            $table->decimal('recuento_efectivo', 15, 2)->default(0);
            $table->decimal('apertura_caja', 15, 2)->default(0);

            $table->timestamps();

            // Índices
            $table->index(['workflow_execution_id', 'fecha_apertura']);
            $table->index('turno');

            // Clave única - turno único por fecha/hora
            $table->unique(['fecha_apertura', 'hora_apertura', 'turno'], 'unique_shift');
        });

        // 7. Devoluciones (devoluciones)
        Schema::create('conciliation_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_execution_id')->constrained()->onDelete('cascade');

            $table->string('producto', 200)->nullable();
            $table->decimal('precio', 12, 2)->default(0);
            $table->text('comentario')->nullable();
            $table->dateTime('fecha_hora_pedido')->nullable();
            $table->time('hora_pedido')->nullable();
            $table->string('hora_anulacion', 50)->nullable();
            $table->string('turno', 20)->nullable();

            // Hash único para identificar la devolución
            $table->string('unique_hash', 64)->nullable();

            $table->timestamps();

            // Índices
            $table->index(['workflow_execution_id']);
            $table->index('turno');

            // Clave única basada en hash
            $table->unique('unique_hash', 'unique_refund');
        });

        // 8. Movimientos negativos MP (mp_negativos)
        Schema::create('conciliation_mp_negatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_execution_id')->constrained()->onDelete('cascade');

            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
            $table->string('id_operacion_mp', 50)->nullable();
            $table->decimal('monto_neto', 15, 2)->default(0);
            $table->string('turno', 20)->nullable();

            $table->timestamps();

            // Índices
            $table->index(['workflow_execution_id', 'fecha']);

            // Clave única - id_operacion_mp es único
            $table->unique('id_operacion_mp', 'unique_mp_negative');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conciliation_mp_negatives');
        Schema::dropIfExists('conciliation_refunds');
        Schema::dropIfExists('conciliation_shifts');
        Schema::dropIfExists('conciliation_cash_movements');
        Schema::dropIfExists('conciliation_system_sales');
        Schema::dropIfExists('conciliation_mp_transactions');
        Schema::dropIfExists('conciliation_getnet_transactions');
        Schema::dropIfExists('conciliation_summaries');
    }
};
