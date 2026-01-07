{{-- PÁGINA 2: ENVIAR EGRESOS - Detalle de Egresos --}}

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
            <tr style="height: 31px">
                <td class="s0" rowspan="2" style="background-color: #bdbdbd; color: #1f3864;">FECHA</td>
                <td class="s1" rowspan="2" style="background-color: #1f3864; color: #ffffff;">{{ $metadata['fecha'] }}</td>
                <td class="s0" rowspan="2" style="background-color: #bdbdbd; color: #1f3864;">TURNO</td>
                <td class="s2" rowspan="2" style="background-color: #1f3864; color: #ffffff;">{{ $metadata['turno'] }}</td>
                <td class="s3" style="background-color: #bdbdbd; color: #1f3864;">HS APERTURA</td>
                <td class="s2" style="background-color: #1f3864; color: #ffffff;">{{ $metadata['hs_apertura'] }}</td>
                <td class="s4" colspan="2" rowspan="2" style="background-color: #bdbdbd; color: #1f3864;">{{ $metadata['sucursal'] }}</td>
                <td class="s5 logo-container" rowspan="2">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo-img">
                </td>
            </tr>
            
            <tr style="height: 33px">
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
            
            {{-- EGRESOS CAJA ADICIÓN --}}
            <tr style="height: 57px">
                <td class="s6" colspan="8" style="background-color: #bdbdbd; color: #1f3864; font-size: 14pt; font-weight: bold;">EGRESOS CAJA ADICIÓN</td>
                <td class="s7" style="background-color: #1f3864; color: #ffffff;">${{ $data['enviar_egresos']['total_caja_adicion'] }}</td>
            </tr>
            
            <tr style="height: 1px">
                <td class="s8" colspan="9"></td>
            </tr>
            
            <tr style="height: 19px">
                <td class="s9" colspan="2" style="background-color: #1f3864; color: #ffffff; font-size: 10pt;">IMPORTE</td>
                <td class="s9" colspan="2" style="background-color: #1f3864; color: #ffffff; font-size: 10pt;">HORA</td>
                <td class="s9" colspan="5" style="background-color: #1f3864; color: #ffffff; font-size: 10pt;">DETALLE</td>
            </tr>
            
            {{-- Filas dinámicas de egresos caja adición --}}
            @php
                $maxRows = 10; // Número fijo de filas para mantener layout consistente
                $itemCount = count($data['enviar_egresos']['caja_adicion']);
            @endphp
            
            @foreach($data['enviar_egresos']['caja_adicion'] as $egreso)
            <tr style="height: 19px">
                <td class="s10" colspan="2" style="background-color: #ffffff; color: #000000;">${{ $egreso['importe'] }}</td>
                <td class="s10" colspan="2" style="background-color: #ffffff; color: #000000;">{{ $egreso['hora'] }}</td>
                <td class="s10" colspan="5" style="background-color: #ffffff; color: #000000;">{{ $egreso['detalle'] }}</td>
            </tr>
            @endforeach
            
            {{-- Filas vacías para completar el layout --}}
            @for($i = $itemCount; $i < $maxRows; $i++)
            <tr style="height: 19px">
                <td class="s10" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                <td class="s10" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                <td class="s10" colspan="5" style="background-color: #ffffff; color: #000000;"></td>
            </tr>
            @endfor
            
            <tr style="height: 1px">
                <td colspan="9" style="background-color: #ffffff;"></td>
            </tr>
            
            {{-- EGRESOS MERCADO PAGO --}}
            <tr style="height: 19px">
                <td class="s6" colspan="8" style="background-color: #bdbdbd; color: #1f3864; font-size: 14pt; font-weight: bold;">EGRESOS MERCADO PAGO</td>
                <td class="s7" style="background-color: #1f3864; color: #ffffff;">${{ $data['enviar_egresos']['total_mercado_pago'] }}</td>
            </tr>
            
            <tr style="height: 1px">
                <td class="s8" colspan="9"></td>
            </tr>
            
            <tr style="height: 19px">
                <td class="s9" colspan="2" style="background-color: #1f3864; color: #ffffff; font-size: 10pt;">IMPORTE</td>
                <td class="s9" colspan="2" style="background-color: #1f3864; color: #ffffff; font-size: 10pt;">HORA</td>
                <td class="s9" colspan="5" style="background-color: #1f3864; color: #ffffff; font-size: 10pt;">DETALLE</td>
            </tr>
            
            {{-- Filas dinámicas de egresos mercado pago --}}
            @php
                $itemCountMP = count($data['enviar_egresos']['mercado_pago']);
            @endphp
            
            @foreach($data['enviar_egresos']['mercado_pago'] as $egreso)
            <tr style="height: 19px">
                <td class="s10" colspan="2" style="background-color: #ffffff; color: #000000;">${{ $egreso['importe'] }}</td>
                <td class="s10" colspan="2" style="background-color: #ffffff; color: #000000;">{{ $egreso['hora'] }}</td>
                <td class="s10" colspan="5" style="background-color: #ffffff; color: #000000;">{{ $egreso['detalle'] }}</td>
            </tr>
            @endforeach
            
            {{-- Filas vacías para completar el layout --}}
            @for($i = $itemCountMP; $i < $maxRows; $i++)
            <tr style="height: 19px">
                <td class="s10" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                <td class="s10" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                <td class="s10" colspan="5" style="background-color: #ffffff; color: #000000;"></td>
            </tr>
            @endfor
        </tbody>
    </table>
</div>
