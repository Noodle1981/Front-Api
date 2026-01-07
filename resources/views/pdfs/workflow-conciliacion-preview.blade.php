<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arqueo de Caja</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Calibri', Arial, sans-serif;
            background-color: #E7E6E6;
            padding: 20px;
            padding-right: 200px; /* Espacio para el botón de descarga */
        }
        
        .main-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
        }
        
        .container {
            width: 100%;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Header corregido: Título inicio, Logo fin */
        .header-bar {
            background: #1F3864;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0;
        }
        
        .header-bar h1 {
            font-size: 32px;
            font-weight: bold;
        }
        
        .logo-container img {
            height: 60px;
            width: auto;
        }
        
        .download-btn {
            background: #28a745;
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            position: absolute;
            right: -180px;
            top: 25px;
            width: 160px;
            text-align: center;
            transition: background 0.3s;
        }
        
        .download-btn:hover { background: #218838; }
        
        .return-btn {
            background: #007bff;
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            position: absolute;
            right: -180px;
            top: 85px;
            width: 160px;
            text-align: center;
            transition: background 0.3s;
        }
        
        .return-btn:hover { background: #0056b3; }
        
        .content {
            padding: 30px;
            background: #E7E6E6;
        }

        /* Tablas: Forzar ancho completo y uniformidad */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            table-layout: fixed; /* Esto hace que todas las tablas se vean iguales */
        }
        
        .main-info-wrapper {
            background: #BDBDBD;
            padding: 15px;
            margin-bottom: 20px;
        }

        .main-info-table {
            table-layout: auto; /* Esta es la única que se ajusta al contenido */
            border-collapse: separate;
            border-spacing: 12px; /* Mejor separación entre celdas */
        }

        .info-cell-label { background: #1F3864; color: white; font-weight: bold; padding: 12px; text-align: center; font-size: 14px; border-radius: 4px; }
        .info-cell-value { background: white; color: black; padding: 12px; text-align: center; font-size: 14px; border-radius: 4px; }
        .total-ventas-label { background: #1F3864; color: white; font-weight: bold; padding: 12px; text-align: center; font-size: 14px; border-radius: 4px; }
        .total-ventas-value { background: white; color: black; padding: 12px; text-align: center; font-size: 20px; font-weight: bold; border-radius: 4px; }
        .parador-cell { background: #BDBDBD; color: black; font-size: 32px; font-weight: bold; text-align: center; padding: 10px; }

        .header-cell { background-color: #bdbdbd; color: #1f3864; font-weight: bold; text-align: center; padding: 12px; font-size: 14px; border: 1px solid #fff; }
        .header-cell-dark { background-color: #1f3864; color: white; font-weight: bold; text-align: center; padding: 12px; font-size: 14px; border: 1px solid #fff; }
        .title-medium { background-color: #bdbdbd; color: #1f3864; font-weight: bold; text-align: center; padding: 15px; font-size: 22px; border: 1px solid #fff; }
        .data-cell { background-color: white; padding: 10px; border: 1px solid #ddd; text-align: center; word-wrap: break-word; }
        .data-cell-gray { background-color: #bdbdbd; padding: 10px; border: 1px solid #999; text-align: center; }

        .section-title { background-color: #1f3864; color: white; padding: 15px; font-size: 18px; font-weight: bold; margin-top: 30px; margin-bottom: 15px; }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
            background: #BDBDBD;
            padding: 15px;
        }
        .stat-box { display: flex; flex-direction: column; gap: 5px; }
        .stat-label { background: #1F3864; color: white; font-weight: bold; padding: 8px; text-align: center; font-size: 11px; border-radius: 4px; }
        .stat-value { background: white; color: black; padding: 10px; text-align: center; font-size: 16px; font-weight: bold; border-radius: 4px; }

        /* Timeline Jornada Mejorada */
        .timeline-container {
            background: #BDBDBD;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        .timeline-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            text-align: center;
        }
        .timeline-icons-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            margin: 15px 0;
            text-align: center;
            font-size: 30px;
            align-items: center;
        }
        .timeline-bottom-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            text-align: center;
        }
        .timeline-card {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .timeline-label { background: #1F3864; color: white; padding: 6px; font-size: 11px; font-weight: bold; border-radius: 4px; text-transform: uppercase; }
        .timeline-value { background: white; color: black; padding: 8px; font-size: 15px; font-weight: bold; border-radius: 4px; }

        /* Responsive */
        @media (max-width: 900px) {
            body { padding-right: 20px; }
            .download-btn, .return-btn { 
                position: static; 
                width: 100%; 
                margin-bottom: 15px; 
                display: block;
            }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .timeline-row, .timeline-icons-row { grid-template-columns: repeat(2, 1fr); }
            .timeline-bottom-row { grid-template-columns: 1fr; }
        }
        
        /* Dashboard de Gráficos */
        .dashboard-header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: #1F3864;
            color: white;
            padding: 15px;
            font-weight: bold;
            text-align: center;
            font-size: 16px;
            gap: 2px;
            margin-top: 30px;
        }

        .dashboard-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            padding: 30px 20px;
            margin-bottom: 30px;
            gap: 30px;
            align-items: center;
            border: 1px solid #ddd;
        }

        .bar-chart-container {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            height: 220px;
            padding-top: 40px;
            border-bottom: 3px solid #1F3864;
        }

        .bar-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 80px;
        }

        .bar {
            background: linear-gradient(180deg, #2a4a7f 0%, #1F3864 100%);
            width: 100%;
            border-radius: 6px 6px 0 0;
            position: relative;
            transition: all 0.3s;
            box-shadow: 0 -2px 8px rgba(31, 56, 100, 0.3);
        }

        .bar:hover { transform: scale(1.05); }

        .bar-value {
            position: absolute;
            top: -28px;
            width: 100%;
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            color: #1F3864;
        }

        .bar-label {
            margin-top: 12px;
            font-weight: bold;
            font-size: 14px;
            color: #1F3864;
        }

        .pie-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .pie-chart {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: conic-gradient(#1F3864 0% 53%, #bdbdbd 53% 100%);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: relative;
        }

        .pie-chart::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .pie-stats { text-align: center; }
        .pie-diff { font-size: 24px; color: #dc3545; font-weight: bold; margin-top: 8px; }
        .pie-perc { font-size: 18px; color: #1f3864; font-weight: bold; margin-top: 5px; }
    </style>
</head>
<body>

    <div class="main-wrapper">
        
        <a href="{{ route('programmer.workflows.execution.pdf.download', $execution) }}" class="download-btn">
            ⬇️ Descargar PDF
        </a>
        
        <a href="{{ route('programmer.workflows.upload') }}" class="return-btn">
            ← Ejecutar Nuevo Workflow
        </a>

        <div class="container">
            <div class="header-bar">
                <h1>ARQUEO DE CAJA</h1>
                <div class="logo-container">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo Empresa">
                </div>
            </div>

            <div class="content">
                <!-- Main Info Table -->
                <div class="main-info-wrapper">
                    <table class="main-info-table">
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
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-label">CANTIDAD DE TICKETS</div>
                        <div class="stat-value">{{ $data['enviar_sucursal']['parador']['cantidad_tickets'] }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">TICKET PROMEDIO</div>
                        <div class="stat-value">${{ $data['enviar_sucursal']['parador']['ticket_promedio'] }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">CANT. COMENSALES</div>
                        <div class="stat-value">{{ $data['enviar_sucursal']['parador']['cantidad_comensales'] }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">COMENSALES PROMEDIO</div>
                        <div class="stat-value">${{ $data['enviar_sucursal']['parador']['comensales_promedio'] }}</div>
                    </div>
                </div>
                
                <!-- Timeline Jornada (Rediseñada con iconos en medio) -->
                <div class="timeline-container">
                    <div class="timeline-row">
                        <div class="timeline-card">
                            <div class="timeline-label">APERTURA</div>
                            <div class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['apertura'] ?? '11:10' }}</div>
                        </div>
                        <div class="timeline-card">
                            <div class="timeline-label">PRIMER VENTA</div>
                            <div class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['primera_venta'] ?? '2:37 pm' }}</div>
                        </div>
                        <div class="timeline-card">
                            <div class="timeline-label">ÚLTIMA VENTA</div>
                            <div class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['ultima_venta'] ?? '8:09 pm' }}</div>
                        </div>
                        <div class="timeline-card">
                            <div class="timeline-label">CIERRE</div>
                            <div class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['cierre'] ?? '8:19 pm' }}</div>
                        </div>
                    </div>

                    <div class="timeline-icons-row">
                        <div>🕐</div>
                        <div>📌</div>
                        <div>📌</div>
                        <div>🕐</div>
                    </div>
                    
                    <div class="timeline-bottom-row">
                        <div class="timeline-card">
                            <div class="timeline-label">INTERVALO PRIMER VENTA</div>
                            <div class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['intervalo_primera_venta'] ?? '03:27:00' }}</div>
                        </div>
                        <div class="timeline-card">
                            <div class="timeline-label">DURACIÓN JORNADA</div>
                            <div class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['duracion_jornada'] ?? '09:09:00' }}</div>
                        </div>
                        <div class="timeline-card">
                            <div class="timeline-label">INTERVALO ÚLTIMA VENTA</div>
                            <div class="timeline-value">{{ $data['enviar_sucursal']['horarios_venta']['intervalo_ultima_venta'] ?? '00:10:00' }}</div>
                        </div>
                    </div>
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
                
                <!-- Dashboard de Gráficos -->
                <div class="dashboard-header">
                    <div>TOTAL DE VENTAS POR HORA</div>
                    <div>FACTURACIÓN</div>
                </div>

                <div class="dashboard-body">
                    <!-- Gráfico de Barras -->
                    <div class="bar-chart-container">
                        <div class="bar-group">
                            <div class="bar" style="height: 53px;">
                                <div class="bar-value">$150.6k</div>
                            </div>
                            <div class="bar-label">14:00</div>
                        </div>
                        <div class="bar-group">
                            <div class="bar" style="height: 180px;">
                                <div class="bar-value">$566.7k</div>
                            </div>
                            <div class="bar-label">15:00</div>
                        </div>
                        <div class="bar-group">
                            <div class="bar" style="height: 56px;">
                                <div class="bar-value">$160.0k</div>
                            </div>
                            <div class="bar-label">16:00</div>
                        </div>
                    </div>

                    <!-- Gráfico de Pastel y Resumen -->
                    <div class="pie-container">
                        <div class="pie-chart"></div>
                        
                        <div style="font-size: 14px; text-align: center;">
                            <strong>FACTURACIÓN REAL:</strong> ${{ $data['enviar_sucursal']['facturacion']['real'] }}<br>
                            <strong>FACTURACIÓN IDEAL:</strong> ${{ $data['enviar_sucursal']['facturacion']['ideal'] }}
                        </div>

                        <div class="pie-stats">
                            <div class="pie-diff">DIFERENCIA: ${{ $data['enviar_sucursal']['facturacion']['diferencia'] }}</div>
                            <div class="pie-perc">% DESVÍO: {{ $data['enviar_sucursal']['facturacion']['desvio_porcentaje'] }}%</div>
                        </div>
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
                        <td class="header-cell-dark">Hora Anulación</td>
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
            </div>
        </div>
    </div>
</body>
</html>