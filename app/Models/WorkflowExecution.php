<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
