<?php

namespace App\Models;

use App\Models\Conciliation\ConciliationSummary;
use App\Models\Conciliation\ConciliationGetnetTransaction;
use App\Models\Conciliation\ConciliationMpTransaction;
use App\Models\Conciliation\ConciliationSystemSale;
use App\Models\Conciliation\ConciliationCashMovement;
use App\Models\Conciliation\ConciliationShift;
use App\Models\Conciliation\ConciliationRefund;
use App\Models\Conciliation\ConciliationMpNegative;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowExecution extends Model
{
    protected $fillable = [
        'workflow_id',
        'workflow_file_batch_id',
        'status',
        'logs_json',
        'json_sent',
        'json_response',
        'excel_response_path',
        'execution_time_ms',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'logs_json' => 'array',
        'json_sent' => 'array',
        'json_response' => 'array',
        'execution_time_ms' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the workflow that owns this execution
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Get the file batch for this execution
     */
    public function fileBatch(): BelongsTo
    {
        return $this->belongsTo(WorkflowFileBatch::class, 'workflow_file_batch_id');
    }

    /**
     * Get the Excel response file path (full storage path)
     */
    public function getExcelResponseFullPath(): ?string
    {
        if (!$this->excel_response_path) {
            return null;
        }

        return storage_path('app/public/' . $this->excel_response_path);
    }

    /**
     * Check if Excel response exists
     */
    public function hasExcelResponse(): bool
    {
        return $this->excel_response_path && \Storage::disk('public')->exists($this->excel_response_path);
    }

    // ==================== CONCILIATION RELATIONS ====================

    /**
     * Get conciliation summaries
     */
    public function conciliationSummaries(): HasMany
    {
        return $this->hasMany(ConciliationSummary::class);
    }

    /**
     * Get Getnet transactions
     */
    public function conciliationGetnetTransactions(): HasMany
    {
        return $this->hasMany(ConciliationGetnetTransaction::class);
    }

    /**
     * Get MercadoPago transactions
     */
    public function conciliationMpTransactions(): HasMany
    {
        return $this->hasMany(ConciliationMpTransaction::class);
    }

    /**
     * Get system sales
     */
    public function conciliationSystemSales(): HasMany
    {
        return $this->hasMany(ConciliationSystemSale::class);
    }

    /**
     * Get cash movements
     */
    public function conciliationCashMovements(): HasMany
    {
        return $this->hasMany(ConciliationCashMovement::class);
    }

    /**
     * Get shifts
     */
    public function conciliationShifts(): HasMany
    {
        return $this->hasMany(ConciliationShift::class);
    }

    /**
     * Get refunds
     */
    public function conciliationRefunds(): HasMany
    {
        return $this->hasMany(ConciliationRefund::class);
    }

    /**
     * Get MP negatives
     */
    public function conciliationMpNegatives(): HasMany
    {
        return $this->hasMany(ConciliationMpNegative::class);
    }

    /**
     * Check if this execution has conciliation data
     */
    public function hasConciliationData(): bool
    {
        return $this->conciliationSummaries()->exists();
    }
}
