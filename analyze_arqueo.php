<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'd:/Front-Api/docs/Lógica_del_diseño/excels/arqueo.xlsm';
$outputFile = 'd:/Front-Api/arqueo_analysis.txt';

try {
    $spreadsheet = IOFactory::load($filePath);
    
    $output = "=== ANÁLISIS DEL ARCHIVO ARQUEO.XLSM ===" . PHP_EOL . PHP_EOL;
    $output .= "Número total de hojas: " . $spreadsheet->getSheetCount() . PHP_EOL . PHP_EOL;
    
    foreach ($spreadsheet->getAllSheets() as $index => $sheet) {
        $output .= str_repeat("=", 80) . PHP_EOL;
        $output .= "HOJA " . ($index + 1) . ": " . $sheet->getTitle() . PHP_EOL;
        $output .= str_repeat("=", 80) . PHP_EOL;
        
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $output .= "Dimensión: $highestRow filas x $highestColumn columnas" . PHP_EOL . PHP_EOL;
        
        // Mostrar primeras 20 filas para entender la estructura
        $maxRowsToShow = min(20, $highestRow);
        
        $output .= "Primeras $maxRowsToShow filas:" . PHP_EOL;
        $output .= str_repeat("-", 80) . PHP_EOL;
        
        for ($row = 1; $row <= $maxRowsToShow; $row++) {
            $rowData = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cell = $sheet->getCell($col . $row);
                $value = $cell->getValue();
                
                if ($value !== null && $value !== '') {
                    // Truncar valores muy largos
                    $displayValue = is_string($value) ? substr($value, 0, 50) : $value;
                    $rowData[] = "$col: $displayValue";
                }
            }
            
            if (!empty($rowData)) {
                $output .= "Fila $row: " . implode(' | ', $rowData) . PHP_EOL;
            }
        }
        
        $output .= PHP_EOL . PHP_EOL;
    }
    
    // Guardar en archivo
    file_put_contents($outputFile, $output);
    
    echo "✓ Análisis completado!" . PHP_EOL;
    echo "✓ Archivo guardado en: $outputFile" . PHP_EOL;
    echo "✓ Total de hojas: " . $spreadsheet->getSheetCount() . PHP_EOL;
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . PHP_EOL;
    file_put_contents($outputFile, "Error: " . $e->getMessage());
}
