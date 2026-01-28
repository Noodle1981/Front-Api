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
        // Add client_id to all conciliation tables
        $tables = [
            'conciliation_summaries',
            'conciliation_getnet_transactions',
            'conciliation_mp_transactions',
            'conciliation_system_sales',
            'conciliation_cash_movements',
            'conciliation_shifts',
            'conciliation_refunds',
            'conciliation_mp_negatives',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->foreignId('client_id')->nullable()->after('workflow_execution_id')->constrained('clients')->onDelete('cascade');
                $t->index('client_id');
            });
        }

        // Drop existing unique constraints and recreate with client_id

        // 1. conciliation_summaries: unique on fecha, turno, encargado -> add client_id
        Schema::table('conciliation_summaries', function (Blueprint $table) {
            $table->dropUnique('unique_summary_turno');
        });
        Schema::table('conciliation_summaries', function (Blueprint $table) {
            $table->unique(['client_id', 'fecha', 'turno', 'encargado'], 'unique_summary_turno');
        });

        // 2. conciliation_getnet_transactions: cod_transaccion -> add client_id
        Schema::table('conciliation_getnet_transactions', function (Blueprint $table) {
            $table->dropUnique('unique_getnet_transaction');
        });
        Schema::table('conciliation_getnet_transactions', function (Blueprint $table) {
            $table->unique(['client_id', 'cod_transaccion'], 'unique_getnet_transaction');
        });

        // 3. conciliation_mp_transactions: id_operacion_mp -> add client_id
        Schema::table('conciliation_mp_transactions', function (Blueprint $table) {
            $table->dropUnique('unique_mp_transaction');
        });
        Schema::table('conciliation_mp_transactions', function (Blueprint $table) {
            $table->unique(['client_id', 'id_operacion_mp'], 'unique_mp_transaction');
        });

        // 4. conciliation_system_sales: id_ticket, source -> add client_id
        Schema::table('conciliation_system_sales', function (Blueprint $table) {
            $table->dropUnique('unique_system_sale');
        });
        Schema::table('conciliation_system_sales', function (Blueprint $table) {
            $table->unique(['client_id', 'id_ticket', 'source'], 'unique_system_sale');
        });

        // 5. conciliation_cash_movements: unique_hash -> add client_id
        Schema::table('conciliation_cash_movements', function (Blueprint $table) {
            $table->dropUnique('unique_cash_movement');
        });
        Schema::table('conciliation_cash_movements', function (Blueprint $table) {
            $table->unique(['client_id', 'unique_hash'], 'unique_cash_movement');
        });

        // 6. conciliation_shifts: fecha_apertura, hora_apertura, turno -> add client_id
        Schema::table('conciliation_shifts', function (Blueprint $table) {
            $table->dropUnique('unique_shift');
        });
        Schema::table('conciliation_shifts', function (Blueprint $table) {
            $table->unique(['client_id', 'fecha_apertura', 'hora_apertura', 'turno'], 'unique_shift');
        });

        // 7. conciliation_refunds: unique_hash -> add client_id
        Schema::table('conciliation_refunds', function (Blueprint $table) {
            $table->dropUnique('unique_refund');
        });
        Schema::table('conciliation_refunds', function (Blueprint $table) {
            $table->unique(['client_id', 'unique_hash'], 'unique_refund');
        });

        // 8. conciliation_mp_negatives: id_operacion_mp -> add client_id
        Schema::table('conciliation_mp_negatives', function (Blueprint $table) {
            $table->dropUnique('unique_mp_negative');
        });
        Schema::table('conciliation_mp_negatives', function (Blueprint $table) {
            $table->unique(['client_id', 'id_operacion_mp'], 'unique_mp_negative');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore original unique constraints
        Schema::table('conciliation_summaries', function (Blueprint $table) {
            $table->dropUnique('unique_summary_turno');
        });
        Schema::table('conciliation_summaries', function (Blueprint $table) {
            $table->unique(['fecha', 'turno', 'encargado'], 'unique_summary_turno');
        });

        Schema::table('conciliation_getnet_transactions', function (Blueprint $table) {
            $table->dropUnique('unique_getnet_transaction');
        });
        Schema::table('conciliation_getnet_transactions', function (Blueprint $table) {
            $table->unique('cod_transaccion', 'unique_getnet_transaction');
        });

        Schema::table('conciliation_mp_transactions', function (Blueprint $table) {
            $table->dropUnique('unique_mp_transaction');
        });
        Schema::table('conciliation_mp_transactions', function (Blueprint $table) {
            $table->unique('id_operacion_mp', 'unique_mp_transaction');
        });

        Schema::table('conciliation_system_sales', function (Blueprint $table) {
            $table->dropUnique('unique_system_sale');
        });
        Schema::table('conciliation_system_sales', function (Blueprint $table) {
            $table->unique(['id_ticket', 'source'], 'unique_system_sale');
        });

        Schema::table('conciliation_cash_movements', function (Blueprint $table) {
            $table->dropUnique('unique_cash_movement');
        });
        Schema::table('conciliation_cash_movements', function (Blueprint $table) {
            $table->unique('unique_hash', 'unique_cash_movement');
        });

        Schema::table('conciliation_shifts', function (Blueprint $table) {
            $table->dropUnique('unique_shift');
        });
        Schema::table('conciliation_shifts', function (Blueprint $table) {
            $table->unique(['fecha_apertura', 'hora_apertura', 'turno'], 'unique_shift');
        });

        Schema::table('conciliation_refunds', function (Blueprint $table) {
            $table->dropUnique('unique_refund');
        });
        Schema::table('conciliation_refunds', function (Blueprint $table) {
            $table->unique('unique_hash', 'unique_refund');
        });

        Schema::table('conciliation_mp_negatives', function (Blueprint $table) {
            $table->dropUnique('unique_mp_negative');
        });
        Schema::table('conciliation_mp_negatives', function (Blueprint $table) {
            $table->unique('id_operacion_mp', 'unique_mp_negative');
        });

        // Drop client_id from all tables
        $tables = [
            'conciliation_summaries',
            'conciliation_getnet_transactions',
            'conciliation_mp_transactions',
            'conciliation_system_sales',
            'conciliation_cash_movements',
            'conciliation_shifts',
            'conciliation_refunds',
            'conciliation_mp_negatives',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropForeign(['client_id']);
                $t->dropIndex(['client_id']);
                $t->dropColumn('client_id');
            });
        }
    }
};
