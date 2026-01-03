<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class WorkflowUploadedFile extends Model
{
    protected $fillable = [
        'workflow_file_batch_id',
        'workflow_file_definition_id',
        'original_filename',
        'stored_filename',
        'file_path',
        'file_size',
        'rows_count',
        'validation_status',
        'validation_errors',
    ];

    protected $casts = [
        'workflow_file_batch_id' => 'integer',
        'workflow_file_definition_id' => 'integer',
        'file_size' => 'integer',
        'rows_count' => 'integer',
        'validation_errors' => 'array',
    ];

    /**
     * Get the batch that owns this file
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(WorkflowFileBatch::class, 'workflow_file_batch_id');
    }

    /**
     * Get the file definition for this file
     */
    public function fileDefinition(): BelongsTo
    {
        return $this->belongsTo(WorkflowFileDefinition::class, 'workflow_file_definition_id');
    }

    /**
     * Get the full path to the file
     */
    public function getFullPath(): string
    {
        return Storage::path($this->file_path);
    }
}
