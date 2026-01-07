{{-- PÁGINA 2: ENVIAR EGRESOS - Detalle de Egresos del Día --}}

<div class="ritz grid-container" dir="ltr">
    <table class="waffle" cellspacing="0" cellpadding="0">
        <tbody>
            {{-- HEADER --}}
            <tr style="height: 19px">
                <td colspan="12"></td>
            </tr>
            
            <tr style="height: 26px">
                <td class="s0" rowspan="3">FECHA</td>
                <td class="s1" rowspan="3"></td>
                <td class="s0" rowspan="3">TURNO</td>
                <td class="s1" rowspan="3"></td>
                <td class="s2">HS APERTURA</td>
                <td class="s1"> </td>
                <td class="s3"></td>
                <td class="s3 parador-title" colspan="3" rowspan="3">{{ $metadata['sucursal'] }}</td>
                <td class="s4" colspan="3" rowspan="3"></td>
            </tr>
            
            <tr style="height: 6px">
                <td class="s4" colspan="3"> </td>
            </tr>
            
            <tr style="height: 26px">
                <td class="s2">HS CIERRE</td>
                <td class="s1"></td>
                <td class="s3"></td>
            </tr>
            
            <tr style="height: 1px">
                <td colspan="12"></td>
            </tr>
            
            {{-- TÍTULO: EGRESOS CAJA ADICIÓN --}}
            <tr style="height: 19px">
                <td class="s5" colspan="9" rowspan="2" style="font-size: 26pt;">EGRESOS CAJA ADICIÓN</td>
                <td class="s1" colspan="3" rowspan="2"></td>
            </tr>
            
            <tr style="height: 19px"></tr>
            
            <tr style="height: 1px">
                <td colspan="12"></td>
            </tr>
            
            {{-- HEADERS DE TABLA --}}
            <tr style="height: 19px">
                <td class="s7" colspan="2">IMPORTE</td>
                <td class="s7" colspan="2">HORA</td>
                <td class="s7" colspan="8">DETALLE</td>
            </tr>
            
            {{-- FILAS DINÁMICAS DE EGRESOS CAJA ADICIÓN --}}
            @if(isset($data['enviar_egresos']['caja_adicion']) && count($data['enviar_egresos']['caja_adicion']) > 0)
                @foreach($data['enviar_egresos']['caja_adicion'] as $egreso)
                <tr style="height: 19px">
                    <td class="s8" colspan="2">${{ $egreso['importe'] }}</td>
                    <td class="s8" colspan="2">{{ $egreso['hora'] }}</td>
                    <td class="s8" colspan="8">{{ $egreso['detalle'] }}</td>
                </tr>
                @endforeach
            @else
                {{-- Filas vacías si no hay datos --}}
                @for($i = 0; $i < 10; $i++)
                <tr style="height: 19px">
                    <td class="s8" colspan="2"></td>
                    <td class="s8" colspan="2"></td>
                    <td class="s8" colspan="8"></td>
                </tr>
                @endfor
            @endif
            
            <tr style="height: 1px">
                <td colspan="12"></td>
            </tr>
            
            {{-- TÍTULO: EGRESOS MERCADO PAGO --}}
            <tr style="height: 19px">
                <td class="s5" colspan="9" rowspan="2" style="font-size: 26pt;">EGRESOS MERCADO PAGO</td>
                <td class="s1" colspan="3" rowspan="2"></td>
            </tr>
            
            <tr style="height: 19px"></tr>
            
            <tr style="height: 1px">
                <td colspan="12"></td>
            </tr>
            
            {{-- HEADERS DE TABLA --}}
            <tr style="height: 19px">
                <td class="s7" colspan="2">IMPORTE</td>
                <td class="s7" colspan="2">HORA</td>
                <td class="s7" colspan="8">DETALLE</td>
            </tr>
            
            {{-- FILAS DINÁMICAS DE EGRESOS MERCADO PAGO --}}
            @if(isset($data['enviar_egresos']['mercado_pago']) && count($data['enviar_egresos']['mercado_pago']) > 0)
                @foreach($data['enviar_egresos']['mercado_pago'] as $egreso)
                <tr style="height: 19px">
                    <td class="s8" colspan="2">${{ $egreso['importe'] }}</td>
                    <td class="s8" colspan="2">{{ $egreso['hora'] }}</td>
                    <td class="s8" colspan="8">{{ $egreso['detalle'] }}</td>
                </tr>
                @endforeach
            @else
                {{-- Filas vacías si no hay datos --}}
                @for($i = 0; $i < 10; $i++)
                <tr style="height: 19px">
                    <td class="s8" colspan="2"></td>
                    <td class="s8" colspan="2"></td>
                    <td class="s8" colspan="8"></td>
                </tr>
                @endfor
            @endif
        </tbody>
    </table>
</div>

{{-- Logo del parador (si existe) --}}
@if(isset($metadata['logo_base64']))
<div style="text-align: center; margin-top: 20px;">
    <img src="{{ $metadata['logo_base64'] }}" style="width: 128px; height: 61px;" alt="Logo">
</div>
@endif
