<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'expected_files_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expected_files_count' => 'integer',
    ];

    /**
     * Get the file definitions for this workflow type
     */
    public function fileDefinitions(): HasMany
    {
        return $this->hasMany(WorkflowFileDefinition::class)->orderBy('order');
    }

    /**
     * Get the file batches for this workflow type
     */
    public function fileBatches(): HasMany
    {
        return $this->hasMany(WorkflowFileBatch::class);
    }

    /**
     * Scope to get only active workflows
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
