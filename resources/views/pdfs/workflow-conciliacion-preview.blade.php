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
            /* Agregamos padding lateral para que el botón no se pegue al borde de la pantalla */
            padding-right: 200px;
        }
        
        /* Envoltura principal para posicionar el botón fuera */
        .main-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            position: relative; /* Referencia para el botón */
        }
        
        .container {
            width: 100%;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-bar {
            background: #1F3864;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between; /* Título a la izquierda, Logo a la derecha */
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        

        .header-bar h1 {
            font-size: 32px;
            font-weight: bold;
            color: white;
        }
        
        .logo-container {
            padding: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-container img {
            height: 60px;
            width: auto;
        }
        
        /* Botón fuera del contenedor */
        .download-btn {
            background: #28a745;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
            
            /* Posicionamiento absoluto fuera del contenedor */
            position: absolute;
            right: -180px; /* Lo mueve a la derecha, fuera del contenedor de 1200px */
            top: 25px;    /* Lo alinea a la altura del header */
            width: 160px;
            text-align: center;
        }
        
        .download-btn:hover {
            background: #218838;
        }
        
        .content {
            padding: 30px;
            background: #E7E6E6;
        }
        
        /* Main Info Table Wrapper */
        .main-info-wrapper {
            background: #BDBDBD;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .main-info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }
        
        .info-cell-label {
            background: #1F3864;
            color: white;
            font-weight: bold;
            padding: 12px;
            text-align: center;
            font-size: 14px;
            border-radius: 4px;
        }
        
        .info-cell-value {
            background: white;
            color: black;
            padding: 12px;
            text-align: center;
            font-size: 14px;
            border-radius: 4px;
        }
        
        .total-ventas-label {
            background: #1F3864;
            color: white;
            font-weight: bold;
            padding: 12px;
            text-align: center;
            font-size: 14px;
            border-radius: 4px;
        }
        
        .total-ventas-value {
            background: white;
            color: black;
            padding: 12px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border-radius: 4px;
        }
        
        .parador-cell {
            background: #BDBDBD;
            color: black;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 12px;
            border-radius: 4px;
        }
        
        /* Table Styles matching Google Sheets */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .header-cell {
            background-color: #bdbdbd;
            color: #1f3864;
            font-weight: bold;
            text-align: center;
            padding: 12px;
            font-size: 14px;
        }
        
        .header-cell-dark {
            background-color: #1f3864;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 12px;
            font-size: 14px;
        }
        
        .title-large {
            background-color: #bdbdbd;
            color: #1f3864;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            font-size: 28px;
        }
        
        .title-medium {
            background-color: #bdbdbd;
            color: #1f3864;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            font-size: 22px;
        }
        
        .data-cell {
            background-color: white;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        .data-cell-gray {
            background-color: #bdbdbd;
            padding: 10px;
            border: 1px solid #999;
            text-align: center;
        }
        
        .section-title {
            background-color: #1f3864;
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .info-label {
            background-color: #bdbdbd;
            color: #1f3864;
            font-weight: bold;
            padding: 10px;
            width: 200px;
            text-align: center;
        }
        
        .info-value {
            background-color: #1f3864;
            color: white;
            padding: 10px;
            flex: 1;
            text-align: center;
        }
        
        .parador-title {
            background-color: #bdbdbd;
            color: #1f3864;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 25px;
            margin: 20px 0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 20px 0;
            background: #BDBDBD;
            padding: 15px;
            border-radius: 4px;
        }
        
        .stat-box {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .stat-label {
            background: #1F3864;
            color: white;
            font-weight: bold;
            padding: 12px;
            text-align: center;
            font-size: 14px;
            border-radius: 4px;
        }
        
        .stat-value {
            background: white;
            color: black;
            padding: 12px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <!-- Nueva envoltura para control de posición -->
    <div class="main-wrapper">
        
        <!-- Botón AHORA fuera de .container -->
        <a href="{{ route('programmer.workflows.execution.pdf.download', $execution) }}" class="download-btn">
            ⬇️ Descargar PDF
        </a>

        <div class="container">
            <!-- Header: Título a la izquierda, Logo a la derecha -->
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
                    <div class="stat-label">CANTIDAD DE COMENSALES</div>
                    <div class="stat-value">{{ $data['enviar_sucursal']['parador']['cantidad_comensales'] }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">COMENSALES PROMEDIO</div>
                    <div class="stat-value">${{ $data['enviar_sucursal']['parador']['comensales_promedio'] }}</div>
                </div>
            </div>
            
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
            
            <!-- Facturación -->
            <div style="margin-top: 40px; text-align: center;">
                <div style="background: #f8f9fa; padding: 20px; border: 2px solid #1f3864; display: inline-block;">
                    <div style="font-size: 18px; margin-bottom: 10px;">
                        <strong>FACTURACIÓN REAL:</strong> ${{ $data['enviar_sucursal']['facturacion']['real'] }}
                    </div>
                    <div style="font-size: 18px; margin-bottom: 10px;">
                        <strong>FACTURACIÓN IDEAL:</strong> ${{ $data['enviar_sucursal']['facturacion']['ideal'] }}
                    </div>
                    <div style="font-size: 24px; color: #dc3545; font-weight: bold; margin-top: 15px;">
                        DIFERENCIA: ${{ $data['enviar_sucursal']['facturacion']['diferencia'] }}
                    </div>
                    <div style="font-size: 20px; color: #1f3864; font-weight: bold;">
                        % DESVÍO: {{ $data['enviar_sucursal']['facturacion']['desvio_porcentaje'] }}%
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</body>
</html>
