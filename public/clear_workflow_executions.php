<?php

/**
 * Script para borrar todos los datos de ejecución de workflows
 *
 * USO: Acceder desde el navegador con el token de confirmación
 * URL: https://tu-dominio.com/clear_workflow_executions.php?token=BORRAR_WORKFLOW_DATA_2026
 *
 * ADVERTENCIA: Este script elimina TODOS los datos de ejecución de workflows.
 * ELIMINAR ESTE ARCHIVO DESPUÉS DE USAR.
 */

// Token de seguridad - cambiar antes de subir a producción
define('SECRET_TOKEN', 'BORRAR_WORKFLOW_DATA_2026');

// Verificar token antes de hacer cualquier cosa
$token = $_GET['token'] ?? '';
if ($token !== SECRET_TOKEN) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: text/plain; charset=utf-8');
    echo "Acceso denegado. Token inválido.";
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

header('Content-Type: text/plain; charset=utf-8');

echo "=== BORRADO DE DATOS DE WORKFLOWS ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";

// Mostrar conteo antes del borrado
$tables = [
    'workflow_executions',
    'workflow_file_batches',
    'workflow_uploaded_files',
    'conciliation_summaries',
    'conciliation_getnet_transactions',
    'conciliation_mp_transactions',
    'conciliation_system_sales',
    'conciliation_cash_movements',
    'conciliation_shifts',
    'conciliation_refunds',
    'conciliation_mp_negatives',
];

echo "REGISTROS ANTES DEL BORRADO:\n";
echo str_repeat('-', 50) . "\n";

$total = 0;
foreach ($tables as $table) {
    $count = DB::table($table)->count();
    $total += $count;
    echo sprintf("%-40s %d\n", $table, $count);
}
echo str_repeat('-', 50) . "\n";
echo sprintf("%-40s %d\n", "TOTAL", $total);
echo "\n";

if ($total === 0) {
    echo "No hay datos para borrar.\n";
    exit;
}

echo "EJECUTANDO BORRADO...\n\n";

try {
    DB::beginTransaction();

    // Borrar en orden correcto (respetando foreign keys)
    $deleted = DB::table('conciliation_mp_negatives')->delete();
    echo "conciliation_mp_negatives: $deleted borrados\n";

    $deleted = DB::table('conciliation_refunds')->delete();
    echo "conciliation_refunds: $deleted borrados\n";

    $deleted = DB::table('conciliation_shifts')->delete();
    echo "conciliation_shifts: $deleted borrados\n";

    $deleted = DB::table('conciliation_cash_movements')->delete();
    echo "conciliation_cash_movements: $deleted borrados\n";

    $deleted = DB::table('conciliation_system_sales')->delete();
    echo "conciliation_system_sales: $deleted borrados\n";

    $deleted = DB::table('conciliation_mp_transactions')->delete();
    echo "conciliation_mp_transactions: $deleted borrados\n";

    $deleted = DB::table('conciliation_getnet_transactions')->delete();
    echo "conciliation_getnet_transactions: $deleted borrados\n";

    $deleted = DB::table('conciliation_summaries')->delete();
    echo "conciliation_summaries: $deleted borrados\n";

    $deleted = DB::table('workflow_uploaded_files')->delete();
    echo "workflow_uploaded_files: $deleted borrados\n";

    $deleted = DB::table('workflow_executions')->delete();
    echo "workflow_executions: $deleted borrados\n";

    $deleted = DB::table('workflow_file_batches')->delete();
    echo "workflow_file_batches: $deleted borrados\n";

    DB::commit();

    echo "\n[OK] Base de datos limpiada exitosamente.\n";

    // Borrar archivos físicos
    $path = 'workflows/executions';
    if (Storage::disk('public')->exists($path)) {
        Storage::disk('public')->deleteDirectory($path);
        echo "[OK] Directorio de archivos borrado.\n";
    } else {
        echo "[INFO] No hay directorio de archivos para borrar.\n";
    }

    echo "\n=== OPERACION COMPLETADA ===\n";
    echo "\n[!] IMPORTANTE: Elimina este archivo del servidor ahora.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n[ERROR] " . $e->getMessage() . "\n";
    echo "\nLos cambios han sido revertidos.\n";
}
