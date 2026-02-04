<?php

namespace App\Services;

use App\Models\WorkflowExecution;
use App\Models\Conciliation\ConciliationSummary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service para procesar resultados del endpoint /arqueo
 * Formato de respuesta: arqueo.json
 *
 * Sección principal:
 * - arqueo_por_turno: Resumen detallado por turno con todas las métricas
 */
class ArqueoResultService
{
    /**
     * Procesar y guardar resultados de arqueo
     */
    public function processAndSave(WorkflowExecution $execution, array $response): array
    {
        $stats = [
            'total_processed' => 0,
            'sections' => [],
        ];

        if (!$this->validateResponse($response)) {
            Log::warning('Arqueo result response validation failed', [
                'execution_id' => $execution->id,
                'status' => $response['status'] ?? 'unknown',
            ]);
            return $stats;
        }

        $clientId = $execution->fileBatch?->branch_id ?? $execution->fileBatch?->client_id;

        if (!$clientId) {
            Log::warning('No client_id found for arqueo execution', [
                'execution_id' => $execution->id,
            ]);
            return $stats;
        }

        DB::transaction(function () use ($execution, $response, $clientId, &$stats) {
            $data = $response['data'];

            // Procesar arqueo por turno (resultado principal)
            $stats['sections']['arqueo_por_turno'] = $this->saveArqueoPorTurno(
                $execution,
                $clientId,
                $data['arqueo_por_turno'] ?? []
            );

            foreach ($stats['sections'] as $section) {
                $stats['total_processed'] += $section['processed'] ?? 0;
            }
        });

        Log::info('Arqueo results processed', [
            'execution_id' => $execution->id,
            'client_id' => $clientId,
            'total_processed' => $stats['total_processed'],
        ]);

        return $stats;
    }

    /**
     * Validar que la respuesta sea exitosa
     */
    public function validateResponse(array $response): bool
    {
        return ($response['status'] ?? '') === 'success'
            && isset($response['data']);
    }

    /**
     * Guardar arqueo por turno (resumen con métricas completas)
     */
    private function saveArqueoPorTurno(WorkflowExecution $execution, int $clientId, array $records): array
    {
        $stats = ['processed' => 0, 'skipped' => 0, 'updated' => 0, 'created' => 0];

        if (empty($records)) {
            return $stats;
        }

        foreach ($records as $r) {
            $fecha = $this->parseDate($r['Fecha'] ?? null);
            // Trim string values to avoid whitespace comparison issues
            $turno = $this->trimString($r['TURNO'] ?? null);
            $encargado = $this->trimString($r['Encargado'] ?? null);

            if (!$fecha || !$turno) {
                $stats['skipped']++;
                continue;
            }

            $summaryData = [
                'workflow_execution_id' => $execution->id,
                'client_id' => $clientId,
                'fecha' => $fecha,
                'dia' => $this->trimString($r['Dia'] ?? null),
                'turno' => $turno,
                'encargado' => $encargado,
                'apertura' => $this->parseDateTime($r['Apertura'] ?? $r['Hora Apertura'] ?? null),
                'cierre' => $this->parseDateTime($r['Cierre'] ?? $r['Hora Cierre'] ?? null),
                'horas_trabajadas' => $this->parseDecimal($r['Horas Trabajadas'] ?? 0),
                // Ventas y tickets
                'ventas_totales' => $this->parseDecimal($r['Ventas Totales'] ?? 0),
                'cantidad_comensales' => (int) ($r['Cantidad de comensales'] ?? 0),
                'cantidad_tickets' => (int) ($r['Cantidad de ticket'] ?? 0),
                'ticket_promedio' => $this->parseDecimal($r['Ticket Promedio'] ?? 0),
                'propina' => $this->parseDecimal($r['Propina'] ?? 0),
                // MercadoPago
                'mp_ventas_real' => $this->parseDecimal($r['Ventas MP Real'] ?? 0),
                'mp_ventas_sistema' => $this->parseDecimal($r['Ventas Sistema MP Real'] ?? $r['Ventas Sistema MP (Corregido)'] ?? 0),
                'mp_conciliado' => $this->parseDecimal($r['Conciliado MP REAL'] ?? $r['Conciliado Sistema MP'] ?? 0),
                'mp_no_conciliado' => $this->parseDecimal($r['No conciliado MP REAL'] ?? $r['No conciiado Sistema MP'] ?? 0),
                'mp_diferencia' => $this->parseDecimal($r['Diferencia MP sistema vs real'] ?? 0),
                'mp_porcentaje' => $this->parseDecimal($r['% diferencia MP'] ?? 0),
                'mp_estado' => $this->trimString($r['Estado Dif MP'] ?? null),
                // Getnet
                'getnet_ventas_real' => $this->parseDecimal($r['Ventas Getnet Real'] ?? 0),
                'getnet_ventas_sistema' => $this->parseDecimal($r['Ventas sistema Real'] ?? $r['Ventas Sistema Getnet (Corregido)'] ?? 0),
                'getnet_conciliado' => $this->parseDecimal($r['Conciliado Getnet REAL'] ?? $r['Conciliado Sistema Getnet'] ?? 0),
                'getnet_no_conciliado' => $this->parseDecimal($r['No conciliado Getnet REAL'] ?? $r['No conciliado Sistema Getnet'] ?? 0),
                'getnet_diferencia' => $this->parseDecimal($r['Diferencia Getnet sistema vs real'] ?? 0),
                'getnet_porcentaje' => $this->parseDecimal($r['% diferencia Getnet'] ?? 0),
                'getnet_estado' => $this->trimString($r['Estado Dif Getnet'] ?? null),
                // Efectivo
                'efectivo_total' => $this->parseDecimal($r['Efectivo Total'] ?? $r['Efectivo Real'] ?? 0),
                'efectivo_apertura_caja' => $this->parseDecimal($r['APERTURA CAJA Efectivo'] ?? 0),
                'efectivo_recuento' => $this->parseDecimal($r['Recuento Efectivo'] ?? $r['Recuento sistema efectivo'] ?? 0),
                'efectivo_diferencia' => $this->parseDecimal($r['Diferencia Efectivo'] ?? 0),
                'efectivo_porcentaje' => $this->parseDecimal($r['% diferencia efectivo'] ?? 0),
                'efectivo_estado' => $this->trimString($r['Estado Diferencia Efectivo'] ?? null),
                // Otros
                'cta_cte_total' => $this->parseDecimal($r['Cta Cte Total'] ?? $r['Cta Cte Real'] ?? 0),
                'otros' => $this->parseDecimal($r['Otros'] ?? 0),
                'descuentos' => $this->parseDecimal($r['Descuentos'] ?? 0),
                'ventas_facturadas' => $this->parseDecimal($r['Ventas facturadas'] ?? 0),
                'ideal_facturacion' => $this->parseDecimal($r['Ideal facturacion'] ?? 0),
                'diferencia_facturacion' => $this->parseDecimal($r['Diferencia de facturacion'] ?? 0),
                'porcentaje_facturacion' => $this->parseDecimal($r['% diferencia facturacion'] ?? 0),
                // Ventas por hora (JSON)
                'ventas_por_hora' => $this->extractVentasPorHora($r),
            ];

            try {
                // Try to find existing record first
                $existing = $this->findExistingSummary($clientId, $fecha, $turno, $encargado);

                if ($existing) {
                    $existing->update($summaryData);
                    $stats['updated']++;
                    Log::debug('Updated existing arqueo summary', [
                        'id' => $existing->id,
                        'fecha' => $fecha,
                        'turno' => $turno,
                    ]);
                } else {
                    ConciliationSummary::create($summaryData);
                    $stats['created']++;
                }
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle UNIQUE constraint violation - record exists but query didn't find it
                if (str_contains($e->getMessage(), 'UNIQUE constraint failed') ||
                    str_contains($e->getMessage(), 'Duplicate entry')) {
                    Log::warning('ArqueoResult: UNIQUE constraint hit, attempting update', [
                        'client_id' => $clientId,
                        'fecha' => $fecha,
                        'turno' => $turno,
                        'encargado' => $encargado,
                    ]);

                    $this->forceUpdateSummary($clientId, $fecha, $turno, $encargado, $summaryData);
                    $stats['updated']++;
                } else {
                    throw $e;
                }
            }

            $stats['processed']++;
        }

        return $stats;
    }

