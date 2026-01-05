<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Conciliación</title>
    <style>
        @page {
            margin: 15mm;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #000;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .header-cell {
            background-color: #bdbdbd;
            color: #1f3864;
            font-weight: bold;
            text-align: center;
            padding: 8px 4px;
            font-size: 10pt;
            border: 1px solid #999;
        }
        
        .header-cell-dark {
            background-color: #1f3864;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            padding: 8px 4px;
            font-size: 10pt;
            border: 1px solid #000;
        }
        
        .title-large {
            background-color: #bdbdbd;
            color: #1f3864;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            font-size: 20pt;
            border: 1px solid #999;
        }
        
        .title-medium {
            background-color: #bdbdbd;
            color: #1f3864;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            font-size: 16pt;
            border: 1px solid #999;
        }
        
        .data-cell {
            background-color: #ffffff;
            padding: 6px 4px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 9pt;
        }
        
        .data-cell-gray {
            background-color: #bdbdbd;
            padding: 6px 4px;
            border: 1px solid #999;
            text-align: center;
            font-size: 9pt;
        }
        
        .section-title {
            background-color: #1f3864;
            color: #ffffff;
            padding: 10px;
            font-size: 14pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .info-label {
            background-color: #bdbdbd;
            color: #1f3864;
            font-weight: bold;
            padding: 8px;
            text-align: center;
            border: 1px solid #999;
        }
        
        .info-value {
            background-color: #1f3864;
            color: #ffffff;
            padding: 8px;
            text-align: center;
            border: 1px solid #000;
        }
        
        .parador-title {
            background-color: #bdbdbd;
            color: #1f3864;
            font-size: 24pt;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border: 1px solid #999;
        }
        
        .facturacion-box {
            background-color: #f8f9fa;
            border: 2px solid #1f3864;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
        }
        
        .facturacion-box div {
            margin: 8px 0;
        }
        
        h1 {
            text-align: center;
            color: #1f3864;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>REPORTE DE CONCILIACIÓN</h1>
    
    <!-- ENVIAR SUCURSAL -->
    <div class="section-title">ENVIAR SUCURSAL</div>
    
    <table>
        <tr>
            <td class="info-label">FECHA</td>
            <td class="info-value">{{ $metadata['fecha'] }}</td>
            <td class="info-label">DIA</td>
            <td class="info-value">{{ $metadata['dia'] }}</td>
            <td class="info-label">TOTAL VENTAS</td>
            <td class="info-value" style="font-size: 14pt;">${{ $data['enviar_sucursal']['total_ventas'] }}</td>
        </tr>
        <tr>
            <td class="info-label">TURNO</td>
            <td class="info-value">{{ $metadata['turno'] }}</td>
            <td class="info-label">ENCARGADO</td>
            <td class="info-value">{{ $metadata['encargado'] }}</td>
            <td colspan="2" rowspan="2" class="parador-title">{{ $metadata['sucursal'] }}</td>
        </tr>
        <tr>
            <td class="info-label">HS APERTURA</td>
            <td class="info-value">{{ $metadata['hs_apertura'] }}</td>
            <td class="info-label">HS CIERRE</td>
            <td class="info-value">{{ $metadata['hs_cierre'] }}</td>
        </tr>
    </table>
    
    <!-- Parador Stats -->
    <table>
        <tr>
            <td class="header-cell">CANTIDAD DE TICKETS</td>
            <td class="header-cell">TICKET PROMEDIO</td>
            <td class="header-cell">CANTIDAD DE COMENSALES</td>
            <td class="header-cell">COMENSALES PROMEDIO</td>
        </tr>
        <tr>
            <td class="data-cell">{{ $data['enviar_sucursal']['parador']['cantidad_tickets'] }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['parador']['ticket_promedio'] }}</td>
            <td class="data-cell">{{ $data['enviar_sucursal']['parador']['cantidad_comensales'] }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['parador']['comensales_promedio'] }}</td>
        </tr>
    </table>
    
    <!-- Diferencias de Caja -->
    <div class="section-title">DIFERENCIAS DE CAJA</div>
    
    <table>
        <tr>
            <td colspan="6" class="title-medium">MERCADO PAGO</td>
        </tr>
        <tr>
            <td class="header-cell">Real</td>
            <td class="header-cell">Real No Conciliado</td>
            <td class="header-cell">Sistema</td>
            <td class="header-cell">Sistema No Conciliado</td>
            <td class="header-cell">Diferencia</td>
            <td class="header-cell">%</td>
        </tr>
        <tr>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['real'] }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['real_no_conciliado'] }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['sistema'] }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['sistema_no_conciliado'] }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['diferencia'] }}</td>
            <td class="data-cell">{{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['porcentaje'] }}%</td>
        </tr>
    </table>
    
    <table>
        <tr>
            <td colspan="6" class="title-medium">GETNET</td>
        </tr>
        <tr>
            <td class="header-cell">Real</td>
            <td class="header-cell">Real No Conciliado</td>
            <td class="header-cell">Sistema</td>
            <td class="header-cell">Sistema No Conciliado</td>
            <td class="header-cell">Diferencia</td>
            <td class="header-cell">%</td>
        </tr>
        <tr>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['real'] }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['real_no_conciliado'] }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['sistema'] }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['sistema_no_conciliado'] }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['diferencia'] }}</td>
            <td class="data-cell">{{ $data['enviar_sucursal']['diferencias_caja']['getnet']['porcentaje'] }}%</td>
        </tr>
    </table>
    
    <!-- Page Break -->
    <div style="page-break-before: always;"></div>
    
    <!-- ENVIAR EGRESOS -->
    <div class="section-title">ENVIAR EGRESOS</div>
    
    <table>
        <tr>
            <td colspan="3" class="title-medium">EGRESOS CAJA ADICIÓN</td>
        </tr>
        <tr>
            <td class="header-cell-dark">IMPORTE</td>
            <td class="header-cell-dark">HORA</td>
            <td class="header-cell-dark">DETALLE</td>
        </tr>
        @foreach($data['enviar_egresos']['caja_adicion'] as $egreso)
        <tr>
            <td class="data-cell-gray">${{ $egreso['importe'] }}</td>
            <td class="data-cell-gray">{{ $egreso['hora'] }}</td>
            <td class="data-cell-gray">{{ $egreso['detalle'] }}</td>
        </tr>
        @endforeach
    </table>
    
    <!-- ENVIAR ANULACIONES -->
    <div class="section-title">ENVIAR ANULACIONES</div>
    
    <table>
        <tr>
            <td class="header-cell-dark">ID Comanda</td>
            <td class="header-cell-dark">Camarero Mesa</td>
            <td class="header-cell-dark">Producto</td>
            <td class="header-cell-dark">Comentario</td>
            <td class="header-cell-dark">Hora</td>
            <td class="header-cell-dark">Precio</td>
        </tr>
        @foreach($data['enviar_anulaciones'] as $anulacion)
        <tr>
            <td class="data-cell-gray">{{ $anulacion['id_comanda'] }}</td>
            <td class="data-cell-gray">{{ $anulacion['camarero_mesa'] }}</td>
            <td class="data-cell-gray">{{ $anulacion['producto'] }}</td>
            <td class="data-cell-gray">{{ $anulacion['comentario'] }}</td>
            <td class="data-cell-gray">{{ $anulacion['hora_anulacion'] }}</td>
            <td class="data-cell-gray">${{ $anulacion['precio'] }}</td>
        </tr>
        @endforeach
    </table>
    
    <!-- Facturación -->
    <div class="facturacion-box">
        <div style="font-size: 12pt;">
            <strong>FACTURACIÓN REAL:</strong> ${{ $data['enviar_sucursal']['facturacion']['real'] }}
        </div>
        <div style="font-size: 12pt;">
            <strong>FACTURACIÓN IDEAL:</strong> ${{ $data['enviar_sucursal']['facturacion']['ideal'] }}
        </div>
        <div style="font-size: 16pt; color: #dc3545; font-weight: bold; margin-top: 10px;">
            DIFERENCIA: ${{ $data['enviar_sucursal']['facturacion']['diferencia'] }}
        </div>
        <div style="font-size: 14pt; color: #1f3864; font-weight: bold;">
            % DESVÍO: {{ $data['enviar_sucursal']['facturacion']['desvio_porcentaje'] }}%
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 30px; font-size: 8pt; color: #666;">
        Generado el {{ now()->format('d/m/Y H:i:s') }} - Batch: {{ $execution->fileBatch->batch_code }}
    </div>
</body>
</html>
