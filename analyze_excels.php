<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$excelDir = __DIR__ . '/docs/Lógica_del_diseño/excels/';
$files = [
    'Turnos.xlsx',
    'Reporte Ventas.xlsx',
    'Reporte getnet.xlsx',
    'Ventas MP.xlsx',
    'Devoluciones.xlsx',
    'Caja Adicion.xlsx'
];

$results = [];

foreach ($files as $file) {
    $filePath = $excelDir . $file;
    
    if (!file_exists($filePath)) {
        echo "❌ Archivo no encontrado: $file\n";
        continue;
    }
    
    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Leer primera fila (headers)
        $headers = [];
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellValue = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
            if ($cellValue !== null && trim($cellValue) !== '') {
                $headers[] = trim($cellValue);
            }
        }
        
        // Contar filas con datos
        $highestRow = $worksheet->getHighestRow();
        
        $results[$file] = [
            'columns' => $headers,
            'column_count' => count($headers),
            'row_count' => $highestRow - 1, // Excluyendo header
        ];
        
        echo "✅ $file\n";
        echo "   Columnas: " . count($headers) . "\n";
        echo "   Filas de datos: " . ($highestRow - 1) . "\n";
        echo "   Headers: " . implode(', ', $headers) . "\n\n";
        
    } catch (Exception $e) {
        echo "❌ Error leyendo $file: " . $e->getMessage() . "\n\n";
    }
}

// Generar JSON para el seeder
echo "\n\n=== JSON PARA SEEDER ===\n\n";
echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
