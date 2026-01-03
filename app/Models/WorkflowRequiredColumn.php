<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowRequiredColumn extends Model
{
    protected $fillable = [
        'workflow_file_definition_id',
        'column_name',
        'column_type',
        'is_required',
        'validation_rules',
    ];

    protected $casts = [
        'workflow_file_definition_id' => 'integer',
        'is_required' => 'boolean',
        'validation_rules' => 'array',
    ];

    /**
     * Get the file definition that owns this required column
     */
    public function fileDefinition(): BelongsTo
    {
        return $this->belongsTo(WorkflowFileDefinition::class, 'workflow_file_definition_id');
    }
}
