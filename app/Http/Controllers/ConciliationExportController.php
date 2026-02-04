<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\WorkflowExecution;
use App\Exports\ConciliationExport;
use App\Exports\ConciliationClientExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ConciliationExportController extends Controller
{
    /**
     * Export conciliation data for a specific execution (legacy)
     */
    public function export(WorkflowExecution $execution, string $type)
    {
        $filename = "conciliacion_{$execution->id}_" . now()->format('Y-m-d') . ".xlsx";

        return Excel::download(new ConciliationExport($execution), $filename);
    }

    /**
     * Export conciliation data for a client within a date range
     */
    public function exportByClient(Request $request, Client $client, string $type)
    {
        $fechaInicio = $request->get('fechaInicio');
        $fechaFin = $request->get('fechaFin');

        $filename = "conciliacion_{$client->company}_";
        if ($fechaInicio && $fechaFin) {
            $filename .= "{$fechaInicio}_a_{$fechaFin}";
        } else {
            $filename .= now()->format('Y-m-d');
        }
        $filename = str_replace(' ', '_', $filename) . ".xlsx";

        return Excel::download(
            new ConciliationClientExport($client->id, $fechaInicio, $fechaFin),
            $filename
        );
    }
}
