<?php

namespace App\Models\Conciliation;

use App\Models\WorkflowExecution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConciliationSystemSale extends Model
{
    protected $table = 'conciliation_system_sales';

    protected $fillable = [
        'workflow_execution_id',
        'id_ticket',
        'fecha_hora',
        'hora',
        'items_count',
        'monto_total',
        'tipo_venta',
        'estado_venta',
        'metodo_pago',
        'turno',
        'estado_conciliacion',
        'tipo_match',
        'id_operacion_conciliado',
        'fecha_solo',
        'conciliado',
        'source',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'items_count' => 'integer',
        'monto_total' => 'decimal:2',
        'fecha_solo' => 'date',
        'conciliado' => 'boolean',
    ];

    public function workflowExecution(): BelongsTo
    {
        return $this->belongsTo(WorkflowExecution::class);
    }

    /**
     * Scope para ventas conciliadas
     */
    public function scopeConciliadas($query)
    {
        return $query->where('conciliado', true);
    }

    /**
     * Scope para ventas no conciliadas
     */
    public function scopeNoConciliadas($query)
    {
        return $query->where('conciliado', false);
    }

    /**
     * Scope por origen (sistema o conciliado)
     */
    public function scopeFromSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope por turno
     */
    public function scopeTurno($query, string $turno)
    {
        return $query->where('turno', $turno);
    }

    /**
     * Scope por método de pago
     */
    public function scopeMetodoPago($query, string $metodoPago)
    {
        return $query->where('metodo_pago', 'like', "%{$metodoPago}%");
    }
}
