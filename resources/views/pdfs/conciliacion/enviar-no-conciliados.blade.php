{{-- PÁGINA 3: ENVIAR NO CONCILIADOS - Transacciones Sin Conciliar --}}

<div class="ritz grid-container" dir="ltr">
    <table class="waffle" cellspacing="0" cellpadding="0">
        <tbody>
            {{-- HEADER --}}
            <tr style="height: 19px">
                <td colspan="11"></td>
            </tr>
            
            <tr style="height: 26px">
                <td class="s0" rowspan="3">FECHA</td>
                <td class="s1" rowspan="3"></td>
                <td class="s0" rowspan="3">TURNO</td>
                <td class="s1" rowspan="3"></td>
                <td class="s2">HS APERTURA</td>
                <td class="s2"></td>
                <td class="s1"> </td>
                <td class="s3 parador-title" colspan="3" rowspan="3">{{ $metadata['sucursal'] }}</td>
                <td class="s4" rowspan="3"></td>
            </tr>
            
            <tr style="height: 6px">
                <td class="s4" colspan="3"> </td>
            </tr>
            
            <tr style="height: 26px">
                <td class="s2">HS CIERRE</td>
                <td class="s2"></td>
                <td class="s1"></td>
            </tr>
            
            <tr style="height: 1px">
                <td colspan="11"></td>
            </tr>
            
            {{-- MERCADO PAGO NO CONCILIADO --}}
            <tr style="height: 19px">
                <td class="s5" colspan="9" rowspan="2" style="font-size: 26pt;">MERCADO PAGO NO CONCILIADO</td>
                <td class="s1" colspan="2" rowspan="2"></td>
            </tr>
            
            <tr style="height: 19px"></tr>
            
            <tr style="height: 1px">
                <td colspan="11"></td>
            </tr>
            
            {{-- TOTALES --}}
            <tr style="height: 31px">
                <td class="s0" colspan="4">TOTAL REAL NO CONCILIADO</td>
                <td class="s1"></td>
                <td></td>
                <td class="s0" colspan="3">TOTAL SISTEMA NO CONCILIADO</td>
                <td class="s0"></td>
                <td class="s1"></td>
            </tr>
            
            <tr style="height: 1px">
                <td colspan="11"></td>
            </tr>
            
            {{-- HEADERS --}}
            <tr style="height: 19px">
                <td class="s8" colspan="2">ID de Venta</td>
                <td class="s8" colspan="2">Hora</td>
                <td class="s8">Monto</td>
                <td class="s9"></td>
                <td class="s8" colspan="2">ID de Venta</td>
                <td class="s8" colspan="2">Hora</td>
                <td class="s8">Monto</td>
            </tr>
            
            {{-- FILAS DINÁMICAS - MERCADO PAGO --}}
            @php
                $maxRows = max(
                    count($data['enviar_no_conciliados']['mercado_pago']['items_real'] ?? []),
                    count($data['enviar_no_conciliados']['mercado_pago']['items_sistema'] ?? []),
                    5 // Mínimo 5 filas
                );
            @endphp
            
            @for($i = 0; $i < $maxRows; $i++)
                @php
                    $itemReal = $data['enviar_no_conciliados']['mercado_pago']['items_real'][$i] ?? null;
                    $itemSistema = $data['enviar_no_conciliados']['mercado_pago']['items_sistema'][$i] ?? null;
                @endphp
                <tr style="height: 19px">
                    <td class="s10" colspan="2">{{ $itemReal['id_venta'] ?? ' ' }}</td>
                    <td class="s10" colspan="2">{{ $itemReal['hora'] ?? ' ' }}</td>
                    <td class="s10">{{ $itemReal ? '$' . $itemReal['monto'] : ' ' }}</td>
                    <td class="s11"></td>
                    <td class="s12" colspan="2">{{ $itemSistema['id_venta'] ?? ' ' }}</td>
                    <td class="s10" colspan="2">{{ $itemSistema['hora'] ?? ' ' }}</td>
                    <td class="s10">{{ $itemSistema ? '$' . $itemSistema['monto'] : ' ' }}</td>
                </tr>
            @endfor
            
            <tr style="height: 1px">
                <td colspan="11"></td>
            </tr>
            
            {{-- GETNET NO CONCILIADO --}}
            <tr style="height: 19px">
                <td class="s5" colspan="9" rowspan="2" style="font-size: 26pt;">GETNET NO CONCILIADO</td>
                <td class="s1" colspan="2" rowspan="2"></td>
            </tr>
            
            <tr style="height: 19px"></tr>
            
            <tr style="height: 1px">
                <td colspan="11"></td>
            </tr>
            
            {{-- TOTALES --}}
            <tr style="height: 19px">
                <td class="s0" colspan="4">TOTAL REAL NO CONCILIADO</td>
                <td class="s1"></td>
                <td></td>
                <td class="s0" colspan="3">TOTAL SISTEMA NO CONCILIADO</td>
                <td class="s0"></td>
                <td class="s1"></td>
            </tr>
            
            <tr style="height: 1px">
                <td colspan="11"></td>
            </tr>
            
            {{-- HEADERS --}}
            <tr style="height: 19px">
                <td class="s8" colspan="2">ID de Venta</td>
                <td class="s8" colspan="2">Hora</td>
                <td class="s8">Monto</td>
                <td class="s9"></td>
                <td class="s8" colspan="2">ID de Venta</td>
                <td class="s8" colspan="2">Hora</td>
                <td class="s8">Monto</td>
            </tr>
            
            {{-- FILAS DINÁMICAS - GETNET --}}
            @php
                $maxRowsGetnet = max(
                    count($data['enviar_no_conciliados']['getnet']['items_real'] ?? []),
                    count($data['enviar_no_conciliados']['getnet']['items_sistema'] ?? []),
                    5
                );
            @endphp
            
            @for($i = 0; $i < $maxRowsGetnet; $i++)
                @php
                    $itemReal = $data['enviar_no_conciliados']['getnet']['items_real'][$i] ?? null;
                    $itemSistema = $data['enviar_no_conciliados']['getnet']['items_sistema'][$i] ?? null;
                @endphp
                <tr style="height: 19px">
                    <td class="s10" colspan="2">{{ $itemReal['id_venta'] ?? ' ' }}</td>
                    <td class="s10" colspan="2">{{ $itemReal['hora'] ?? ' ' }}</td>
                    <td class="s10">{{ $itemReal ? '$' . $itemReal['monto'] : ' ' }}</td>
                    <td class="s13"></td>
                    <td class="s10" colspan="2">{{ $itemSistema['id_venta'] ?? ' ' }}</td>
                    <td class="s10" colspan="2">{{ $itemSistema['hora'] ?? ' ' }}</td>
                    <td class="s10">{{ $itemSistema ? '$' . $itemSistema['monto'] : ' ' }}</td>
                </tr>
            @endfor
            
            <tr style="height: 1px">
                <td colspan="11"></td>
            </tr>
            
            {{-- EFECTIVO Y CTA CTE CONCILIADO --}}
            <tr style="height: 19px">
                <td class="s5" colspan="9" rowspan="2" style="font-size: 26pt;">EFECTIVO Y CTA CTE CONCILIADO</td>
                <td class="s1" colspan="2" rowspan="2"></td>
            </tr>
            
            <tr style="height: 19px"></tr>
            
            <tr style="height: 1px">
                <td colspan="11"></td>
            </tr>
            
            {{-- TOTALES --}}
            <tr style="height: 19px">
                <td class="s0" colspan="4">TOTAL REAL NO CONCILIADO</td>
                <td class="s1"></td>
                <td></td>
                <td class="s0" colspan="3">TOTAL SISTEMA NO CONCILIADO</td>
                <td class="s0"></td>
                <td class="s1"></td>
            </tr>
            
            <tr style="height: 1px">
                <td colspan="11"></td>
            </tr>
            
            {{-- HEADERS --}}
            <tr style="height: 19px">
                <td class="s8" colspan="2">ID de Venta</td>
                <td class="s8" colspan="2">Hora</td>
                <td class="s8">Monto</td>
                <td class="s9"></td>
                <td class="s8" colspan="2">ID de Venta</td>
                <td class="s8" colspan="2">Hora</td>
                <td class="s8">Monto</td>
            </tr>
            
            {{-- FILAS DINÁMICAS - EFECTIVO/CTA CTE --}}
            @php
                $maxRowsEfectivo = max(
                    count($data['enviar_no_conciliados']['efectivo_cta_cte']['items_real'] ?? []),
                    count($data['enviar_no_conciliados']['efectivo_cta_cte']['items_sistema'] ?? []),
                    5
                );
            @endphp
            
            @for($i = 0; $i < $maxRowsEfectivo; $i++)
                @php
                    $itemReal = $data['enviar_no_conciliados']['efectivo_cta_cte']['items_real'][$i] ?? null;
                    $itemSistema = $data['enviar_no_conciliados']['efectivo_cta_cte']['items_sistema'][$i] ?? null;
                @endphp
                <tr style="height: 19px">
                    <td class="s10" colspan="2">{{ $itemReal['id_venta'] ?? ' ' }}</td>
                    <td class="s10" colspan="2">{{ $itemReal['hora'] ?? ' ' }}</td>
                    <td class="s10">{{ $itemReal ? '$' . $itemReal['monto'] : ' ' }}</td>
                    <td class="s11"></td>
                    <td class="s12" colspan="2">{{ $itemSistema['id_venta'] ?? ' ' }}</td>
                    <td class="s10" colspan="2">{{ $itemSistema['hora'] ?? ' ' }}</td>
                    <td class="s10">{{ $itemSistema ? '$' . $itemSistema['monto'] : ' ' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
