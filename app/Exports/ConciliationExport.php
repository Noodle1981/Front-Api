<?php

namespace App\Exports;

use App\Models\WorkflowExecution;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ConciliationExport implements WithMultipleSheets
{
    protected WorkflowExecution $execution;

    public function __construct(WorkflowExecution $execution)
    {
        $this->execution = $execution;
    }

    public function sheets(): array
    {
        return [
            new ConciliationSummarySheet($this->execution),
            new ConciliationGetnetSheet($this->execution),
            new ConciliationMpSheet($this->execution),
            new ConciliationCashMovementsSheet($this->execution),
        ];
    }
}

class ConciliationSummarySheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected WorkflowExecution $execution;

    public function __construct(WorkflowExecution $execution)
    {
        $this->execution = $execution;
    }

    public function collection(): Collection
    {
        return $this->execution->conciliationSummaries->map(function ($s) {
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
            'Día',
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

class ConciliationGetnetSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected WorkflowExecution $execution;

    public function __construct(WorkflowExecution $execution)
    {
        $this->execution = $execution;
    }

    public function collection(): Collection
    {
        return $this->execution->conciliationGetnetTransactions->map(function ($t) {
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
            'Cod. Transacción',
            'Marca',
            'Tipo',
            'Tarjeta',
            'Monto Bruto',
            'Monto Neto',
            'Arancel',
            'Estado Venta',
            'Estado Conciliación',
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

class ConciliationMpSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected WorkflowExecution $execution;

    public function __construct(WorkflowExecution $execution)
    {
        $this->execution = $execution;
    }

    public function collection(): Collection
    {
        return $this->execution->conciliationMpTransactions->map(function ($t) {
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
            'ID Operación',
            'Monto Neto',
            'Medio Pago',
            'Método Pago',
            'Estado',
            'Estado Conciliación',
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

class ConciliationCashMovementsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected WorkflowExecution $execution;

    public function __construct(WorkflowExecution $execution)
    {
        $this->execution = $execution;
    }

    public function collection(): Collection
    {
        return $this->execution->conciliationCashMovements->map(function ($m) {
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
