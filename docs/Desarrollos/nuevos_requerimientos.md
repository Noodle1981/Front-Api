
## NUEVOS REQUERIMIENTOS

http://127.0.0.1:8000/programadores/workflows/execution/17/pdf/preview

Cambios de diseños, contenido y etc.

COLORES QUE USAMOS, ME OLVIDE ACLARAR

#E7E6E6
#BDBDBD
#000000
#1F3864

## 1 cambio

hay que agregar antes de la seccion de ENVIAR EGRESOS

html

<!-- Fila de Títulos Azul -->
<div class="dashboard-header">
    <div>TOTAL DE VENTAS POR HORA</div>
    <div>FACTURACIÓN</div>
</div>

<!-- Contenido de Gráficos Fondo Blanco -->
<div class="dashboard-body">
    
    <!-- LADO IZQUIERDO: Ventas por Hora -->
    <div class="bar-chart-container">
        <!-- Barra 14:00 -->
        <div class="bar-group">
            <div class="bar" style="height: 53px;"> <!-- Calculado proporcionalmente -->
                <div class="bar-value">$150.6k</div>
            </div>
            <div class="bar-label">14:00</div>
        </div>

        <!-- Barra 15:00 -->
        <div class="bar-group">
            <div class="bar" style="height: 180px;">
                <div class="bar-value">$566.7k</div>
            </div>
            <div class="bar-label">15:00</div>
        </div>

        <!-- Barra 16:00 -->
        <div class="bar-group">
            <div class="bar" style="height: 56px;">
                <div class="bar-value">$160.0k</div>
            </div>
            <div class="bar-label">16:00</div>
        </div>
    </div>

    <!-- LADO DERECHO: Pastel y Resumen -->
    <div class="pie-container">
        <div class="pie-chart"></div>
        
        <div style="font-size: 14px; text-align: center;">
            <strong>FACTURACIÓN REAL:</strong> $841,700.00<br>
            <strong>FACTURACIÓN IDEAL:</strong> $747,740.00
        </div>

        <div class="pie-stats">
            <div class="pie-diff">DIFERENCIA: $93,960.00</div>
            <div class="pie-perc">% DESVÍO: 12.57%</div>
        </div>
    </div>
</div>

css

/* Contenedor de títulos azul */
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

/* Contenedor de contenido blanco */
.dashboard-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: white;
    padding: 20px;
    margin-bottom: 30px;
    gap: 20px;
    align-items: center;
    border: 1px solid #ddd;
}

/* Gráfico de Barras */
.bar-chart-container {
    display: flex;
    justify-content: space-around;
    align-items: flex-end;
    height: 200px;
    padding-top: 40px;
    border-bottom: 2px solid #1F3864;
}

.bar-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 70px;
}

.bar {
    background: #1F3864;
    width: 100%;
    border-radius: 4px 4px 0 0;
    position: relative;
    transition: height 0.5s;
}

.bar-value {
    position: absolute;
    top: -25px;
    width: 100%;
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    color: #1F3864;
}

.bar-label {
    margin-top: 10px;
    font-weight: bold;
    font-size: 14px;
}

/* Gráfico de Pastel y Textos */
.pie-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.pie-chart {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    /* El degradado representa la proporción visual */
    background: conic-gradient(#1F3864 0% 53%, #bdbdbd 53% 100%);
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.pie-stats {
    text-align: center;
}

.pie-diff {
    font-size: 22px;
    color: #dc3545;
    font-weight: bold;
    margin-top: 5px;
}

.pie-perc {
    font-size: 18px;
    color: #1f3864;
    font-weight: bold;
}

pero es logico que hay que armar la estructura de js. este caso alpine

# 2

CORREGIR LOGO, QUE VOLVIO A HACER FONDO BLANCO

# 3 

REVISAR SEPARACIONES DE DIV DE

FECHA	12/02/2025	DIA	Martes	TOTAL VENTAS	$816,500.00
TURNO	MAÑANA	ENCARGADO	Felipe

QUEDO FEA

# 4

DIME EN QUE LINEA ESTA LO DE PARADOR PARA REVISAR Y CONSTATAR