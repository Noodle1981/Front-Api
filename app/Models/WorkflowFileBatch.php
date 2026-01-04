<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class WorkflowFileBatch extends Model
{
    protected $fillable = [
        'workflow_type_id',
        'client_id',
        'branch_id',
        'user_id',
        'batch_code',
        'status',
        'validation_errors',
        'uploaded_at',
        'validated_at',
    ];

    protected $casts = [
        'workflow_type_id' => 'integer',
        'client_id' => 'integer',
        'branch_id' => 'integer',
        'user_id' => 'integer',
        'validation_errors' => 'array',
        'uploaded_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($batch) {
            if (empty($batch->batch_code)) {
                $batch->batch_code = $batch->generateBatchCode();
            }
        });
    }

    /**
     * Get the workflow type that owns this batch
     */
    public function workflowType(): BelongsTo
    {
        return $this->belongsTo(WorkflowType::class);
    }

    /**
     * Get the client that owns this batch
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the branch (client child) that owns this batch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'branch_id');
    }

    /**
     * Get the user that created this batch
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the uploaded files for this batch
     */
    public function uploadedFiles(): HasMany
    {
        return $this->hasMany(WorkflowUploadedFile::class);
    }

    /**
     * Get the execution for this batch
     */
    public function execution(): HasOne
    {
        return $this->hasOne(WorkflowExecution::class);
    }

    /**
     * Get all executions for this batch
     */
    public function executions(): HasMany
    {
        return $this->hasMany(WorkflowExecution::class);
    }

    /**
     * Generate a unique batch code
     */
    public function generateBatchCode(): string
    {
        return 'WF-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }

    /**
     * Check if batch has all required files
     */
    public function isComplete(): bool
    {
        $expectedCount = $this->workflowType->expected_files_count;
        $uploadedCount = $this->uploadedFiles()->count();
        
        return $uploadedCount === $expectedCount;
    }

    /**
     * Check if batch passed validation
     */
    public function isValid(): bool
    {
        return $this->status === 'validated' && empty($this->validation_errors);
    }
}
