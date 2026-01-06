<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Arqueo de Caja</title>
    <style>
        @page {
            margin: 15mm;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #000;
        }
        
        .header-bar {
            background: #1F3864;
            color: white;
            padding: 15px 20px;
            margin-bottom: 15px;
        }
        
        .header-bar h1 {
            font-size: 24pt;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .main-info-wrapper {
            background: #BDBDBD;
            padding: 10px;
            margin-bottom: 12px;
        }

        .info-cell-label { background: #1F3864; color: white; font-weight: bold; padding: 8px; text-align: center; font-size: 9pt; }
        .info-cell-value { background: white; color: black; padding: 8px; text-align: center; font-size: 9pt; }
        .total-ventas-label { background: #1F3864; color: white; font-weight: bold; padding: 8px; text-align: center; font-size: 9pt; }
        .total-ventas-value { background: white; color: black; padding: 8px; text-align: center; font-size: 14pt; font-weight: bold; }
        .parador-cell { background: #BDBDBD; color: black; font-size: 20pt; font-weight: bold; text-align: center; padding: 8px; }

        .header-cell { background-color: #bdbdbd; color: #1f3864; font-weight: bold; text-align: center; padding: 8px; font-size: 9pt; border: 1px solid #fff; }
        .header-cell-dark { background-color: #1f3864; color: white; font-weight: bold; text-align: center; padding: 8px; font-size: 9pt; border: 1px solid #fff; }
        .title-medium { background-color: #bdbdbd; color: #1f3864; font-weight: bold; text-align: center; padding: 10px; font-size: 16pt; border: 1px solid #fff; }
        .data-cell { background-color: white; padding: 6px; border: 1px solid #ddd; text-align: center; font-size: 8pt; }
        .data-cell-gray { background-color: #bdbdbd; padding: 6px; border: 1px solid #999; text-align: center; font-size: 8pt; }

        .section-title { background-color: #1f3864; color: white; padding: 10px; font-size: 12pt; font-weight: bold; margin-top: 15px; margin-bottom: 10px; text-align: center; }

        .stats-wrapper {
            background: #BDBDBD;
            padding: 10px;
            margin-bottom: 12px;
        }
        
        .stat-cell-label { background: #1F3864; color: white; font-weight: bold; padding: 6px; text-align: center; font-size: 8pt; }
        .stat-cell-value { background: white; color: black; padding: 8px; text-align: center; font-size: 12pt; font-weight: bold; }

        .timeline-wrapper {
            background: #BDBDBD;
            padding: 10px;
            margin-bottom: 12px;
        }
        
        .timeline-label { background: #1F3864; color: white; padding: 5px; font-size: 8pt; font-weight: bold; text-align: center; }
        .timeline-value { background: white; color: black; padding: 6px; font-size: 10pt; font-weight: bold; text-align: center; }
        
        .facturacion-box {
            background: #f8f9fa;
            border: 2px solid #1f3864;
            padding: 12px;
            text-align: center;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header-bar">
        <h1>ARQUEO DE CAJA</h1>
    </div>

    <!-- Main Info Table -->
    <div class="main-info-wrapper">
        <table>
            <tr>
                <td class="info-cell-label">FECHA</td>
                <td class="info-cell-value">{{ $metadata['fecha'] }}</td>
                <td class="info-cell-label">DIA</td>
                <td class="info-cell-value">{{ $metadata['dia'] }}</td>
                <td rowspan="2" class="total-ventas-label">TOTAL VENTAS</td>
                <td rowspan="2" class="total-ventas-value">${{ $data['enviar_sucursal']['total_ventas'] }}</td>
            </tr>
            <tr>
                <td class="info-cell-label">TURNO</td>
                <td class="info-cell-value">{{ $metadata['turno'] }}</td>
                <td class="info-cell-label">ENCARGADO</td>
                <td class="info-cell-value">{{ $metadata['encargado'] }}</td>
            </tr>
            <tr>
                <td colspan="6" class="parador-cell">{{ $metadata['sucursal'] }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Parador Stats -->
    <div class="stats-wrapper">
        <table>
            <tr>
                <td class="stat-cell-label">CANTIDAD DE TICKETS</td>
                <td class="stat-cell-label">TICKET PROMEDIO</td>
                <td class="stat-cell-label">CANT. COMENSALES</td>
                <td class="stat-cell-label">COMENSALES PROMEDIO</td>
            </tr>
            <tr>
                <td class="stat-cell-value">{{ $data['enviar_sucursal']['parador']['cantidad_tickets'] }}</td>
                <td class="stat-cell-value">${{ $data['enviar_sucursal']['parador']['ticket_promedio'] }}</td>
                <td class="stat-cell-value">{{ $data['enviar_sucursal']['parador']['cantidad_comensales'] }}</td>
                <td class="stat-cell-value">${{ $data['enviar_sucursal']['parador']['comensales_promedio'] }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Timeline Jornada -->
    <div class="timeline-wrapper">
        <table>
            <tr>
                <td class="timeline-label">APERTURA</td>
                <td class="timeline-label">PRIMER VENTA</td>
                <td class="timeline-label">ÚLTIMA VENTA</td>
                <td class="timeline-label">CIERRE</td>
            </tr>
            <tr>
                <td class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['apertura'] ?? '11:10' }}</td>
                <td class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['primera_venta'] ?? '2:37 pm' }}</td>
                <td class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['ultima_venta'] ?? '8:09 pm' }}</td>
                <td class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['cierre'] ?? '8:19 pm' }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="timeline-label">INTERVALO PRIMER VENTA</td>
                <td class="timeline-label">DURACIÓN JORNADA</td>
                <td class="timeline-label">INTERVALO ÚLTIMA VENTA</td>
            </tr>
            <tr>
                <td class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['intervalo_primera_venta'] ?? '03:27:00' }}</td>
                <td class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['duracion_jornada'] ?? '09:09:00' }}</td>
                <td class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['intervalo_ultima_venta'] ?? '00:10:00' }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Diferencias de Caja -->
    <div class="section-title">DIFERENCIAS DE CAJA</div>
    
    <table>
        <tr><td colspan="6" class="title-medium">MERCADO PAGO</td></tr>
        <tr>
            <td class="header-cell">Real</td>
            <td class="header-cell">Real No Conc.</td>
            <td class="header-cell">Sistema</td>
            <td class="header-cell">Sist. No Conc.</td>
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
        <tr><td colspan="6" class="title-medium">GETNET</td></tr>
        <tr>
            <td class="header-cell">Real</td>
            <td class="header-cell">Real No Conc.</td>
            <td class="header-cell">Sistema</td>
            <td class="header-cell">Sist. No Conc.</td>
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
    
    <table>
        <tr><td colspan="6" class="title-medium">EFECTIVO</td></tr>
        <tr>
            <td class="header-cell">Apertura Caja</td>
            <td class="header-cell">Efectivo Real</td>
            <td class="header-cell">Pagos</td>
            <td class="header-cell">Recuento Real</td>
            <td class="header-cell">Diferencia</td>
            <td class="header-cell">%</td>
        </tr>
        <tr>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['apertura_caja'] ?? '0.00' }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['efectivo_real'] ?? '0.00' }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['pagos'] ?? '0.00' }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['recuento_real'] ?? '0.00' }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['diferencia'] ?? '0.00' }}</td>
            <td class="data-cell">{{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['porcentaje'] ?? '0.00' }}%</td>
        </tr>
    </table>

    <table>
        <tr><td colspan="3" class="title-medium">CTA CTE</td></tr>
        <tr>
            <td class="header-cell">CtaCte Sistema</td>
            <td class="header-cell">Conciliado Sistema</td>
            <td class="header-cell">CtaCte Real</td>
        </tr>
        <tr>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['cta_cte']['sistema'] ?? '0.00' }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['cta_cte']['conciliado_sistema'] ?? '0.00' }}</td>
            <td class="data-cell">${{ $data['enviar_sucursal']['diferencias_caja']['cta_cte']['real'] ?? '0.00' }}</td>
        </tr>
    </table>
    
    <!-- Facturación (reemplaza dashboard con gráficos) -->
    <div class="facturacion-box">
        <div style="font-size: 11pt; margin-bottom: 8px;">
            <strong>FACTURACIÓN REAL:</strong> ${{ $data['enviar_sucursal']['facturacion']['real'] }}
        </div>
        <div style="font-size: 11pt; margin-bottom: 8px;">
            <strong>FACTURACIÓN IDEAL:</strong> ${{ $data['enviar_sucursal']['facturacion']['ideal'] }}
        </div>
        <div style="font-size: 16pt; color: #dc3545; font-weight: bold; margin-top: 10px;">
            DIFERENCIA: ${{ $data['enviar_sucursal']['facturacion']['diferencia'] }}
        </div>
        <div style="font-size: 14pt; color: #1f3864; font-weight: bold;">
            % DESVÍO: {{ $data['enviar_sucursal']['facturacion']['desvio_porcentaje'] }}%
        </div>
    </div>
    
    <div class="section-title">ENVIAR EGRESOS</div>
    <table>
        <tr><td colspan="3" class="title-medium">EGRESOS CAJA ADICIÓN</td></tr>
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
    
    <div style="text-align: center; margin-top: 20px; font-size: 8pt; color: #666;">
        Generado el {{ now()->format('d/m/Y H:i:s') }} - Batch: {{ $execution->fileBatch->batch_code }}
    </div>
</body>
</html>
