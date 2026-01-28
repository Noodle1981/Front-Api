<?php

namespace App\Models\Conciliation;

use App\Models\WorkflowExecution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConciliationCashMovement extends Model
{
    protected $table = 'conciliation_cash_movements';

    protected $fillable = [
        'workflow_execution_id',
        'fecha_contable',
        'fecha_modificacion',
        'origen',
        'clase',
        'proveedor_para',
        'monto',
        'comentario',
        'usuario',
        'tipo',
        'forma_pago',
        'cuenta_contable',
        'turno',
        'unique_hash',
    ];

    protected $casts = [
        'fecha_contable' => 'date',
        'fecha_modificacion' => 'datetime',
        'monto' => 'decimal:2',
    ];

    /**
     * Generar hash único basado en campos identificadores
     */
    public static function generateUniqueHash(array $data): string
    {
        $key = implode('|', [
            $data['fecha_contable'] ?? '',
            $data['fecha_modificacion'] ?? '',
            $data['monto'] ?? 0,
            substr($data['comentario'] ?? '', 0, 50),
            $data['tipo'] ?? '',
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
     * Verificar si es ingreso
     */
    public function isIngreso(): bool
    {
        return $this->tipo === 'Ingreso';
    }

    /**
     * Verificar si es egreso
     */
    public function isEgreso(): bool
    {
        return $this->tipo === 'Egreso';
    }

    /**
     * Scope para ingresos
     */
    public function scopeIngresos($query)
    {
        return $query->where('tipo', 'Ingreso');
    }

    /**
     * Scope para egresos
     */
    public function scopeEgresos($query)
    {
        return $query->where('tipo', 'Egreso');
    }

    /**
     * Scope por turno
     */
    public function scopeTurno($query, string $turno)
    {
        return $query->where('turno', $turno);
    }
}
