<?php

namespace App\Http\Controllers;

use App\Models\WorkflowFileBatch;
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
}
