<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessRule extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'client_id',
        'api_service_id',
        'endpoint_id',
        'python_code',
        'input_schema',
        'output_schema',
        'created_by',
        'last_executed_at',
        'execution_count',
    ];

    protected $casts = [
        'input_schema' => 'array',
        'output_schema' => 'array',
        'last_executed_at' => 'datetime',
        'execution_count' => 'integer',
    ];

    /**
     * Relación con Cliente
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relación con Servicio API
     */
    public function apiService(): BelongsTo
    {
        return $this->belongsTo(ApiService::class);
    }

    /**
     * Relación con Endpoint
     */
    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(Endpoint::class);
    }

    /**
     * Relación con el usuario que creó la regla
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Verifica si la regla está activa
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Incrementa el contador de ejecuciones
     */
    public function incrementExecutionCount(): void
    {
        $this->increment('execution_count');
        $this->update(['last_executed_at' => now()]);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para filtrar por inactivo
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
