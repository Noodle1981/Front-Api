{{-- PÁGINA 4: ENVIAR ANULACIONES - Productos Anulados --}}

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
                <td class="s4" colspan="3" rowspan="2" style="background-color: #bdbdbd; color: #1f3864;">{{ $metadata['sucursal'] }}</td>
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
                <td></td>
            </tr>
            
            {{-- ANULACIONES --}}
            <tr style="height: 31px">
                <td class="s6" colspan="9" style="background-color: #bdbdbd; color: #1f3864; font-weight: bold;">ANULACIONES</td>
                <td class="s7" style="background-color: #1f3864; color: #ffffff;">
                    @php
                        $totalAnulaciones = array_sum(array_column($data['enviar_anulaciones'], 'precio'));
                    @endphp
                    ${{ number_format($totalAnulaciones, 2, ',', '.') }}
                </td>
            </tr>
            
            <tr style="height: 1px">
                <td colspan="10"></td>
            </tr>
            
            {{-- HEADER DE TABLA --}}
            <tr style="height: 28px">
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff;">ID Comanda</td>
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff;">Camarero Mesa</td>
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff;">Producto</td>
                <td class="s10" colspan="2" style="background-color: #1f3864; color: #ffffff;">Comentario</td>
                <td class="s11" style="background-color: #1f3864; color: #ffffff;">Hora Anulación</td>
                <td class="s10" style="background-color: #1f3864; color: #ffffff;">Precio</td>
            </tr>
            
            {{-- Filas dinámicas de anulaciones --}}
            @php
                $maxRows = 10; // Número fijo de filas para mantener layout consistente
                $itemCount = count($data['enviar_anulaciones']);
            @endphp
            
            @foreach($data['enviar_anulaciones'] as $anulacion)
            <tr style="height: 19px">
                <td class="s12" colspan="2" style="background-color: #ffffff; color: #000000;">{{ $anulacion['id_comanda'] }}</td>
                <td class="s12" colspan="2" style="background-color: #ffffff; color: #000000;">{{ $anulacion['camarero_mesa'] }}</td>
                <td class="s12" colspan="2" style="background-color: #ffffff; color: #000000;">{{ $anulacion['producto'] }}</td>
                <td class="s12" colspan="2" style="background-color: #ffffff; color: #000000;">{{ $anulacion['comentario'] }}</td>
                <td class="s12" style="background-color: #ffffff; color: #000000;">{{ $anulacion['hora_anulacion'] }}</td>
                <td class="s13" style="background-color: #ffffff; color: #000000;">${{ $anulacion['precio'] }}</td>
            </tr>
            @endforeach
            
            {{-- Filas vacías para completar el layout --}}
            @for($i = $itemCount; $i < $maxRows; $i++)
            <tr style="height: 19px">
                <td class="s12" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                <td class="s12" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                <td class="s12" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                <td class="s12" colspan="2" style="background-color: #ffffff; color: #000000;"></td>
                <td class="s12" style="background-color: #ffffff; color: #000000;"></td>
                <td class="s12" style="background-color: #ffffff; color: #000000;"></td>
            </tr>
            @endfor
        </tbody>
    </table>
</div>
