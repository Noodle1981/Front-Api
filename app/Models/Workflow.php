<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $fillable = ['name', 'description', 'trigger_type', 'schedule', 'steps_json', 'is_active'];

    protected $casts = [
        'steps_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function executions()
    {
        return $this->hasMany(WorkflowExecution::class);
    }
}
