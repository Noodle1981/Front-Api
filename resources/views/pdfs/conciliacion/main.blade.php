<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Arqueo de Caja - {{ $metadata['sucursal'] }} - {{ $metadata['fecha'] }}</title>
    
    @include('pdfs.conciliacion.partials.styles')
</head>
<body>
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
</body>
</html>
