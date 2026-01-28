<?php

namespace App\Models\Conciliation;

use App\Models\WorkflowExecution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConciliationMpNegative extends Model
{
    protected $table = 'conciliation_mp_negatives';

    protected $fillable = [
        'workflow_execution_id',
        'fecha',
        'hora',
        'id_operacion_mp',
        'monto_neto',
        'turno',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto_neto' => 'decimal:2',
    ];

    public function workflowExecution(): BelongsTo
    {
        return $this->belongsTo(WorkflowExecution::class);
    }

    /**
     * Scope por turno
     */
    public function scopeTurno($query, string $turno)
    {
        return $query->where('turno', $turno);
    }

    /**
     * Obtener monto absoluto
     */
    public function getMontoAbsolutoAttribute(): float
    {
        return abs($this->monto_neto);
    }
}