    /**
     * Find existing summary record handling NULL encargado correctly
     */
    private function findExistingSummary(int $clientId, string $fecha, string $turno, ?string $encargado): ?ConciliationSummary
    {
        $query = ConciliationSummary::where('client_id', $clientId)
            ->where('fecha', $fecha)
            ->where('turno', $turno);

        if ($encargado === null) {
            $query->whereNull('encargado');
        } else {
            $query->where('encargado', $encargado);
        }

        return $query->first();
    }

    /**
     * Force update summary using raw query to handle NULL encargado
     */
    private function forceUpdateSummary(int $clientId, string $fecha, string $turno, ?string $encargado, array $data): void
    {
        $query = DB::table('conciliation_summaries')
            ->where('client_id', $clientId)
            ->where('fecha', $fecha)
            ->where('turno', $turno);

        if ($encargado === null) {
            $query->whereNull('encargado');
        } else {
            $query->where('encargado', $encargado);
        }

        unset($data['created_at']);
        $data['updated_at'] = now();

        $query->update($data);
    }

    /**
     * Trim string value, return null if empty
     */
    private function trimString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim($value);
        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * Extract ventas por hora from record
     */
    private function extractVentasPorHora(array $r): array
    {
        $ventasPorHora = [];

        for ($hour = 0; $hour < 24; $hour++) {
            $key = sprintf('%02d:00 - %02d:00', $hour, $hour + 1);
            if (isset($r[$key])) {
                $ventasPorHora[$key] = $this->parseDecimal($r[$key]);
            }
        }

        return $ventasPorHora;
    }

    // ==================== HELPERS ====================

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}T/', $value)) {
                return Carbon::parse($value)->format('Y-m-d');
            }
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value;
            }
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseDateTime($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value)) {
                return Carbon::parse($value)->format('Y-m-d H:i:s');
            }
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}\s+\d{2}:\d{2}:\d{2}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y H:i:s', $value)->format('Y-m-d H:i:s');
            }
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseDecimal($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        if (is_string($value)) {
            return (float) str_replace([',', '"'], ['', ''], $value);
        }
        return 0.0;
    }
}
