{{-- PÁGINA 4: ENVIAR ANULACIONES - Productos y Ventas Anuladas --}}

<div class="ritz grid-container" dir="ltr">
    <table class="waffle" cellspacing="0" cellpadding="0">
        <tbody>
            {{-- HEADER --}}
            <tr style="height: 19px">
                <td colspan="10"></td>
            </tr>
            
            <tr style="height: 26px">
                <td class="s0" rowspan="3">FECHA</td>
                <td class="s1" rowspan="3"></td>
                <td class="s0" rowspan="3">TURNO</td>
                <td class="s1" rowspan="3"></td>
                <td class="s2">HS APERTURA</td>
                <td class="s1"> </td>
                <td class="s3 parador-title" colspan="3" rowspan="3">{{ $metadata['sucursal'] }}</td>
                <td class="s4" rowspan="3"></td>
            </tr>
            
            <tr style="height: 6px">
                <td class="s4" colspan="2"> </td>
            </tr>
            
            <tr style="height: 26px">
                <td class="s2">HS CIERRE</td>
                <td class="s1"></td>
            </tr>
            
            <tr style="height: 1px">
                <td colspan="10"></td>
            </tr>
            
            {{-- TÍTULO: ANULACIONES --}}
            <tr style="height: 19px">
                <td class="s5" colspan="8" rowspan="2" style="font-size: 26pt;">ANULACIONES</td>
                <td class="s1" colspan="2" rowspan="2"></td>
            </tr>
            
            <tr style="height: 31px"></tr>
            
            <tr style="height: 1px">
                <td colspan="10"></td>
            </tr>
            
            {{-- HEADERS DE TABLA --}}
            <tr style="height: 28px">
                <td class="s8" colspan="2">ID Comanda</td>
                <td class="s8" colspan="2">Camarero Mesa</td>
                <td class="s8" colspan="2">Producto</td>
                <td class="s8" colspan="2">Comentario</td>
                <td class="s9">Hora Anulación</td>
                <td class="s8">Precio</td>
            </tr>
            
            {{-- FILAS DINÁMICAS DE ANULACIONES --}}
            @if(isset($data['enviar_anulaciones']) && count($data['enviar_anulaciones']) > 0)
                @foreach($data['enviar_anulaciones'] as $anulacion)
                <tr style="height: 19px">
                    <td class="s10" colspan="2">{{ $anulacion['id_comanda'] }}</td>
                    <td class="s10" colspan="2">{{ $anulacion['camarero_mesa'] }}</td>
                    <td class="s10" colspan="2">{{ $anulacion['producto'] }}</td>
                    <td class="s10" colspan="2">{{ $anulacion['comentario'] }}</td>
                    <td class="s10">{{ $anulacion['hora_anulacion'] }}</td>
                    <td class="s10">${{ $anulacion['precio'] }}</td>
                </tr>
                @endforeach
                
                {{-- Agregar filas vacías hasta completar al menos 10 filas --}}
                @for($i = count($data['enviar_anulaciones']); $i < 10; $i++)
                <tr style="height: 19px">
                    <td class="s10" colspan="2"></td>
                    <td class="s10" colspan="2"></td>
                    <td class="s10" colspan="2"></td>
                    <td class="s10" colspan="2"></td>
                    <td class="s10"></td>
                    <td class="s10"></td>
                </tr>
                @endfor
            @else
                {{-- Si no hay anulaciones, mostrar 10 filas vacías --}}
                @for($i = 0; $i < 10; $i++)
                <tr style="height: 19px">
                    <td class="s10" colspan="2"></td>
                    <td class="s10" colspan="2"></td>
                    <td class="s10" colspan="2"></td>
                    <td class="s10" colspan="2"></td>
                    <td class="s10"></td>
                    <td class="s10"></td>
                </tr>
                @endfor
            @endif
        </tbody>
    </table>
</div>

{{-- Logo del parador (si existe) --}}
@if(isset($metadata['logo_base64']))
<div style="text-align: center; margin-top: 20px;">
    <img src="{{ $metadata['logo_base64'] }}" style="width: 175px; height: 83px;" alt="Logo {{ $metadata['sucursal'] }}">
</div>
@endif
