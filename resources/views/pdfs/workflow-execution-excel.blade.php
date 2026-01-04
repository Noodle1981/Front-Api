<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Workflow - {{ $batch->batch_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
        }
        
        .header {
            background-color: #4a5568;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .header-info {
            font-size: 9px;
            margin-top: 10px;
        }
        
        .header-info div {
            margin: 3px 0;
        }
        
        .sheet-container {
            page-break-after: always;
            padding: 10px;
        }
        
        .sheet-container:last-child {
            page-break-after: auto;
        }
        
        .sheet-title {
            background-color: #2d3748;
            color: white;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th {
            background-color: #4299e1;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #2b6cb0;
        }
        
        table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e0;
            font-size: 9px;
        }
        
        table tr:nth-child(even) {
            background-color: #f7fafc;
        }
        
        table tr:nth-child(odd) {
            background-color: white;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #718096;
            font-style: italic;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #718096;
            padding: 10px;
            border-top: 1px solid #e2e8f0;
        }
        
        @page {
            margin: 15mm;
        }
    </style>
</head>
<body>
    {{-- Header (solo en primera página) --}}
    <div class="header">
        <h1>REPORTE DE WORKFLOW - {{ strtoupper($workflow_type->name) }}</h1>
        <div class="header-info">
            <div><strong>Código:</strong> {{ $batch->batch_code }}</div>
            <div><strong>Cliente:</strong> {{ $client->company }}</div>
            <div><strong>Sucursal:</strong> {{ $branch->branch_name ?? 'N/A' }}</div>
            <div><strong>Fecha de Ejecución:</strong> {{ $generated_at->format('d/m/Y H:i:s') }}</div>
            <div><strong>Usuario:</strong> {{ $user->name }}</div>
        </div>
    </div>

    {{-- Sheets --}}
    @foreach($sheets as $index => $sheet)
        <div class="sheet-container">
            <div class="sheet-title">{{ $sheet['name'] }}</div>
            
            @if(!empty($sheet['data']))
                <table>
                    @foreach($sheet['data'] as $rowIndex => $row)
                        @if($rowIndex === 0)
                            {{-- Primera fila como header --}}
                            <thead>
                                <tr>
                                    @foreach($row as $cell)
                                        <th>{{ $cell ?? '' }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                        @else
                            <tr>
                                @foreach($row as $cell)
                                    <td>{{ $cell ?? '' }}</td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">Sin datos disponibles</div>
            @endif
        </div>
    @endforeach

    {{-- Footer --}}
    <div class="footer">
        Generado por {{ $user->name }} el {{ $generated_at->format('d/m/Y H:i:s') }} | 
        Sistema de Workflows - {{ config('app.name') }}
    </div>
</body>
</html>
