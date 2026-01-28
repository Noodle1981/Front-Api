<?php

namespace App\Models\Conciliation;

use App\Models\WorkflowExecution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConciliationRefund extends Model
{
    protected $table = 'conciliation_refunds';

    protected $fillable = [
        'workflow_execution_id',
        'producto',
        'precio',
        'comentario',
        'fecha_hora_pedido',
        'hora_pedido',
        'hora_anulacion',
        'turno',
        'unique_hash',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'fecha_hora_pedido' => 'datetime',
    ];

    /**
     * Generar hash único basado en campos identificadores
     */
    public static function generateUniqueHash(array $data): string
    {
        $key = implode('|', [
            $data['fecha_hora_pedido'] ?? '',
            $data['producto'] ?? '',
            $data['precio'] ?? 0,
            substr($data['comentario'] ?? '', 0, 30),
        ]);

        return hash('sha256', $key);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->unique_hash)) {
                $model->unique_hash = self::generateUniqueHash($model->toArray());
            }
        });
    }

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
}
