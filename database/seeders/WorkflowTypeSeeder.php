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
            'description' => 'Workflow para procesar archivos de conciliación de datos',
            'is_active' => true,
            'expected_files_count' => 6,
        ]);

        // Definir los 6 archivos requeridos
        $archivos = [
            [
                'file_identifier' => 'archivo_1',
                'display_name' => 'Archivo Principal',
                'description' => 'Archivo principal de datos',
                'order' => 1,
                'columnas' => [
                    ['column_name' => 'id', 'column_type' => 'integer', 'is_required' => true],
                    ['column_name' => 'nombre', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'fecha', 'column_type' => 'date', 'is_required' => true],
                    ['column_name' => 'monto', 'column_type' => 'decimal', 'is_required' => true],
                ],
            ],
            [
                'file_identifier' => 'archivo_2',
                'display_name' => 'Archivo de Detalle',
                'description' => 'Archivo con detalles complementarios',
                'order' => 2,
                'columnas' => [
                    ['column_name' => 'id_principal', 'column_type' => 'integer', 'is_required' => true],
                    ['column_name' => 'descripcion', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'cantidad', 'column_type' => 'integer', 'is_required' => true],
                ],
            ],
            [
                'file_identifier' => 'archivo_3',
                'display_name' => 'Archivo de Validación',
                'description' => 'Archivo para validación de datos',
                'order' => 3,
                'columnas' => [
                    ['column_name' => 'codigo', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'estado', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'validado', 'column_type' => 'boolean', 'is_required' => false],
                ],
            ],
            [
                'file_identifier' => 'archivo_4',
                'display_name' => 'Archivo de Referencia',
                'description' => 'Archivo de datos de referencia',
                'order' => 4,
                'columnas' => [
                    ['column_name' => 'referencia_id', 'column_type' => 'integer', 'is_required' => true],
                    ['column_name' => 'tipo', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'valor', 'column_type' => 'string', 'is_required' => true],
                ],
            ],
            [
                'file_identifier' => 'archivo_5',
                'display_name' => 'Archivo de Configuración',
                'description' => 'Archivo con parámetros de configuración',
                'order' => 5,
                'columnas' => [
                    ['column_name' => 'parametro', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'valor', 'column_type' => 'string', 'is_required' => true],
                ],
            ],
            [
                'file_identifier' => 'archivo_6',
                'display_name' => 'Archivo de Resumen',
                'description' => 'Archivo con datos de resumen',
                'order' => 6,
                'columnas' => [
                    ['column_name' => 'periodo', 'column_type' => 'string', 'is_required' => true],
                    ['column_name' => 'total_registros', 'column_type' => 'integer', 'is_required' => true],
                    ['column_name' => 'total_monto', 'column_type' => 'decimal', 'is_required' => true],
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
