<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Conciliación - {{ $batch->batch_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #1F3864;
            line-height: 1.3;
        }
        
        .page {
            page-break-after: always;
            padding: 20px;
        }
        
        .page:last-child {
            page-break-after: auto;
        }
        
        /* Header */
        .header {
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 2px solid #1F3864;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #1F3864;
            margin-bottom: 5px;
        }
        
        .header .subtitle {
            font-size: 12px;
            color: #1F3864;
        }
        
        .info-box {
            margin: 15px 0;
            padding: 10px;
            background-color: #E7E6E6;
            border: 1px solid #1F3864;
        }
        
        .info-row {
            margin: 4px 0;
            font-size: 10px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
            color: #1F3864;
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        table th {
            background-color: #E7E6E6;
            color: #1F3864;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #1F3864;
        }
        
        table td {
            padding: 6px 8px;
            border: 1px solid #1F3864;
            font-size: 9px;
            background-color: white;
        }
        
        /* Two column layout for sheet 3 */
        .two-columns {
            display: table;
            width: 100%;
        }
        
        .column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        
        .column:first-child {
            padding-right: 2%;
        }
        
        .column h3 {
            font-size: 11px;
            margin-bottom: 8px;
            padding: 6px;
            background-color: #E7E6E6;
            color: #1F3864;
            border: 1px solid #1F3864;
            font-weight: bold;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding: 8px;
            background-color: #E7E6E6;
            color: #1F3864;
            border-left: 4px solid #1F3864;
        }
        
        .footer {
            position: fixed;
            bottom: 10px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 8px;
            color: #1F3864;
            border-top: 1px solid #1F3864;
            padding-top: 5px;
        }
        
        @page {
            margin: 15mm;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    @foreach($sheets as $sheetIndex => $sheet)
        <div class="page">
            {{-- Header --}}
            <div class="header">
                <h1>REPORTE DE CONCILIACIÓN</h1>
                <div class="subtitle">{{ strtoupper($sheet['name']) }}</div>
            </div>
            
            {{-- Info Box --}}
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Cliente:</span>
                    <span>{{ $client->company }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Sucursal:</span>
                    <span>{{ $branch->branch_name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha:</span>
                    <span>{{ $generated_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Código Batch:</span>
                    <span>{{ $batch->batch_code }}</span>
                </div>
            </div>
            
            {{-- Sheet Content --}}
            @if(!empty($sheet['data']))
                @if($sheetIndex === 2)
                    {{-- Sheet 3: Two columns side by side --}}
                    <div class="section-title">Comparación de Ventas</div>
                    <div class="two-columns">
                        <div class="column">
                            <h3>Ventas en Sistema</h3>
                            <table>
                                <thead>
                                    <tr>
                                        @if(isset($sheet['data'][0]))
                                            @foreach(array_slice($sheet['data'][0], 0, 3) as $cell)
                                                <th>{{ $cell ?? '' }}</th>
                                            @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($sheet['data'], 1, 15) as $row)
                                        <tr>
                                            @foreach(array_slice($row, 0, 3) as $cell)
                                                <td>{{ $cell ?? '' }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="column">
                            <h3>Ventas en Getnet</h3>
                            <table>
                                <thead>
                                    <tr>
                                        @if(isset($sheet['data'][0]))
                                            @foreach(array_slice($sheet['data'][0], 3, 3) as $cell)
                                                <th>{{ $cell ?? '' }}</th>
                                            @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($sheet['data'], 1, 15) as $row)
                                        <tr>
                                            @foreach(array_slice($row, 3, 3) as $cell)
                                                <td>{{ $cell ?? '' }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    {{-- Regular table for other sheets --}}
                    <div class="section-title">{{ $sheet['name'] }}</div>
                    <table>
                        <thead>
                            <tr>
                                @if(isset($sheet['data'][0]))
                                    @foreach($sheet['data'][0] as $cell)
                                        <th>{{ $cell ?? '' }}</th>
                                    @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($sheet['data'], 1, 30) as $row)
                                <tr>
                                    @foreach($row as $cell)
                                        <td>{{ $cell ?? '' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @else
                <p style="text-align: center; color: #999; padding: 20px;">Sin datos disponibles</p>
            @endif
        </div>
    @endforeach
    
    {{-- Footer --}}
    <div class="footer">
        Generado por {{ $user->name }} el {{ $generated_at->format('d/m/Y H:i:s') }} | 
        Sistema de Workflows
    </div>
</body>
</html>
