<?php

namespace App\Models\Conciliation;

use App\Models\WorkflowExecution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConciliationShift extends Model
{
    protected $table = 'conciliation_shifts';

    protected $fillable = [
        'workflow_execution_id',
        'fecha_apertura',
        'hora_apertura',
        'fecha_cierre',
        'hora_cierre',
        'turno',
        'encargado',
        'cantidad_comensales',
        'recuento_efectivo',
        'apertura_caja',
    ];

    protected $casts = [
        'fecha_apertura' => 'date',
        'fecha_cierre' => 'date',
        'cantidad_comensales' => 'integer',
        'recuento_efectivo' => 'decimal:2',
        'apertura_caja' => 'decimal:2',
    ];

    public function workflowExecution(): BelongsTo
    {
        return $this->belongsTo(WorkflowExecution::class);
    }

    /**
     * Calcular diferencia de efectivo
     */
    public function getDiferenciaEfectivoAttribute(): float
    {
        return $this->recuento_efectivo - $this->apertura_caja;
    }

    /**
     * Scope por turno
     */
    public function scopeTurno($query, string $turno)
    {
        return $query->where('turno', $turno);
    }

    /**
     * Scope por encargado
     */
    public function scopeEncargado($query, string $encargado)
    {
        return $query->where('encargado', $encargado);
    }
}
