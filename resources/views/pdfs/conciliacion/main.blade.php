<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Arqueo de Caja - {{ $metadata['sucursal'] }} - {{ $metadata['fecha'] }}</title>
    
    @include('pdfs.conciliacion.partials.styles')
    
    <style>
        /* Container para preview en navegador */
        .pdf-preview-container {
            max-width: 900px;  /* Ancho máximo del PDF */
            margin: 0 auto;    /* Centrado */
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        /* Para impresión/PDF, usar todo el ancho */
        @media print {
            .pdf-preview-container {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body style="background: #f5f5f5; padding: 20px 0;">
    <div class="pdf-preview-container">
        {{-- ========================================
             PÁGINA 1 DE 4: ENVIAR SUCURSAL
             Resumen general del día
             ======================================== --}}
        @include('pdfs.conciliacion.enviar-sucursal')
        
        <div class="page-break"></div>
        
        {{-- ========================================
             PÁGINA 2 DE 4: ENVIAR EGRESOS
             Detalle de egresos del día
             ======================================== --}}
        @include('pdfs.conciliacion.enviar-egresos')
        
        <div class="page-break"></div>
        
        {{-- ========================================
             PÁGINA 3 DE 4: ENVIAR NO CONCILIADOS
             Transacciones sin conciliar
             ======================================== --}}
        @include('pdfs.conciliacion.enviar-no-conciliados')
        
        <div class="page-break"></div>
        
        {{-- ========================================
             PÁGINA 4 DE 4: ENVIAR ANULACIONES
             Productos y ventas anuladas
             ======================================== --}}
        @include('pdfs.conciliacion.enviar-anulaciones')
    </div>
</body>
</html>
