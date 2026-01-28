<?php

namespace App\Http\Controllers;

use App\Models\WorkflowExecution;
use App\Exports\ConciliationExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ConciliationExportController extends Controller
{
    public function export(WorkflowExecution $execution, string $type)
    {
        $filename = "conciliacion_{$execution->id}_" . now()->format('Y-m-d') . ".xlsx";

        return Excel::download(new ConciliationExport($execution), $filename);
    }
}
