{{-- PÁGINA 1: ENVIAR SUCURSAL - Resumen General del Día --}}

<div class="ritz grid-container" dir="ltr">
    <table class="waffle" cellspacing="0" cellpadding="0">
        <tbody>
            {{-- HEADER: ARQUEO DE CAJA --}}
            <tr style="height: 76px">
                <td class="s0" colspan="11" style="font-size: 20pt;">ARQUEO DE CAJA</td>
            </tr>
            
            <tr style="height: 18px">
                <td colspan="11"></td>
            </tr>
            
            {{-- FILA: FECHA Y DIA --}}
            <tr style="height: 18px">
                <td class="s5" colspan="2">FECHA</td>
                <td></td>
                <td class="s6" colspan="2">DIA</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="s5" colspan="3">TOTAL VENTAS</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s7" colspan="2">{{ $metadata['fecha'] }}</td>
                <td></td>
                <td class="s8" colspan="2">{{ $metadata['dia'] }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="s9" colspan="3" rowspan="4" style="font-size: 24pt;">${{ $data['enviar_sucursal']['total_ventas'] }}</td>
            </tr>
            
            <tr style="height: 18px">
                <td colspan="8"></td>
            </tr>
            
            {{-- FILA: TURNO Y ENCARGADO --}}
            <tr style="height: 18px">
                <td class="s5" colspan="2">TURNO</td>
                <td></td>
                <td class="s5" colspan="2">ENCARGADO</td>
                <td colspan="3"></td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s8" colspan="2">{{ $metadata['turno'] }}</td>
                <td></td>
                <td class="s8" colspan="2">{{ $metadata['encargado'] }}</td>
                <td colspan="3"></td>
            </tr>
            
            <tr style="height: 18px">
                <td colspan="11"></td>
            </tr>
            
            {{-- PARADOR --}}
            <tr style="height: 18px">
                <td class="s11" colspan="11" style="font-size: 17pt;">{{ $metadata['sucursal'] }}</td>
            </tr>
            
            <tr style="height: 18px">
                <td colspan="11"></td>
            </tr>
            
            {{-- ESTADÍSTICAS DEL PARADOR --}}
            <tr style="height: 18px">
                <td class="s13" colspan="2">CANTIDAD DE TICKETS</td>
                <td></td>
                <td class="s13" colspan="2">TICKET PROMEDIO</td>
                <td></td>
                <td class="s13" colspan="2">CANTIDAD DE COMENSALES</td>
                <td></td>
                <td class="s13" colspan="2">COMENSALES PROMEDIO</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s7" colspan="2">{{ $data['enviar_sucursal']['parador']['cantidad_tickets'] }}</td>
                <td></td>
                <td class="s7" colspan="2">${{ $data['enviar_sucursal']['parador']['ticket_promedio'] }}</td>
                <td></td>
                <td class="s8" colspan="2">{{ $data['enviar_sucursal']['parador']['cantidad_comensales'] }}</td>
                <td></td>
                <td class="s7" colspan="2">${{ $data['enviar_sucursal']['parador']['comensales_promedio'] }}</td>
            </tr>
            
            <tr style="height: 18px">
                <td colspan="11"></td>
            </tr>
            
            {{-- HORARIOS DE VENTA --}}
            <tr style="height: 18px">
                <td class="s5" colspan="11">HORARIOS DE VENTA</td>
            </tr>
            
            <tr style="height: 18px">
                <td colspan="11"></td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s14">{{ $data['enviar_sucursal']['horarios_venta']['apertura'] }}</td>
                <td></td>
                <td class="s14">{{ $data['enviar_sucursal']['horarios_venta']['primera_venta'] }}</td>
                <td colspan="3"></td>
                <td class="s14">{{ $data['enviar_sucursal']['horarios_venta']['ultima_venta'] }}</td>
                <td></td>
                <td class="s14">{{ $data['enviar_sucursal']['horarios_venta']['cierre'] }}</td>
                <td colspan="2"></td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s16">APERTURA</td>
                <td></td>
                <td class="s17">PRIMER VENTA</td>
                <td colspan="3"></td>
                <td class="s16">ULTIMA VENTA</td>
                <td></td>
                <td class="s18">CIERRE</td>
                <td colspan="2"></td>
            </tr>
            
            <tr style="height: 18px">
                <td colspan="11"></td>
            </tr>
            
            <tr style="height: 18px">
                <td></td>
                <td class="s14">{{ $data['enviar_sucursal']['horarios_venta']['intervalo_primera_venta'] }}</td>
                <td></td>
                <td colspan="2" class="s14">{{ $data['enviar_sucursal']['horarios_venta']['duracion_jornada'] }}</td>
                <td></td>
                <td class="s14" colspan="2">{{ $data['enviar_sucursal']['horarios_venta']['intervalo_ultima_venta'] }}</td>
                <td colspan="3"></td>
            </tr>
            
            <tr style="height: 18px">
                <td></td>
                <td class="s19">INTERVALO PRIMER VENTA</td>
                <td></td>
                <td colspan="2" class="s20">DURACION DE LA JORNADA</td>
                <td></td>
                <td class="s16" colspan="2">INTERVALO DE LA ULTIMA VENTA</td>
                <td colspan="3"></td>
            </tr>
            
            <tr style="height: 18px">
                <td colspan="11"></td>
            </tr>
            
            {{-- DIFERENCIAS DE CAJA --}}
            <tr style="height: 27px">
                <td class="s21" colspan="11">DIFERENCIAS DE CAJA</td>
            </tr>
            
            <tr style="height: 1px">
                <td colspan="11"></td>
            </tr>
            
            {{-- MERCADO PAGO --}}
            <tr style="height: 24px">
                <td class="s23" colspan="11">MERCADO PAGO</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s3">MercadoPago Real</td>
                <td></td>
                <td class="s3">Real No Conciliado</td>
                <td></td>
                <td class="s10">MercadoPago Sistema</td>
                <td></td>
                <td class="s10">Sistema No Conciliado</td>
                <td colspan="2"></td>
                <td class="s10">Diferencia</td>
                <td class="s28">%</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s29">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['real'] }}</td>
                <td class="s22"></td>
                <td class="s30">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['real_no_conciliado'] }}</td>
                <td class="s22"></td>
                <td class="s30">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['sistema'] }}</td>
                <td class="s22"></td>
                <td class="s31">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['sistema_no_conciliado'] }}</td>
                <td colspan="2" class="s32"></td>
                <td class="s22">${{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['diferencia'] }}</td>
                <td class="s33">{{ $data['enviar_sucursal']['diferencias_caja']['mercado_pago']['porcentaje'] }}%</td>
            </tr>
            
            {{-- GETNET --}}
            <tr style="height: 31px">
                <td class="s23" colspan="11">GETNET</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s3">Getnet Real</td>
                <td></td>
                <td class="s3">Real No Conciliado</td>
                <td></td>
                <td class="s10">Getnet Sistema</td>
                <td></td>
                <td class="s10">Sistema No Concilicado</td>
                <td colspan="2"></td>
                <td class="s10">Diferencia</td>
                <td class="s28">%</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s29">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['real'] }}</td>
                <td class="s22"></td>
                <td class="s30">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['real_no_conciliado'] }}</td>
                <td class="s22"></td>
                <td class="s29">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['sistema'] }}</td>
                <td class="s22"></td>
                <td class="s30">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['sistema_no_conciliado'] }}</td>
                <td colspan="2" class="s22"></td>
                <td class="s30">${{ $data['enviar_sucursal']['diferencias_caja']['getnet']['diferencia'] }}</td>
                <td class="s35">{{ $data['enviar_sucursal']['diferencias_caja']['getnet']['porcentaje'] }}%</td>
            </tr>
            
            {{-- EFECTIVO --}}
            <tr style="height: 29px">
                <td class="s23" colspan="11">EFECTIVO</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s3">Apertura Caja</td>
                <td></td>
                <td class="s10">Efectivo Real</td>
                <td></td>
                <td class="s10">Pagos</td>
                <td></td>
                <td class="s10">Recuento Real</td>
                <td colspan="2"></td>
                <td class="s10">Diferencia</td>
                <td class="s28">%</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s22">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['apertura_caja'] }}</td>
                <td class="s22"></td>
                <td class="s29">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['efectivo_real'] }}</td>
                <td class="s22"></td>
                <td class="s29">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['pagos'] }}</td>
                <td class="s22"></td>
                <td class="s31">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['recuento_real'] }}</td>
                <td colspan="2" class="s32"></td>
                <td class="s22">${{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['diferencia'] }}</td>
                <td class="s36">{{ $data['enviar_sucursal']['diferencias_caja']['efectivo']['porcentaje'] }}%</td>
            </tr>
            
            {{-- CTA CTE --}}
            <tr style="height: 27px">
                <td class="s23" colspan="11">CTA CTE</td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s3">CtaCte Sistema</td>
                <td colspan="3"></td>
                <td class="s10">Conciliado Sistema</td>
                <td></td>
                <td class="s10">CtaCte Real</td>
                <td colspan="4"></td>
            </tr>
            
            <tr style="height: 18px">
                <td class="s30">${{ $data['enviar_sucursal']['diferencias_caja']['cta_cte']['sistema'] }}</td>
                <td colspan="3" class="s22"></td>
                <td class="s30">${{ $data['enviar_sucursal']['diferencias_caja']['cta_cte']['conciliado_sistema'] }}</td>
                <td class="s22"></td>
                <td class="s30">${{ $data['enviar_sucursal']['diferencias_caja']['cta_cte']['real'] }}</td>
                <td colspan="4" class="s22"></td>
            </tr>
            
            <tr style="height: 18px">
                <td colspan="11"></td>
            </tr>
            
            {{-- FACTURACIÓN --}}
            <tr style="height: 18px">
                <td class="s5" colspan="8">TOTAL DE VENTAS POR HORA</td>
                <td class="s5" colspan="3">FACTURACIÓN IDEAL</td>
            </tr>
            
            <tr style="height: 18px">
                <td colspan="8" rowspan="5" style="text-align: center; vertical-align: middle;">
                    {{-- Aquí iría el gráfico de barras (opcional) --}}
                    <div style="padding: 20px; background: #f0f0f0; text-align: center;">
                        <strong>Gráfico de Ventas por Hora</strong><br>
                        @foreach($data['enviar_sucursal']['ventas_por_hora'] as $venta)
                            {{ $venta['hora'] }}: ${{ number_format($venta['monto'], 0, ',', '.') }}<br>
                        @endforeach
                    </div>
                </td>
                <td class="s38" colspan="3" rowspan="5" style="vertical-align: top; padding: 10px;">
                    <strong>FACTURACIÓN REAL</strong> ${{ $data['enviar_sucursal']['facturacion']['real'] }}<br>
                    <strong>FACTURACIÓN IDEAL</strong> ${{ $data['enviar_sucursal']['facturacion']['ideal'] }}
                </td>
            </tr>
            
            <tr style="height: 18px"></tr>
            <tr style="height: 18px"></tr>
            <tr style="height: 18px"></tr>
            <tr style="height: 58px"></tr>
            
            <tr style="height: 18px">
                <td colspan="2" class="s39">DIFERENCIA</td>
                <td colspan="2" class="s40">${{ $data['enviar_sucursal']['facturacion']['diferencia'] }}</td>
                <td colspan="7"></td>
            </tr>
            
            <tr style="height: 18px">
                <td colspan="11"></td>
            </tr>
            
            <tr style="height: 18px">
                <td colspan="2" class="s39">% DESVIO</td>
                <td colspan="2" class="s40">{{ $data['enviar_sucursal']['facturacion']['desvio_porcentaje'] }}%</td>
                <td colspan="7"></td>
            </tr>
        </tbody>
    </table>
</div>
