<?php
/**
 * Script para verificar extensiones PHP requeridas por maatwebsite/excel
 * ELIMINAR ESTE ARCHIVO DESPUÉS DE USAR
 */

$required_extensions = [
    'zip' => 'Requerido para leer/escribir archivos .xlsx',
    'gd' => 'Requerido para procesamiento de imágenes en Excel',
    'xml' => 'Requerido para leer estructura XML de archivos Excel',
    'fileinfo' => 'Requerido para detección de tipo MIME',
    'simplexml' => 'Requerido para parsear archivos Excel',
    'xmlwriter' => 'Requerido para escribir archivos Excel',
    'dom' => 'Requerido para manipulación de documentos',
    'mbstring' => 'Requerido para manejo de caracteres especiales',
];

echo "<h1>Verificación de Extensiones PHP para maatwebsite/excel</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Extensión</th><th>Estado</th><th>Descripción</th></tr>";

$all_ok = true;
foreach ($required_extensions as $ext => $desc) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? '✅ Instalada' : '❌ FALTANTE';
    $color = $loaded ? 'green' : 'red';

    if (!$loaded) {
        $all_ok = false;
    }

    echo "<tr>";
    echo "<td><strong>$ext</strong></td>";
    echo "<td style='color: $color'>$status</td>";
    echo "<td>$desc</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Información adicional</h2>";
echo "<p><strong>Directorio temporal:</strong> " . sys_get_temp_dir() . "</p>";
echo "<p><strong>Escritura en temp:</strong> " . (is_writable(sys_get_temp_dir()) ? '✅ OK' : '❌ Sin permisos') . "</p>";

// Verificar si Livewire puede escribir en storage
$storagePath = __DIR__ . '/../storage/app/livewire-tmp';
echo "<p><strong>Livewire temp dir:</strong> " . (is_dir($storagePath) ? '✅ Existe' : '⚠️ No existe') . "</p>";

if ($all_ok) {
    echo "<h2 style='color: green'>✅ Todas las extensiones están instaladas</h2>";
    echo "<p>El problema puede estar en otra parte. Revisa storage/logs/laravel.log</p>";
} else {
    echo "<h2 style='color: red'>❌ Faltan extensiones requeridas</h2>";
    echo "<p>Contacta a tu proveedor de hosting para habilitar las extensiones faltantes.</p>";
}

echo "<hr><p style='color: red'><strong>⚠️ IMPORTANTE: Elimina este archivo después de usar por seguridad.</strong></p>";
