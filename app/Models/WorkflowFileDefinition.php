<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowFileDefinition extends Model
{
    protected $fillable = [
        'workflow_type_id',
        'file_identifier',
        'display_name',
        'description',
        'order',
        'is_required',
    ];

    protected $casts = [
        'workflow_type_id' => 'integer',
        'order' => 'integer',
        'is_required' => 'boolean',
    ];

    /**
     * Get the workflow type that owns this file definition
     */
    public function workflowType(): BelongsTo
    {
        return $this->belongsTo(WorkflowType::class);
    }

    /**
     * Get the required columns for this file definition
     */
    public function requiredColumns(): HasMany
    {
        return $this->hasMany(WorkflowRequiredColumn::class);
    }

    /**
     * Get the uploaded files for this file definition
     */
    public function uploadedFiles(): HasMany
    {
        return $this->hasMany(WorkflowUploadedFile::class);
    }
}
