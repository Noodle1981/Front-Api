<?php

namespace Database\Seeders;

use App\Models\WorkflowType;
use App\Models\WorkflowFileDefinition;
use App\Models\WorkflowRequiredColumn;
use Illuminate\Database\Seeder;

class WorkflowTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear workflow "Conciliación"
        $conciliacion = WorkflowType::create([
            'name' => 'Conciliación',
            'code' => 'conciliacion',
            'description' => 'Workflow para procesar archivos de conciliación de datos de ventas',
            'is_active' => true,
            'expected_files_count' => 6,
        ]);

        // Definir los 6 archivos requeridos con sus columnas reales
        $archivos = [
            [
                'file_identifier' => 'Turnos',
                'display_name' => 'Turnos',
                'description' => 'Archivo de turnos de caja',
                'order' => 1,
                'columnas' => [
                    ['column_name' => 'fecha', 'column_type' => 'date', 'is_required' => true],
                    ['column_name' => 'fecha_apertura', 'column_type' => 'date', 'is_required' => true],
                    ['column_name' => 'hs_ap_caja', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'turno', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'encargado', 'column_type' => 'string', 'is_required' => true],
                ],
            ],
            [
                'file_identifier' => 'Reporte_Ventas',
                'display_name' => 'Reporte Ventas',
                'description' => 'Reporte de ventas del día',
                'order' => 2,
                'columnas' => [
                    ['column_name' => 'FechaCierre', 'column_type' => 'date', 'is_required' => true],
                    ['column_name' => 'Comanda', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'Total', 'column_type' => 'decimal', 'is_required' => true],
                    ['column_name' => 'Propina', 'column_type' => 'decimal', 'is_required' => false],
                    ['column_name' => 'Pagos', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'Boleta', 'column_type' => 'string', 'is_required' => false],
                    ['column_name' => 'Efectivo', 'column_type' => 'decimal', 'is_required' => true],
                    ['column_name' => 'Getnet', 'column_type' => 'decimal', 'is_required' => true],
                    ['column_name' => 'Mercado Pago', 'column_type' => 'decimal', 'is_required' => true],
                    ['column_name' => 'Cta Cte', 'column_type' => 'decimal', 'is_required' => false],
                ],
            ],
            [
                'file_identifier' => 'Reporte_getnet',
                'display_name' => 'Reporte getnet',
                'description' => 'Reporte de transacciones Getnet',
                'order' => 3,
                'columnas' => [
                    ['column_name' => 'nro_de_establecimiento', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'fecha_de_operacion', 'column_type' => 'date', 'is_required' => true],
                    ['column_name' => 'monto_bruto_transaccion', 'column_type' => 'decimal', 'is_required' => true],
                    ['column_name' => 'cod_aut', 'column_type' => 'string', 'is_required' => false],
                ],
            ],
            [
                'file_identifier' => 'Prueba_MP',
                'display_name' => 'Ventas MP',
                'description' => 'Ventas de Mercado Pago',
                'order' => 4,
                'columnas' => [
                    ['column_name' => 'numero_de_identificacion', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'fecha_de_origen', 'column_type' => 'date', 'is_required' => true],
                    ['column_name' => 'valor_de_la_compra', 'column_type' => 'decimal', 'is_required' => true],
                    ['column_name' => 'monto_neto_de_la_operacion', 'column_type' => 'decimal', 'is_required' => true],
                ],
            ],
            [
                'file_identifier' => 'Devoluciones',
                'display_name' => 'Devoluciones',
                'description' => 'Registro de devoluciones',
                'order' => 5,
                'columnas' => [
                    ['column_name' => 'id_comanda', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'mesa', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'producto', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'precios', 'column_type' => 'decimal', 'is_required' => true],
                ],
            ],
            [
                'file_identifier' => 'Caja_Adicion',
                'display_name' => 'Caja Adicion',
                'description' => 'Adiciones y retiros de caja',
                'order' => 6,
                'columnas' => [
                    ['column_name' => 'fecha_contable', 'column_type' => 'date', 'is_required' => true],
                    ['column_name' => 'origen', 'column_type' => 'string', 'is_required' => false],
                    ['column_name' => 'clase', 'column_type' => 'string', 'is_required' => false],
                    ['column_name' => 'proveedor_para', 'column_type' => 'string', 'is_required' => false],
                    ['column_name' => 'monto', 'column_type' => 'decimal', 'is_required' => true],
                ],
            ],
        ];

        // Crear las definiciones de archivos y sus columnas
        foreach ($archivos as $archivoData) {
            $columnas = $archivoData['columnas'];
            unset($archivoData['columnas']);

            $fileDefinition = WorkflowFileDefinition::create([
                'workflow_type_id' => $conciliacion->id,
                ...$archivoData,
            ]);

            // Crear las columnas requeridas para este archivo
            foreach ($columnas as $columna) {
                WorkflowRequiredColumn::create([
                    'workflow_file_definition_id' => $fileDefinition->id,
                    ...$columna,
                ]);
            }
        }

        $this->command->info('✅ Workflow "Conciliación" creado con 6 archivos y sus columnas requeridas');
    }
}
