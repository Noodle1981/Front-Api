<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowRequest extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'branch_id',
        'workflow_type',
        'title',
        'description',
        'priority',
        'expected_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function branch()
    {
        return $this->belongsTo(Client::class, 'branch_id');
    }

    public function batch()
    {
        return $this->hasOne(WorkflowFileBatch::class);
    }
}
