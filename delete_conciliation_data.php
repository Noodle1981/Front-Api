<?php

/**
 * Script para eliminar todos los datos de conciliación y ejecuciones de workflows
 * Ejecutar con: php delete_conciliation_data.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Eliminando datos de conciliación y ejecuciones de workflows ===\n\n";

// Mostrar conteo antes
echo "Conteo ANTES de eliminar:\n";
echo "- workflow_executions: " . DB::table('workflow_executions')->count() . "\n";
echo "- conciliation_summaries: " . DB::table('conciliation_summaries')->count() . "\n";
echo "- conciliation_getnet_transactions: " . DB::table('conciliation_getnet_transactions')->count() . "\n";
echo "- conciliation_mp_transactions: " . DB::table('conciliation_mp_transactions')->count() . "\n";
echo "- conciliation_system_sales: " . DB::table('conciliation_system_sales')->count() . "\n";
echo "- conciliation_cash_movements: " . DB::table('conciliation_cash_movements')->count() . "\n";
echo "- conciliation_shifts: " . DB::table('conciliation_shifts')->count() . "\n";
echo "- conciliation_refunds: " . DB::table('conciliation_refunds')->count() . "\n";
echo "- conciliation_mp_negatives: " . DB::table('conciliation_mp_negatives')->count() . "\n";

echo "\nEliminando datos...\n";

// Eliminar en orden para respetar foreign keys
// Primero las tablas de conciliación (tienen FK a workflow_executions)
DB::table('conciliation_mp_negatives')->truncate();
echo "- conciliation_mp_negatives: eliminado\n";

DB::table('conciliation_refunds')->truncate();
echo "- conciliation_refunds: eliminado\n";

DB::table('conciliation_shifts')->truncate();
echo "- conciliation_shifts: eliminado\n";

DB::table('conciliation_cash_movements')->truncate();
echo "- conciliation_cash_movements: eliminado\n";

DB::table('conciliation_system_sales')->truncate();
echo "- conciliation_system_sales: eliminado\n";

DB::table('conciliation_mp_transactions')->truncate();
echo "- conciliation_mp_transactions: eliminado\n";

DB::table('conciliation_getnet_transactions')->truncate();
echo "- conciliation_getnet_transactions: eliminado\n";

DB::table('conciliation_summaries')->truncate();
echo "- conciliation_summaries: eliminado\n";

// Ahora eliminar workflow_executions
DB::table('workflow_executions')->truncate();
echo "- workflow_executions: eliminado\n";

echo "\n=== Eliminación completada ===\n";

// Mostrar conteo después
echo "\nConteo DESPUÉS de eliminar:\n";
echo "- workflow_executions: " . DB::table('workflow_executions')->count() . "\n";
echo "- conciliation_summaries: " . DB::table('conciliation_summaries')->count() . "\n";
