<style type="text/css">
    /* ============================================
       ESTILOS BASE PARA PDF - DOMPDF COMPATIBLE
       Extraídos de los HTMLs exportados de Excel
       ============================================ */
    
    /* Reset y configuración de página */
    @page {
        margin: 15mm;
        size: A4 portrait;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Calibri', 'Arial', sans-serif;
        font-size: 11pt;
        color: #000;
        line-height: 1.2;
    }
    
    /* Page breaks */
    .page-break {
        page-break-after: always;
        clear: both;
    }
    
    /* Evitar que las filas de tabla se corten entre páginas */
    tr {
        page-break-inside: avoid;
    }
    
    /* ============================================
       ESTILOS DE TABLA (Google Sheets Export)
       ============================================ */
    
    .ritz .waffle a { 
        color: inherit; 
    }
    
    .ritz .waffle {
        width: 100%;
    }
    
    table.waffle {
        border-collapse: collapse;
        width: 100%;
        font-family: 'Calibri', Arial, sans-serif;
    }
    
    table.waffle td,
    table.waffle th {
        padding: 3px;
        vertical-align: middle;
        border: none; /* Sin bordes por defecto */
    }
    
    /* Bordes solo en celdas con contenido (clases específicas) */
    table.waffle td[class^="s"],
    table.waffle td.s0, table.waffle td.s1, table.waffle td.s2, table.waffle td.s3,
    table.waffle td.s4, table.waffle td.s5, table.waffle td.s6, table.waffle td.s7,
    table.waffle td.s8, table.waffle td.s9, table.waffle td.s10, table.waffle td.s11,
    table.waffle td.s12, table.waffle td.s13, table.waffle td.s14, table.waffle td.s15,
    table.waffle td.s16, table.waffle td.s17, table.waffle td.s18, table.waffle td.s19,
    table.waffle td.s20, table.waffle td.s21, table.waffle td.s22, table.waffle td.s23,
    table.waffle td.s28, table.waffle td.s29, table.waffle td.s30, table.waffle td.s31,
    table.waffle td.s32, table.waffle td.s33, table.waffle td.s35, table.waffle td.s36,
    table.waffle td.s38, table.waffle td.s39, table.waffle td.s40 {
        border: 1px solid #000000;
    }
    
    /* ============================================
       CLASES DE CELDAS (s0-s40)
       Estilos específicos de cada tipo de celda
       ============================================ */
    
    /* Encabezados principales - Azul oscuro */
    .s0, .s5, .s6, .s13, .s21 {
        background-color: #1f3864;
        color: #ffffff;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
        padding: 8px 3px;
    }
    
    /* Encabezados secundarios - Gris claro */
    .s2, .s10, .s23, .s29 {
        background-color: #bdbdbd;
        color: #1f3864;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
        padding: 8px 3px;
    }
    
    /* Celdas de datos - Fondo gris */
    .s3, .s4, .s12, .s22, .s27, .s31, .s32 {
        background-color: #e7e6e6;
        text-align: left;
        vertical-align: bottom;
        padding: 5px 3px;
    }
    
    /* Celdas de datos - Fondo blanco */
    .s7, .s8, .s9, .s38 {
        background-color: #ffffff;
        text-align: center;
        vertical-align: middle;
        padding: 5px 3px;
    }
    
    /* Celdas con bordes */
    .s14, .s16, .s17, .s18, .s19, .s20 {
        border-bottom: 1px solid #000000;
        border-right: 1px solid #000000;
        background-color: #dedede;
        text-align: center;
        padding: 6px 3px;
    }
    
    /* Celdas de datos con fondo dedede */
    .s30, .s34, .s35, .s36 {
        background-color: #dedede;
        text-align: center;
        padding: 5px 3px;
    }
    
    /* Títulos grandes */
    .s1, .s4 {
        font-size: 20pt;
        font-weight: bold;
    }
    
    /* Subtítulos */
    .s11 {
        background-color: #bdbdbd;
        font-size: 17pt;
        font-weight: bold;
        text-align: center;
    }
    
    /* Valores numéricos grandes */
    .s9 {
        font-size: 24pt;
        font-weight: normal;
    }
    
    /* Parador - Título muy grande */
    .s3.parador-title {
        background-color: #bdbdbd;
        font-size: 33pt;
        font-weight: bold;
        text-align: center;
        color: #1f3864;
    }
    
    /* ============================================
       HEADER BAR - Barra superior azul
       ============================================ */
    
    .header-bar {
        background: #1f3864;
        color: white;
        padding: 15px 20px;
        margin-bottom: 15px;
        text-align: center;
    }
    
    .header-bar h1 {
        font-size: 24pt;
        font-weight: bold;
        margin: 0;
    }
    
    /* ============================================
       WRAPPERS - Contenedores con fondo gris
       ============================================ */
    
    .main-info-wrapper,
    .stats-wrapper,
    .timeline-wrapper {
        background: #bdbdbd;
        padding: 10px;
        margin-bottom: 12px;
    }
    
    /* ============================================
       CELDAS ESPECÍFICAS
       ============================================ */
    
    .info-cell-label {
        background: #1f3864;
        color: white;
        font-weight: bold;
        padding: 8px;
        text-align: center;
        font-size: 9pt;
    }
    
    .info-cell-value {
        background: white;
        color: black;
        padding: 8px;
        text-align: center;
        font-size: 9pt;
    }
    
    .total-ventas-label {
        background: #1f3864;
        color: white;
        font-weight: bold;
        padding: 8px;
        text-align: center;
        font-size: 9pt;
    }
    
    .total-ventas-value {
        background: white;
        color: black;
        padding: 8px;
        text-align: center;
        font-size: 14pt;
        font-weight: bold;
    }
    
    .parador-cell {
        background: #bdbdbd;
        color: black;
        font-size: 20pt;
        font-weight: bold;
        text-align: center;
        padding: 8px;
    }
    
    /* ============================================
       HEADERS DE TABLA
       ============================================ */
    
    .header-cell {
        background-color: #bdbdbd;
        color: #1f3864;
        font-weight: bold;
        text-align: center;
        padding: 8px;
        font-size: 9pt;
        border: 1px solid #fff;
    }
    
    .header-cell-dark {
        background-color: #1f3864;
        color: white;
        font-weight: bold;
        text-align: center;
        padding: 8px;
        font-size: 9pt;
        border: 1px solid #fff;
    }
    
    /* ============================================
       TÍTULOS DE SECCIÓN
       ============================================ */
    
    .title-medium {
        background-color: #bdbdbd;
        color: #1f3864;
        font-weight: bold;
        text-align: center;
        padding: 10px;
        font-size: 16pt;
        border: 1px solid #fff;
    }
    
    .section-title {
        background-color: #1f3864;
        color: white;
        padding: 10px;
        font-size: 12pt;
        font-weight: bold;
        margin-top: 15px;
        margin-bottom: 10px;
        text-align: center;
    }
    
    /* ============================================
       CELDAS DE DATOS
       ============================================ */
    
    .data-cell {
        background-color: white;
        padding: 6px;
        border: 1px solid #ddd;
        text-align: center;
        font-size: 8pt;
    }
    
    .data-cell-gray {
        background-color: #bdbdbd;
        padding: 6px;
        border: 1px solid #999;
        text-align: center;
        font-size: 8pt;
    }
    
    /* ============================================
       ESTADÍSTICAS
       ============================================ */
    
    .stat-cell-label {
        background: #1f3864;
        color: white;
        font-weight: bold;
        padding: 6px;
        text-align: center;
        font-size: 8pt;
    }
    
    .stat-cell-value {
        background: white;
        color: black;
        padding: 8px;
        text-align: center;
        font-size: 12pt;
        font-weight: bold;
    }
    
    /* ============================================
       TIMELINE / HORARIOS
       ============================================ */
    
    .timeline-label {
        background: #1f3864;
        color: white;
        padding: 5px;
        font-size: 8pt;
        font-weight: bold;
        text-align: center;
    }
    
    .timeline-value {
        background: white;
        color: black;
        padding: 6px;
        font-size: 10pt;
        font-weight: bold;
        text-align: center;
    }
    
    /* ============================================
       FACTURACIÓN BOX
       ============================================ */
    
    .facturacion-box {
        background: #f8f9fa;
        border: 2px solid #1f3864;
        padding: 12px;
        text-align: center;
        margin: 15px 0;
    }
    
    /* ============================================
       UTILIDADES
       ============================================ */
    
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
    .bg-white { background-color: white; }
    .bg-gray { background-color: #e7e6e6; }
    .bg-dark-gray { background-color: #bdbdbd; }
    .bg-blue { background-color: #1f3864; }
    .text-white { color: white; }
    .text-blue { color: #1f3864; }
</style>
