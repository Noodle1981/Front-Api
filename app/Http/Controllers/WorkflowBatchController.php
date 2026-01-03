<?php

namespace App\Http\Controllers;

use App\Models\WorkflowFileBatch;
use App\Models\WorkflowExecution;
use Illuminate\Http\Request;

class WorkflowBatchController extends Controller
{
    /**
     * Display the specified batch
     */
    public function show(WorkflowFileBatch $batch)
    {
        $batch->load([
            'workflowType',
            'client',
            'branch',
            'user',
            'uploadedFiles.fileDefinition',
        ]);
        
        return view('workflows.batch-show', compact('batch'));
    }
    
    /**
     * Display test view with latest executions
     */
    public function test()
    {
        $executions = WorkflowExecution::with([
            'fileBatch.workflowType',
            'fileBatch.client',
            'fileBatch.branch',
            'fileBatch.user'
        ])
        ->latest()
        ->take(10)
        ->get();
        
        return view('workflows.test', compact('executions'));
    }
}
