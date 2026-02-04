<?php

namespace App\Exports;

use App\Models\Conciliation\ConciliationSummary;
use App\Models\Conciliation\ConciliationGetnetTransaction;
use App\Models\Conciliation\ConciliationMpTransaction;
use App\Models\Conciliation\ConciliationCashMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ConciliationClientExport implements WithMultipleSheets
{
    protected int $clientId;
    protected ?string $fechaInicio;
    protected ?string $fechaFin;

    public function __construct(int $clientId, ?string $fechaInicio = null, ?string $fechaFin = null)
    {
        $this->clientId = $clientId;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function sheets(): array
    {
        return [
            new ClientSummarySheet($this->clientId, $this->fechaInicio, $this->fechaFin),
            new ClientGetnetSheet($this->clientId, $this->fechaInicio, $this->fechaFin),
            new ClientMpSheet($this->clientId, $this->fechaInicio, $this->fechaFin),
            new ClientCashMovementsSheet($this->clientId, $this->fechaInicio, $this->fechaFin),
        ];
    }
}

class ClientSummarySheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected int $clientId;
    protected ?string $fechaInicio;
    protected ?string $fechaFin;

    public function __construct(int $clientId, ?string $fechaInicio, ?string $fechaFin)
    {
        $this->clientId = $clientId;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection(): Collection
    {
        $query = ConciliationSummary::where('client_id', $this->clientId);

        if ($this->fechaInicio) {
            $query->whereDate('fecha', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('fecha', '<=', $this->fechaFin);
        }

        return $query->orderBy('fecha')->get()->map(function ($s) {
            return [
                'fecha' => $s->fecha?->format('d/m/Y'),
                'dia' => $s->dia,
                'turno' => $s->turno,
                'encargado' => $s->encargado,
                'ventas_totales' => $s->ventas_totales,
                'cantidad_comensales' => $s->cantidad_comensales,
                'cantidad_tickets' => $s->cantidad_tickets,
                'ticket_promedio' => $s->ticket_promedio,
                'mp_ventas_real' => $s->mp_ventas_real,
                'mp_conciliado' => $s->mp_conciliado,
                'mp_diferencia' => $s->mp_diferencia,
                'getnet_ventas_real' => $s->getnet_ventas_real,
                'getnet_conciliado' => $s->getnet_conciliado,
                'getnet_diferencia' => $s->getnet_diferencia,
                'efectivo_total' => $s->efectivo_total,
                'efectivo_recuento' => $s->efectivo_recuento,
                'efectivo_diferencia' => $s->efectivo_diferencia,
                'efectivo_estado' => $s->efectivo_estado,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Dia',
            'Turno',
            'Encargado',
            'Ventas Totales',
            'Comensales',
            'Tickets',
            'Ticket Promedio',
            'MP Real',
            'MP Conciliado',
            'MP Diferencia',
            'Getnet Real',
            'Getnet Conciliado',
            'Getnet Diferencia',
            'Efectivo Total',
            'Efectivo Recuento',
            'Efectivo Diferencia',
            'Estado Efectivo',
        ];
    }

    public function title(): string
    {
        return 'Resumen';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class ClientGetnetSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected int $clientId;
    protected ?string $fechaInicio;
    protected ?string $fechaFin;

    public function __construct(int $clientId, ?string $fechaInicio, ?string $fechaFin)
    {
        $this->clientId = $clientId;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection(): Collection
    {
        $query = ConciliationGetnetTransaction::where('client_id', $this->clientId);

        if ($this->fechaInicio) {
            $query->whereDate('fecha_operacion', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('fecha_operacion', '<=', $this->fechaFin);
        }

        return $query->orderBy('fecha_operacion')->get()->map(function ($t) {
            return [
                'fecha' => $t->fecha_operacion?->format('d/m/Y H:i'),
                'cod_transaccion' => $t->cod_transaccion,
                'marca' => $t->marca,
                'tipo' => $t->tipo_tarjeta,
                'tarjeta' => $t->tarjeta_ultimos4,
                'monto_bruto' => $t->monto_bruto,
                'monto_neto' => $t->monto_neto,
                'arancel' => $t->arancel,
                'estado_venta' => $t->estado_venta,
                'estado_conciliacion' => $t->estado_conciliacion,
                'tipo_match' => $t->tipo_match,
                'turno' => $t->turno,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Cod. Transaccion',
            'Marca',
            'Tipo',
            'Tarjeta',
            'Monto Bruto',
            'Monto Neto',
            'Arancel',
            'Estado Venta',
            'Estado Conciliacion',
            'Tipo Match',
            'Turno',
        ];
    }

    public function title(): string
    {
        return 'Getnet';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class ClientMpSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected int $clientId;
    protected ?string $fechaInicio;
    protected ?string $fechaFin;

    public function __construct(int $clientId, ?string $fechaInicio, ?string $fechaFin)
    {
        $this->clientId = $clientId;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection(): Collection
    {
        $query = ConciliationMpTransaction::where('client_id', $this->clientId);

        if ($this->fechaInicio) {
            $query->whereDate('fecha', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('fecha', '<=', $this->fechaFin);
        }

        return $query->orderBy('fecha')->get()->map(function ($t) {
            return [
                'fecha' => $t->fecha?->format('d/m/Y'),
                'hora' => $t->hora,
                'id_operacion' => $t->id_operacion_mp,
                'monto_neto' => $t->monto_neto,
                'medio_pago' => $t->medio_pago,
                'metodo_pago' => $t->metodo_pago,
                'estado' => $t->estado,
                'estado_conciliacion' => $t->estado_conciliacion,
                'tipo_match' => $t->tipo_match,
                'turno' => $t->turno,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Hora',
            'ID Operacion',
            'Monto Neto',
            'Medio Pago',
            'Metodo Pago',
            'Estado',
            'Estado Conciliacion',
            'Tipo Match',
            'Turno',
        ];
    }

    public function title(): string
    {
        return 'MercadoPago';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class ClientCashMovementsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected int $clientId;
    protected ?string $fechaInicio;
    protected ?string $fechaFin;

    public function __construct(int $clientId, ?string $fechaInicio, ?string $fechaFin)
    {
        $this->clientId = $clientId;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection(): Collection
    {
        $query = ConciliationCashMovement::where('client_id', $this->clientId);

        if ($this->fechaInicio) {
            $query->whereDate('fecha_contable', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('fecha_contable', '<=', $this->fechaFin);
        }

        return $query->orderBy('fecha_contable')->get()->map(function ($m) {
            return [
                'fecha' => $m->fecha_contable?->format('d/m/Y'),
                'tipo' => $m->tipo,
                'proveedor_para' => $m->proveedor_para,
                'monto' => $m->monto,
                'comentario' => $m->comentario,
                'usuario' => $m->usuario,
                'forma_pago' => $m->forma_pago,
                'turno' => $m->turno,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Tipo',
            'Proveedor/Para',
            'Monto',
            'Comentario',
            'Usuario',
            'Forma Pago',
            'Turno',
        ];
    }

    public function title(): string
    {
        return 'Movimientos Caja';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
