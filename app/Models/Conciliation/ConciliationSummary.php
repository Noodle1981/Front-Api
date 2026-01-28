<?php

namespace App\Models\Conciliation;

use App\Models\Client;
use App\Models\WorkflowExecution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConciliationSummary extends Model
{
    protected $table = 'conciliation_summaries';

    protected $fillable = [
        'workflow_execution_id',
        'client_id',
        'fecha',
        'dia',
        'turno',
        'encargado',
        'apertura',
        'cierre',
        'horas_trabajadas',
        'ventas_totales',
        'cantidad_comensales',
        'ticket_promedio',
        'cantidad_tickets',
        'propina',
        'mp_ventas_real',
        'mp_ventas_sistema',
        'mp_conciliado',
        'mp_no_conciliado',
        'mp_diferencia',
        'mp_porcentaje',
        'mp_estado',
        'getnet_ventas_real',
        'getnet_ventas_sistema',
        'getnet_conciliado',
        'getnet_no_conciliado',
        'getnet_diferencia',
        'getnet_porcentaje',
        'getnet_estado',
        'efectivo_total',
        'efectivo_apertura_caja',
        'efectivo_recuento',
        'efectivo_diferencia',
        'efectivo_porcentaje',
        'efectivo_estado',
        'cta_cte_total',
        'otros',
        'descuentos',
        'ventas_facturadas',
        'ideal_facturacion',
        'diferencia_facturacion',
        'porcentaje_facturacion',
        'ventas_por_hora',
    ];

    protected $casts = [
        'fecha' => 'date',
        'apertura' => 'datetime',
        'cierre' => 'datetime',
        'horas_trabajadas' => 'decimal:4',
        'ventas_totales' => 'decimal:2',
        'cantidad_comensales' => 'integer',
        'ticket_promedio' => 'decimal:2',
        'cantidad_tickets' => 'integer',
        'propina' => 'decimal:2',
        'mp_ventas_real' => 'decimal:2',
        'mp_ventas_sistema' => 'decimal:2',
        'mp_conciliado' => 'decimal:2',
        'mp_no_conciliado' => 'decimal:2',
        'mp_diferencia' => 'decimal:2',
        'mp_porcentaje' => 'decimal:4',
        'getnet_ventas_real' => 'decimal:2',
        'getnet_ventas_sistema' => 'decimal:2',
        'getnet_conciliado' => 'decimal:2',
        'getnet_no_conciliado' => 'decimal:2',
        'getnet_diferencia' => 'decimal:2',
        'getnet_porcentaje' => 'decimal:4',
        'efectivo_total' => 'decimal:2',
        'efectivo_apertura_caja' => 'decimal:2',
        'efectivo_recuento' => 'decimal:2',
        'efectivo_diferencia' => 'decimal:2',
        'efectivo_porcentaje' => 'decimal:4',
        'cta_cte_total' => 'decimal:2',
        'otros' => 'decimal:2',
        'descuentos' => 'decimal:2',
        'ventas_facturadas' => 'decimal:2',
        'ideal_facturacion' => 'decimal:2',
        'diferencia_facturacion' => 'decimal:2',
        'porcentaje_facturacion' => 'decimal:4',
        'ventas_por_hora' => 'array',
    ];

    public function workflowExecution(): BelongsTo
    {
        return $this->belongsTo(WorkflowExecution::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Calcular porcentaje de conciliación MP
     */
    public function getMpConciliacionPorcentajeAttribute(): float
    {
        if ($this->mp_ventas_real == 0) {
            return 100;
        }
        return round(($this->mp_conciliado / $this->mp_ventas_real) * 100, 2);
    }

    /**
     * Calcular porcentaje de conciliación Getnet
     */
    public function getGetnetConciliacionPorcentajeAttribute(): float
    {
        if ($this->getnet_ventas_real == 0) {
            return 100;
        }
        return round(($this->getnet_conciliado / $this->getnet_ventas_real) * 100, 2);
    }

    /**
     * Verificar si hay alertas de diferencias significativas
     */
    public function hasAlerts(): bool
    {
        return abs($this->mp_diferencia) > 1000
            || abs($this->getnet_diferencia) > 1000
            || abs($this->efectivo_diferencia) > 1000;
    }
}
