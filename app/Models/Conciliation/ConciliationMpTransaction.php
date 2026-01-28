<?php

namespace App\Models\Conciliation;

use App\Models\WorkflowExecution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConciliationMpTransaction extends Model
{
    protected $table = 'conciliation_mp_transactions';

    protected $fillable = [
        'workflow_execution_id',
        'fecha',
        'hora',
        'id_operacion_mp',
        'monto_neto',
        'tipo_movimiento',
        'medio_pago',
        'metodo_pago',
        'cuotas',
        'estado',
        'turno',
        'estado_conciliacion',
        'tipo_match',
        'id_venta_sistema',
        'fecha_solo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto_neto' => 'decimal:2',
        'cuotas' => 'integer',
        'fecha_solo' => 'date',
    ];

    public function workflowExecution(): BelongsTo
    {
        return $this->belongsTo(WorkflowExecution::class);
    }

    /**
     * Verificar si está conciliado
     */
    public function isConciliado(): bool
    {
        return $this->estado_conciliacion === 'Conciliado';
    }

    /**
     * Scope para transacciones conciliadas
     */
    public function scopeConciliadas($query)
    {
        return $query->where('estado_conciliacion', 'Conciliado');
    }

    /**
     * Scope para transacciones no conciliadas
     */
    public function scopeNoConciliadas($query)
    {
        return $query->where('estado_conciliacion', '!=', 'Conciliado')
            ->orWhereNull('estado_conciliacion');
    }

    /**
     * Scope por turno
     */
    public function scopeTurno($query, string $turno)
    {
        return $query->where('turno', $turno);
    }
}
