<?php

namespace App\Models\Conciliation;

use App\Models\WorkflowExecution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConciliationGetnetTransaction extends Model
{
    protected $table = 'conciliation_getnet_transactions';

    protected $fillable = [
        'workflow_execution_id',
        'nro_establecimiento',
        'nombre_establecimiento',
        'fecha_operacion',
        'billetera',
        'marca',
        'tipo_tarjeta',
        'tarjeta_ultimos4',
        'tipo_transaccion',
        'canal',
        'modo_canal',
        'codigo_pos',
        'estado_venta',
        'cod_transaccion',
        'cod_transaccion_externo',
        'nro_cupon',
        'cod_autorizacion',
        'plan_cuotas',
        'moneda',
        'monto_bruto',
        'arancel',
        'iva_arancel',
        'propina',
        'monto_neto',
        'fecha_liquidacion',
        'fecha_pago',
        'cod_liquidacion',
        'turno',
        'estado_conciliacion',
        'tipo_match',
        'id_venta_sistema',
        'fecha_solo',
    ];

    protected $casts = [
        'fecha_operacion' => 'datetime',
        'monto_bruto' => 'decimal:2',
        'arancel' => 'decimal:2',
        'iva_arancel' => 'decimal:2',
        'propina' => 'decimal:2',
        'monto_neto' => 'decimal:2',
        'fecha_liquidacion' => 'date',
        'fecha_pago' => 'date',
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
