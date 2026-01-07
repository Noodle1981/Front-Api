{{-- PÁGINA 3: ENVIAR NO CONCILIADOS - Transacciones Sin Conciliar --}}

<div class="ritz grid-container" dir="ltr">
    <table class="waffle" cellspacing="0" cellpadding="0">
        <tbody>
            <tr style="height: 19px">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            
            {{-- HEADER: FECHA, TURNO, HORARIOS --}}
            <tr style="height: 26px">
                <td class="s0" rowspan="2" style="background-color: #bdbdbd; color: #1f3864;">FECHA</td>
                <td class="s1" rowspan="2" style="background-color: #1f3864; color: #ffffff;">{{ $metadata['fecha'] }}</td>
                <td class="s0" rowspan="2" style="background-color: #bdbdbd; color: #1f3864;">TURNO</td>
                <td class="s2" rowspan="2" style="background-color: #1f3864; color: #ffffff;">{{ $metadata['turno'] }}</td>
                <td class="s3" style="background-color: #bdbdbd; color: #1f3864;">HS APERTURA</td>
                <td class="s2" style="background-color: #1f3864; color: #ffffff;">{{ $metadata['hs_apertura'] }}</td>
                <td class="s4" colspan="2" rowspan="2" style="background-color: #bdbdbd; color: #1f3864;">{{ $metadata['sucursal'] }}</td>
                <td class="s5 logo-container" rowspan="2">
                    <img src="{{ $logo_base64 }}" alt="Logo" class="logo-img">
                </td>
            </tr>
            
            <tr style="height: 26px">
                <td class="s3" style="background-color: #bdbdbd; color: #1f3864;">HS CIERRE</td>
                <td class="s2" style="background-color: #1f3864; color: #ffffff;">{{ $metadata['hs_cierre'] }}</td>
            </tr>
            
            <tr style="height: 1px">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            
            {{-- MERCADO PAGO NO CONCILIADO --}}
            <tr style="height: 19px">
                <td class="s6" colspan="8" style="background-color: #bdbdbd; color: #1f3864; font-size: 14pt; font-weight: bold;">MERCADO PAGO NO CONCILIADO</td>
                <td class="s7" style="background-color: #1f3864; color: #ffffff;">${{ $data['enviar_no_conciliados']['mercado_pago']['total_no_conciliado'] }}</td>
            </tr>
            
            <tr style="height: 1px">
                <td colspan="9"></td>
            </tr>
            
            <tr style="height: 40px">
                <td class="s0" colspan="4" style="background-color: #bdbdbd; color: #1f3864;">TOTAL REAL NO CONCILIADO</td>
                <td class="s7" style="background-color: #1f3864; color: #ffffff;">${{ $data['enviar_no_conciliados']['mercado_pago']['total_real_no_conciliado'] }}</td>
                <td class="s0" colspan="3" style="background-color: #bdbdbd; color: #1f3864;">TOTAL SISTEMA NO CONCILIADO</td>
                <td class="s7" style="background-color: #1f3864; color: #ffffff;">${{ $data['enviar_no_conciliados']['mercado_pago']['total_sistema_no_conciliado'] }}</td>
            </tr>
            
            <tr style="height: 3px">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            
            <tr style="height: 19px">
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff; text-align: center;">ID de Venta</td>
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff; text-align: center;">Hora</td>
                <td class="s10" style="background-color: #1f3864; color: #ffffff; text-align: center;">Monto</td>
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff; text-align: center;">ID de Venta</td>
                <td class="s10" style="background-color: #1f3864; color: #ffffff; text-align: center;">Hora</td>
                <td class="s10" style="background-color: #1f3864; color: #ffffff; text-align: center;">Monto</td>
            </tr>
            
            {{-- Filas dinámicas Mercado Pago --}}
            @php
                $maxRows = 7;
                $realCount = count($data['enviar_no_conciliados']['mercado_pago']['items_real']);
                $sistemaCount = count($data['enviar_no_conciliados']['mercado_pago']['items_sistema']);
                $maxItems = max($realCount, $sistemaCount, $maxRows);
            @endphp
            
            @for($i = 0; $i < $maxItems; $i++)
            <tr style="height: 19px">
                @if(isset($data['enviar_no_conciliados']['mercado_pago']['items_real'][$i]))
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000; font-size: 10pt;">{{ $data['enviar_no_conciliados']['mercado_pago']['items_real'][$i]['id_venta'] }}</td>
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000; font-size: 10pt;">{{ $data['enviar_no_conciliados']['mercado_pago']['items_real'][$i]['hora'] }}</td>
                    <td class="s12" style="background-color: #ffffff; color: #000000; font-size: 10pt;">${{ $data['enviar_no_conciliados']['mercado_pago']['items_real'][$i]['monto'] }}</td>
                @else
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                    <td class="s11" style="background-color: #ffffff; color: #000000;"></td>
                @endif
                
                @if(isset($data['enviar_no_conciliados']['mercado_pago']['items_sistema'][$i]))
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000; font-size: 10pt;">{{ $data['enviar_no_conciliados']['mercado_pago']['items_sistema'][$i]['id_venta'] }}</td>
                    <td class="s13" style="background-color: #ffffff; color: #000000; font-size: 10pt;">{{ $data['enviar_no_conciliados']['mercado_pago']['items_sistema'][$i]['hora'] }}</td>
                    <td class="s13" style="background-color: #ffffff; color: #000000; font-size: 10pt;">${{ $data['enviar_no_conciliados']['mercado_pago']['items_sistema'][$i]['monto'] }}</td>
                @else
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                    <td class="s11" style="background-color: #ffffff; color: #000000;"></td>
                    <td class="s11" style="background-color: #ffffff; color: #000000;"></td>
                @endif
            </tr>
            @endfor
            
            <tr style="height: 1px">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            
            {{-- GETNET NO CONCILIADO --}}
            <tr style="height: 19px">
                <td class="s6" colspan="8" style="background-color: #bdbdbd; color: #1f3864; font-size: 14pt; font-weight: bold;">GETNET NO CONCILIADO</td>
                <td class="s7" style="background-color: #1f3864; color: #ffffff;">${{ $data['enviar_no_conciliados']['getnet']['total_no_conciliado'] }}</td>
            </tr>
            
            <tr style="height: 1px">
                <td colspan="9"></td>
            </tr>
            
            <tr style="height: 19px">
                <td class="s0" colspan="4" style="background-color: #bdbdbd; color: #1f3864;">TOTAL REAL NO CONCILIADO</td>
                <td class="s7" style="background-color: #1f3864; color: #ffffff;">${{ $data['enviar_no_conciliados']['getnet']['total_real_no_conciliado'] }}</td>
                <td class="s0" colspan="3" style="background-color: #bdbdbd; color: #1f3864;">TOTAL SISTEMA NO CONCILIADO</td>
                <td class="s7" style="background-color: #1f3864; color: #ffffff;">${{ $data['enviar_no_conciliados']['getnet']['total_sistema_no_conciliado'] }}</td>
            </tr>
            
            <tr style="height: 1px">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            
            <tr style="height: 19px">
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff; text-align: center;">ID de Venta</td>
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff; text-align: center;">Hora</td>
                <td class="s10" style="background-color: #1f3864; color: #ffffff; text-align: center;">Monto</td>
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff; text-align: center;">ID de Venta</td>
                <td class="s10" style="background-color: #1f3864; color: #ffffff; text-align: center;">Hora</td>
                <td class="s10" style="background-color: #1f3864; color: #ffffff; text-align: center;">Monto</td>
            </tr>
            
            {{-- Filas dinámicas Getnet --}}
            @php
                $realCountGN = count($data['enviar_no_conciliados']['getnet']['items_real']);
                $sistemaCountGN = count($data['enviar_no_conciliados']['getnet']['items_sistema']);
                $maxItemsGN = max($realCountGN, $sistemaCountGN, $maxRows);
            @endphp
            
            @for($i = 0; $i < $maxItemsGN; $i++)
            <tr style="height: 19px">
                @if(isset($data['enviar_no_conciliados']['getnet']['items_real'][$i]))
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000; font-size: 10pt;">{{ $data['enviar_no_conciliados']['getnet']['items_real'][$i]['id_venta'] }}</td>
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000; font-size: 10pt;">{{ $data['enviar_no_conciliados']['getnet']['items_real'][$i]['hora'] }}</td>
                    <td class="s12" style="background-color: #ffffff; color: #000000; font-size: 10pt;">${{ $data['enviar_no_conciliados']['getnet']['items_real'][$i]['monto'] }}</td>
                @else
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                    <td class="s11" style="background-color: #ffffff; color: #000000;"></td>
                @endif
                
                @if(isset($data['enviar_no_conciliados']['getnet']['items_sistema'][$i]))
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000; font-size: 10pt;">{{ $data['enviar_no_conciliados']['getnet']['items_sistema'][$i]['id_venta'] }}</td>
                    <td class="s13" style="background-color: #ffffff; color: #000000; font-size: 10pt;">{{ $data['enviar_no_conciliados']['getnet']['items_sistema'][$i]['hora'] }}</td>
                    <td class="s13" style="background-color: #ffffff; color: #000000; font-size: 10pt;">${{ $data['enviar_no_conciliados']['getnet']['items_sistema'][$i]['monto'] }}</td>
                @else
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                    <td class="s11" style="background-color: #ffffff; color: #000000;"></td>
                    <td class="s11" style="background-color: #ffffff; color: #000000;"></td>
                @endif
            </tr>
            @endfor
            
            <tr style="height: 1px">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            
            {{-- EFECTIVO Y CTA CTE CONCILIADO --}}
            <tr style="height: 19px">
                <td class="s6" colspan="8" style="background-color: #bdbdbd; color: #1f3864; font-size: 14pt; font-weight: bold;">EFECTIVO Y CTA CTE CONCILIADO</td>
                <td class="s7" style="background-color: #1f3864; color: #ffffff;">${{ $data['enviar_no_conciliados']['efectivo_cta_cte']['total_no_conciliado'] }}</td>
            </tr>
            
            <tr style="height: 1px">
                <td colspan="9"></td>
            </tr>
            
            <tr style="height: 19px">
                <td class="s0" colspan="4" style="background-color: #bdbdbd; color: #1f3864;">TOTAL REAL NO CONCILIADO</td>
                <td class="s7" style="background-color: #1f3864; color: #ffffff;">${{ $data['enviar_no_conciliados']['efectivo_cta_cte']['total_real_no_conciliado'] }}</td>
                <td class="s0" colspan="3" style="background-color: #bdbdbd; color: #1f3864;">TOTAL SISTEMA NO CONCILIADO</td>
                <td class="s7" style="background-color: #1f3864; color: #ffffff;">${{ $data['enviar_no_conciliados']['efectivo_cta_cte']['total_sistema_no_conciliado'] }}</td>
            </tr>
            
            <tr style="height: 1px">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="s14"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            
            <tr style="height: 19px">
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff; text-align: center;">ID de Venta</td>
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff; text-align: center;">Hora</td>
                <td class="s10" style="background-color: #1f3864; color: #ffffff; text-align: center;">Monto</td>
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff; text-align: center;">ID de Venta</td>
                <td class="s10" style="background-color: #1f3864; color: #ffffff; text-align: center;">Hora</td>
                <td class="s10" style="background-color: #1f3864; color: #ffffff; text-align: center;">Monto</td>
            </tr>
            
            {{-- Filas dinámicas Efectivo/Cta Cte --}}
            @php
                $realCountEF = count($data['enviar_no_conciliados']['efectivo_cta_cte']['items_real']);
                $sistemaCountEF = count($data['enviar_no_conciliados']['efectivo_cta_cte']['items_sistema']);
                $maxItemsEF = max($realCountEF, $sistemaCountEF, $maxRows);
            @endphp
            
            @for($i = 0; $i < $maxItemsEF; $i++)
            <tr style="height: 19px">
                @if(isset($data['enviar_no_conciliados']['efectivo_cta_cte']['items_real'][$i]))
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000; font-size: 10pt;">{{ $data['enviar_no_conciliados']['efectivo_cta_cte']['items_real'][$i]['id_venta'] }}</td>
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000; font-size: 10pt;">{{ $data['enviar_no_conciliados']['efectivo_cta_cte']['items_real'][$i]['hora'] }}</td>
                    <td class="s12" style="background-color: #ffffff; color: #000000; font-size: 10pt;">${{ $data['enviar_no_conciliados']['efectivo_cta_cte']['items_real'][$i]['monto'] }}</td>
                @else
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                    <td class="s11" style="background-color: #ffffff; color: #000000;"></td>
                @endif
                
                @if(isset($data['enviar_no_conciliados']['efectivo_cta_cte']['items_sistema'][$i]))
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000; font-size: 10pt;">{{ $data['enviar_no_conciliados']['efectivo_cta_cte']['items_sistema'][$i]['id_venta'] }}</td>
                    <td class="s13" style="background-color: #ffffff; color: #000000; font-size: 10pt;">{{ $data['enviar_no_conciliados']['efectivo_cta_cte']['items_sistema'][$i]['hora'] }}</td>
                    <td class="s13" style="background-color: #ffffff; color: #000000; font-size: 10pt;">${{ $data['enviar_no_conciliados']['efectivo_cta_cte']['items_sistema'][$i]['monto'] }}</td>
                @else
                    <td class="s11" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                    <td class="s11" style="background-color: #ffffff; color: #000000;"></td>
                    <td class="s11" style="background-color: #ffffff; color: #000000;"></td>
                @endif
            </tr>
            @endfor
        </tbody>
    </table>
</div>
