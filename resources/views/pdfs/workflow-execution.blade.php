<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ejecución de Workflow</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            background-color: #f0f0f0;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            border-left: 4px solid #667eea;
            margin-bottom: 10px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 10px;
            width: 30%;
            background-color: #f9f9f9;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px 10px;
            width: 70%;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table th {
            background-color: #667eea;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        
        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .results-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-top: 10px;
        }
        
        .results-box h4 {
            margin-bottom: 10px;
            color: #667eea;
        }
        
        .stat-grid {
            display: table;
            width: 100%;
        }
        
        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #667eea;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        .error-list {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin-top: 10px;
        }
        
        .error-list ul {
            margin-left: 20px;
        }
        
        .error-list li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Ejecución de Workflow</h1>
        <p>{{ $workflow_type->name }}</p>
    </div>

    <!-- Información General -->
    <div class="section">
        <div class="section-title">Información General</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Código de Batch:</div>
                <div class="info-value">{{ $batch->batch_code }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tipo de Workflow:</div>
                <div class="info-value">{{ $workflow_type->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Cliente:</div>
                <div class="info-value">{{ $client->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Sede:</div>
                <div class="info-value">{{ $branch->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Ejecución:</div>
                <div class="info-value">{{ $execution->started_at->format('d/m/Y H:i:s') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Usuario:</div>
                <div class="info-value">{{ $user->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Estado:</div>
                <div class="info-value">
                    <span class="status-badge {{ $is_success ? 'status-success' : 'status-failed' }}">
                        {{ $is_success ? 'EXITOSO' : 'FALLIDO' }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Tiempo de Ejecución:</div>
                <div class="info-value">{{ $execution->execution_time_ms }} ms</div>
            </div>
        </div>
    </div>

    <!-- Archivos Procesados -->
    <div class="section">
        <div class="section-title">Archivos Procesados</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tipo de Archivo</th>
                    <th>Nombre Original</th>
                    <th>Tamaño</th>
                    <th>Filas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $index => $file)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $file->fileDefinition->display_name }}</td>
                    <td>{{ $file->original_filename }}</td>
                    <td>{{ number_format($file->file_size / 1024, 2) }} KB</td>
                    <td>{{ $file->rows_count ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Resultados de Ejecución -->
    <div class="section">
        <div class="section-title">Resultados de Ejecución</div>
        
        @if(isset($results['total_records']))
        <div class="stat-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $results['total_records'] ?? 0 }}</div>
                <div class="stat-label">Total Registros</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" style="color: #28a745;">{{ $results['valid_records'] ?? 0 }}</div>
                <div class="stat-label">Registros Válidos</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" style="color: #dc3545;">{{ $results['invalid_records'] ?? 0 }}</div>
                <div class="stat-label">Registros Inválidos</div>
            </div>
        </div>
        @endif

        @if(isset($results['errors']) && count($results['errors']) > 0)
        <div class="error-list">
            <strong>Errores Encontrados:</strong>
            <ul>
                @foreach($results['errors'] as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(isset($results['warnings']) && count($results['warnings']) > 0)
        <div class="error-list" style="background-color: #e7f3ff; border-color: #2196F3;">
            <strong>Advertencias:</strong>
            <ul>
                @foreach($results['warnings'] as $warning)
                <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Documento generado el {{ $generated_at->format('d/m/Y H:i:s') }}</p>
        <p>Sistema de Workflows - {{ config('app.name') }}</p>
    </div>
</body>
</html>
