{{-- PÁGINA 1: ENVIAR SUCURSAL - Resumen General del Día --}}

<style>
    /* Estilos adicionales para mejorar el diseño */
    .padding-row {
        padding-top: 15px !important;
    }
    .large-indicator {
        font-size: 28pt !important;
        font-weight: bold !important;
    }
    .parador-destacado {
        background-color: #bdbdbd !important;
        font-weight: bold !important;
        font-size: 20pt !important;
        padding: 12px 3px !important;
        text-align: center !important;
    }
    .header-azul {
        background-color: #1f3864 !important;
        color: #ffffff !important;
        font-size: 10pt !important;
    }
    .valor-blanco {
        background-color: #ffffff !important;
        color: #000000 !important;
        font-size: 12pt !important;
    }
    .valor-grande {
        font-size: 13pt !important;
    }
</style>

<div class="ritz grid-container" dir="ltr">
    <table class="waffle" cellspacing="0" cellpadding="0">
        <tbody>
            {{-- HEADER: ARQUEO DE CAJA + LOGO --}}
            <tr style="height: 76px">
                <td class="s0" colspan="5" style="text-align: left; padding: 15px 3px;">ARQUEO DE CAJA</td>
                <td class="s5 logo-container" colspan="2">
                    <img src="{{ $logo_base64 }}" alt="Logo" class="logo-img">
                </td>
            </tr>
            
            {{-- Fila de separación --}}
            <tr style="height: 15px">
                <td colspan="7" style="background-color: #ffffff;"></td>
            </tr>
            
            {{-- FILA: FECHA Y DIA --}}
            <tr style="height: 18px">
                <td class="s2 header-azul">FECHA</td>
                <td class="s2 header-azul">DIA</td>
                <td class="s4" colspan="2" rowspan="4" style="background-color: #ffffff; border-top: none; border-bottom: none;"></td>
                <td class="s2 header-azul" colspan="3">TOTAL VENTAS</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s5 valor-blanco">{{ $metadata['fecha'] }}</td>
                <td class="s5 valor-blanco">{{ $metadata['dia'] }}</td>
                <td class="s6 valor-blanco large-indicator" colspan="3" rowspan="3">${{ $data['enviar_sucursal']['total_ventas'] }}</td>
            </tr>
            
            {{-- FILA: TURNO Y ENCARGADO --}}
            <tr style="height: 18px">
                <td class="s2 header-azul">TURNO</td>
                <td class="s2 header-azul">ENCARGADO</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s5 valor-blanco">{{ $metadata['turno'] }}</td>
                <td class="s5 valor-blanco">{{ $metadata['encargado'] }}</td>
            </tr>
            
            {{-- Fila de separación --}}
            <tr style="height: 15px">
                <td colspan="7" style="background-color: #ffffff;"></td>
            </tr>
            
            {{-- PARADOR (con padding antes) --}}
            <tr style="height: 30px">
                <td class="parador-destacado" colspan="7">{{ $metadata['sucursal'] }}</td>
            </tr>
            
            {{-- Fila de separación --}}
            <tr style="height: 15px">
                <td colspan="7" style="background-color: #ffffff;"></td>
            </tr>
            
            {{-- ESTADÍSTICAS DEL PARADOR (con padding antes) --}}
            <tr style="height: 18px; padding-top: 15px;">
                <td class="s8" rowspan="2" style="background-color: #ffffff; border: none;"></td>
                <td class="s9 header-azul">CANTIDAD DE TICKETS</td>
                <td class="s9 header-azul">TICKET PROMEDIO</td>
                <td class="s9 header-azul">CANTIDAD DE COMENSALES</td>
                <td class="s9 header-azul" colspan="2">COMENSALES PROMEDIO</td>
                <td class="s10" rowspan="2" style="background-color: #ffffff; border: none;"></td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s5 valor-blanco valor-grande">{{ $data['enviar_sucursal']['parador']['cantidad_tickets'] }}</td>
                <td class="s5 valor-blanco valor-grande">${{ $data['enviar_sucursal']['parador']['ticket_promedio'] }}</td>
                <td class="s5 valor-blanco valor-grande">{{ $data['enviar_sucursal']['parador']['cantidad_comensales'] }}</td>
                <td class="s5 valor-blanco valor-grande" colspan="2">${{ $data['enviar_sucursal']['parador']['comensales_promedio'] }}</td>
            </tr>
            
            {{-- Fila de separación --}}
            <tr style="height: 15px">
                <td colspan="7" style="background-color: #ffffff;"></td>
            </tr>
            
            {{-- HORARIOS DE VENTA (con padding antes) --}}
            <tr style="height: 18px; padding-top: 15px;">
                <td class="s11 header-azul" colspan="7">HORARIOS DE VENTA</td>
            </tr>
            
            <tr style="height: 17px">
                <td class="s12 valor-blanco" style="text-align: center;">{{ $data['enviar_sucursal']['horarios_venta']['apertura'] }}</td>
                <td class="s13" rowspan="2" style="background-color: #bdbdbd;"></td>
                <td class="s12 valor-blanco" style="text-align: center;">{{ $data['enviar_sucursal']['horarios_venta']['primera_venta'] }}</td>
                <td class="s13" rowspan="2" style="background-color: #bdbdbd;"></td>
                <td class="s12 valor-blanco" style="text-align: center;">{{ $data['enviar_sucursal']['horarios_venta']['ultima_venta'] }}</td>
                <td class="s13" rowspan="2" style="background-color: #bdbdbd;"></td>
                <td class="s14 valor-blanco" style="text-align: center;">{{ $data['enviar_sucursal']['horarios_venta']['cierre'] }}</td>
            </tr>
            
            <tr style="height: 15px">
                <td class="s15" style="text-align: center;">APERTURA</td>
                <td class="s15" style="text-align: center;">PRIMER VENTA</td>
                <td class="s15" style="text-align: center;">ÚLTIMA VENTA</td>
                <td class="s15" style="text-align: center;">CIERRE</td>
            </tr>
            
            <tr style="height: 44px">
                <td class="s16" style="border-right: none; text-align: center;">
                    <i class="fas fa-clock"></i>
                </td>
                <td class="s17" style="border-left: none; border-right: none; text-align: center;">- - - - - - - - - - - - - - - - - - -</td>
                <td class="s16" style="border-left: none; border-right: none; text-align: center;">
                    <i class="fas fa-map-marker-alt"></i>
                </td>
                <td class="s17" style="border-left: none; border-right: none; text-align: center;">- - - - - - - - - - - - - - - - - - -</td>
                <td class="s16" style="border-left: none; border-right: none; text-align: center;">
                    <i class="fas fa-map-marker-alt"></i>
                </td>
                <td class="s17" style="border-left: none; border-right: none; text-align: center;">- - - - - - - - - - - - - - - - - - -</td>
                <td class="s16" style="border-left: none; text-align: center;">
                    <i class="fas fa-clock"></i>
                </td>
            </tr>
            
            <tr style="height: 22px">
                <td class="s18" rowspan="2"></td>
                <td class="s12 valor-blanco" style="text-align: center;">{{ $data['enviar_sucursal']['horarios_venta']['intervalo_primera_venta'] }}</td>
                <td class="s18" rowspan="2"></td>
                <td class="s12 valor-blanco" style="text-align: center;">{{ $data['enviar_sucursal']['horarios_venta']['duracion_jornada'] }}</td>
                <td class="s18" rowspan="2"></td>
                <td class="s12 valor-blanco" style="text-align: center;">{{ $data['enviar_sucursal']['horarios_venta']['intervalo_ultima_venta'] }}</td>
                <td class="s19" rowspan="2"></td>
            </tr>
            
            <tr style="height: 26px">
                <td class="s15" style="text-align: center;">INTERVALO PRIMER VENTA</td>
                <td class="s15" style="text-align: center;">DURACIÓN DE LA JORNADA</td>
                <td class="s15" style="text-align: center;">INTERVALO DE LA ULTIMA VENTA</td>
            </tr>
            
            {{-- Fila de separación --}}
            <tr style="height: 15px">
                <td colspan="7" style="background-color: #ffffff;"></td>
            </tr>
            
            {{-- DIFERENCIAS DE CAJA --}}
            <tr style="height: 27px">
                <td class="s20 header-azul" colspan="7">DIFERENCIAS DE CAJA</td>
            </tr>
            
            {{-- Fila de separación --}}
            <tr style="height: 15px">
                <td colspan="7" style="background-color: #ffffff;"></td>
            </tr>
            
            {{-- MERCADO PAGO --}}
            <tr style="height: 24px">
                <td class="s21" colspan="7">MERCADO PAGO</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s22 header-azul">MercadoPago Real</td>
                <td class="s22 header-azul">Real No Conciliado</td>
                <td class="s22 header-azul">Mercado Pago Sistema</td>
                <td class="s22 header-azul">Sistema No Conciliado</td>
                <td class="s22 header-azul">Diferencia</td>
                <td class="s22 header-azul">%</td>
                <td class="s22 header-azul"></td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['real'] }}</td>
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['real_no_conciliado'] }}</td>
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['sistema'] }}</td>
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['sistema_no_conciliado'] }}</td>
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['diferencia'] }}</td>
                <td class="s23 valor-blanco">{{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['porcentaje'] }}%</td>
                <td class="s23 valor-blanco">{{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['porcentaje_conciliacion'] }}%</td>
            </tr>
            
            {{-- GETNET --}}
            <tr style="height: 31px">
                <td class="s21" colspan="7">GETNET</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s22 header-azul">Getnet Real</td>
                <td class="s22 header-azul">Real No Conciliado</td>
                <td class="s22 header-azul">Getnet Sistema</td>
                <td class="s22 header-azul">Sistema No Concilicado</td>
                <td class="s22 header-azul">Diferencia</td>
                <td class="s22 header-azul">%</td>
                <td class="s22 header-azul"></td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['real'] }}</td>
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['real_no_conciliado'] }}</td>
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['sistema'] }}</td>
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['sistema_no_conciliado'] }}</td>
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['diferencia'] }}</td>
                <td class="s23 valor-blanco">{{ $data['enviar_sucursal']['diferencias_caja']['getnet']['porcentaje'] }}%</td>
                <td class="s23 valor-blanco">{{ $data['enviar_sucursal']['diferencias_caja']['getnet']['porcentaje_conciliacion'] }}%</td>
            </tr>
            
            {{-- EFECTIVO --}}
            <tr style="height: 29px">
                <td class="s21" colspan="7">EFECTIVO</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s22 header-azul">Apertura Caja</td>
                <td class="s22 header-azul">Efectivo Real</td>
                <td class="s22 header-azul">Pagos</td>
                <td class="s22 header-azul">Recuento Real</td>
                <td class="s22 header-azul">Diferencia</td>
                <td class="s22 header-azul">%</td>
                <td class="s22 header-azul"></td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['apertura_caja'] }}</td>
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['efectivo_real'] }}</td>
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['pagos'] }}</td>
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['recuento_real'] }}</td>
                <td class="s23 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['diferencia'] }}</td>
                <td class="s23 valor-blanco">{{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['porcentaje'] }}%</td>
                <td class="s23 valor-blanco">{{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['porcentaje_conciliacion'] }}%</td>
            </tr>
            
            {{-- CTA CTE --}}
            <tr style="height: 27px">
                <td class="s21" colspan="7">CTA CTE</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s22 header-azul">CtaCte Sistema</td>
                <td class="s22 header-azul">Conciliado Sistema</td>
                <td class="s22 header-azul">CtaCte Real</td>
                <td class="s22" colspan="4" rowspan="2"></td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s24 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['cta_cte']['sistema'] }}</td>
                <td class="s24 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['cta_cte']['conciliado_sistema'] }}</td>
                <td class="s24 valor-blanco">${{ $data['enviar_sucursal']['diferencias_caja']['cta_cte']['real'] }}</td>
            </tr>
            
            {{-- Fila de separación --}}
            <tr style="height: 15px">
                <td colspan="7" style="background-color: #ffffff;"></td>
            </tr>
            
            {{-- FACTURACIÓN --}}
            <tr style="height: 18px">
                <td class="s2 header-azul" colspan="4">TOTAL DE VENTAS POR HORA</td>
                <td class="s2 header-azul" colspan="3">FACTURACIÓN IDEAL</td>
            </tr>
            
            {{-- Fila de separación --}}
            <tr style="height: 15px">
                <td colspan="7" style="background-color: #ffffff;"></td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s25 valor-blanco" colspan="4" rowspan="6">
                    {{-- Gráfico de ventas por hora --}}
                    <div style="padding: 10px; text-align: center;">
                        <strong style="color: #1f3864;">Ventas por Hora</strong><br>
                        @php
                            // Calcular el valor máximo para escalar las barras
                            $maxMonto = max(array_column($data['enviar_sucursal']['ventas_por_hora'], 'monto'));
                            $maxHeight = 80; // Altura máxima en px
                        @endphp
                        <div style="display: flex; align-items: flex-end; justify-content: center; height: 100px; margin-top: 10px;">
                            @foreach($data['enviar_sucursal']['ventas_por_hora'] as $venta)
                                @php
                                    $altura = ($venta['monto'] / $maxMonto) * $maxHeight;
                                @endphp
                                <div style="display: flex; flex-direction: column; align-items: center; margin: 0 5px;">
                                    <div style="font-size: 8pt; margin-bottom: 2px;">${{ number_format($venta['monto'], 0, ',', '.') }}</div>
                                    <div style="width: 30px; height: {{ $altura }}px; background-color: #1f3864;"></div>
                                    <div style="font-size: 8pt; margin-top: 2px;">{{ $venta['hora'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </td>
                <td class="s25 valor-blanco" colspan="3" rowspan="4">
                    {{-- Gráfico pastel facturación --}}
                    <div style="padding: 10px; text-align: center;">
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: conic-gradient(#1f3864 0% 53%, #bdbdbd 53% 100%); margin: 0 auto;"></div>
                        <div style="margin-top: 10px;">
                            <strong style="color: #1f3864;">FACTURACIÓN REAL</strong><br>
                            <span style="font-size: 12pt;">${{ $data['enviar_sucursal']['facturacion']['real'] }}</span><br><br>
                            <strong style="color: #bdbdbd;">FACTURACIÓN IDEAL</strong><br>
                            <span style="font-size: 12pt;">${{ $data['enviar_sucursal']['facturacion']['ideal'] }}</span>
                        </div>
                    </div>
                </td>
            </tr>
            
            <tr style="height: 18px"></tr>
            <tr style="height: 18px"></tr>
            <tr style="height: 51px"></tr>
            
            <tr style="height: 18px">
                <td class="s26">DIFERENCIA</td>
                <td class="s27" colspan="2">${{ $data['enviar_sucursal']['facturacion']['diferencia'] }}</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s26">DESVIO</td>
                <td class="s27" colspan="2">{{ $data['enviar_sucursal']['facturacion']['desvio_porcentaje'] }}%</td>
            </tr>
        </tbody>
    </table>
</div>
