<?php

namespace App\Services;

use App\Models\WorkflowExecution;
use App\Models\Conciliation\ConciliationSummary;
use App\Models\Conciliation\ConciliationGetnetTransaction;
use App\Models\Conciliation\ConciliationMpTransaction;
use App\Models\Conciliation\ConciliationSystemSale;
use App\Models\Conciliation\ConciliationCashMovement;
use App\Models\Conciliation\ConciliationShift;
use App\Models\Conciliation\ConciliationRefund;
use App\Models\Conciliation\ConciliationMpNegative;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service para procesar datos del endpoint /procesar-json (Arqueo)
 * Formato de respuesta: arqueo_resultado_test.json
 *
 * Secciones:
 * - arqueo_por_turno: Resumen por turno con métricas
 * - getnet_conciliado: Transacciones Getnet
 * - mp_conciliado: Transacciones MercadoPago
 * - sistema_conciliado: Ventas del sistema conciliadas
 * - ventas_sistema: Todas las ventas del sistema
 * - turnos_procesados: Turnos procesados
 * - devoluciones: Devoluciones/anulaciones
 * - caja_adicion: Movimientos de caja
 * - mp_negativos: MP negativos
 */
class ArqueoDataService
{
    /**
     * Procesar y guardar datos de arqueo
     */
    public function processAndSave(WorkflowExecution $execution, array $response): array
    {
        $stats = [
            'total_processed' => 0,
            'sections' => [],
        ];

        if (!$this->validateResponse($response)) {
            Log::warning('Arqueo response validation failed', [
                'execution_id' => $execution->id,
                'status' => $response['status'] ?? 'unknown',
            ]);
            return $stats;
        }

        $clientId = $execution->fileBatch?->branch_id ?? $execution->fileBatch?->client_id;

        if (!$clientId) {
            Log::warning('No client_id found for execution', [
                'execution_id' => $execution->id,
            ]);
            return $stats;
        }

        DB::transaction(function () use ($execution, $response, $clientId, &$stats) {
            $data = $response['data'];

            // Procesar cada sección del formato arqueo
            $stats['sections']['arqueo_por_turno'] = $this->saveArqueoPorTurno($execution, $clientId, $data['arqueo_por_turno'] ?? []);
            $stats['sections']['getnet'] = $this->saveGetnetTransactions($execution, $clientId, $data['getnet_conciliado'] ?? []);
            $stats['sections']['mp'] = $this->saveMpTransactions($execution, $clientId, $data['mp_conciliado'] ?? []);
            $stats['sections']['sistema'] = $this->saveSystemSales($execution, $clientId, $data['sistema_conciliado'] ?? [], 'conciliado');
            $stats['sections']['ventas_sistema'] = $this->saveSystemSales($execution, $clientId, $data['ventas_sistema'] ?? [], 'sistema');
            $stats['sections']['turnos'] = $this->saveTurnos($execution, $clientId, $data['turnos_procesados'] ?? []);
            $stats['sections']['devoluciones'] = $this->saveRefunds($execution, $clientId, $data['devoluciones'] ?? []);
            $stats['sections']['caja'] = $this->saveCashMovements($execution, $clientId, $data['caja_adicion'] ?? []);
            $stats['sections']['mp_negativos'] = $this->saveMpNegatives($execution, $clientId, $data['mp_negativos'] ?? []);

            foreach ($stats['sections'] as $section) {
                $stats['total_processed'] += $section['processed'] ?? 0;
            }
        });

        Log::info('Arqueo data processed', [
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
     * Guardar arqueo por turno (resumen con métricas)
     */
    private function saveArqueoPorTurno(WorkflowExecution $execution, int $clientId, array $records): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        foreach ($records as $r) {
            ConciliationSummary::updateOrCreate(
                [
                    'client_id' => $clientId,
                    'fecha' => $this->parseDate($r['Fecha'] ?? null),
                    'turno' => $r['TURNO'] ?? null,
                ],
                [
                    'workflow_execution_id' => $execution->id,
                    'dia' => $r['Dia'] ?? null,
                    'hora_apertura' => $this->parseDateTime($r['Hora Apertura'] ?? $r['Apertura'] ?? null),
                    'hora_cierre' => $this->parseDateTime($r['Hora Cierre'] ?? $r['Cierre'] ?? null),
                    'horas_trabajadas' => $r['Horas Trabajadas'] ?? 0,
                    'encargado' => $r['Encargado'] ?? null,
                    'ventas_totales' => $this->parseDecimal($r['Ventas Totales'] ?? 0),
                    'cantidad_comensales' => $r['Cantidad de comensales'] ?? 0,
                    'cantidad_tickets' => $r['Cantidad de ticket'] ?? 0,
                    'ticket_promedio' => $this->parseDecimal($r['Ticket Promedio'] ?? 0),
                    'propina' => $this->parseDecimal($r['Propina'] ?? 0),
                    // MP
                    'ventas_mp_real' => $this->parseDecimal($r['Ventas MP Real'] ?? 0),
                    'ventas_sistema_mp' => $this->parseDecimal($r['Ventas Sistema MP Real'] ?? 0),
                    'conciliado_mp' => $this->parseDecimal($r['Conciliado MP REAL'] ?? 0),
                    'no_conciliado_mp' => $this->parseDecimal($r['No conciliado MP REAL'] ?? 0),
                    'diferencia_mp' => $this->parseDecimal($r['Diferencia MP sistema vs real'] ?? 0),
                    'porcentaje_diferencia_mp' => $this->parseDecimal($r['% diferencia MP'] ?? 0),
                    // Efectivo
                    'efectivo_total' => $this->parseDecimal($r['Efectivo Total'] ?? 0),
                    'apertura_caja_efectivo' => $this->parseDecimal($r['APERTURA CAJA Efectivo'] ?? 0),
                    'pagos_efectivo' => $this->parseDecimal($r['Pagos en Efectivo'] ?? 0),
                    'recuento_efectivo' => $this->parseDecimal($r['Recuento Efectivo'] ?? 0),
                    'diferencia_efectivo' => $this->parseDecimal($r['Diferencia Efectivo'] ?? 0),
                    'estado_diferencia_efectivo' => $r['Estado Diferencia Efectivo'] ?? null,
                    // Getnet
                    'ventas_getnet_real' => $this->parseDecimal($r['Ventas Getnet Real'] ?? 0),
                    'conciliado_getnet' => $this->parseDecimal($r['Conciliado Getnet REAL'] ?? 0),
                    'no_conciliado_getnet' => $this->parseDecimal($r['No conciliado Getnet REAL'] ?? 0),
                    'diferencia_getnet' => $this->parseDecimal($r['Diferencia Getnet sistema vs real'] ?? 0),
                    // Otros
                    'cta_cte_total' => $this->parseDecimal($r['Cta Cte Total'] ?? 0),
                    'otros' => $this->parseDecimal($r['Otros'] ?? 0),
                    'descuentos' => $this->parseDecimal($r['Descuentos'] ?? 0),
                    'ventas_facturadas' => $this->parseDecimal($r['Ventas facturadas'] ?? 0),
                    'updated_at' => now(),
                ]
            );

            $stats['processed']++;
        }

        return $stats;
    }

    /**
     * Guardar transacciones Getnet
     */
    private function saveGetnetTransactions(WorkflowExecution $execution, int $clientId, array $records): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        foreach (array_chunk($records, 500) as $chunk) {
            $data = array_map(fn($r) => $this->mapGetnetTransaction($execution, $clientId, $r), $chunk);
            $data = array_filter($data, fn($r) => !empty($r['cod_transaccion']));

            if (!empty($data)) {
                ConciliationGetnetTransaction::upsert(
                    array_values($data),
                    ['client_id', 'cod_transaccion'],
                    ['estado_conciliacion', 'tipo_match', 'id_venta_sistema', 'workflow_execution_id', 'updated_at']
                );
            }

            $stats['processed'] += count($chunk);
        }

        return $stats;
    }

    /**
     * Guardar transacciones MercadoPago
     */
    private function saveMpTransactions(WorkflowExecution $execution, int $clientId, array $records): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        foreach (array_chunk($records, 500) as $chunk) {
            $data = array_map(fn($r) => $this->mapMpTransaction($execution, $clientId, $r), $chunk);
            $data = array_filter($data, fn($r) => !empty($r['id_operacion_mp']));

            if (!empty($data)) {
                ConciliationMpTransaction::upsert(
                    array_values($data),
                    ['client_id', 'id_operacion_mp'],
                    ['estado_conciliacion', 'tipo_match', 'id_venta_sistema', 'workflow_execution_id', 'updated_at']
                );
            }

            $stats['processed'] += count($chunk);
        }

        return $stats;
    }

    /**
     * Guardar ventas del sistema
     */
    private function saveSystemSales(WorkflowExecution $execution, int $clientId, array $records, string $source): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        foreach (array_chunk($records, 500) as $chunk) {
            $data = array_map(fn($r) => $this->mapSystemSale($execution, $clientId, $r, $source), $chunk);
            $data = array_filter($data, fn($r) => !empty($r['id_ticket']));

            if (!empty($data)) {
                ConciliationSystemSale::upsert(
                    array_values($data),
                    ['client_id', 'id_ticket', 'source'],
                    ['estado_conciliacion', 'tipo_match', 'id_operacion_conciliado', 'conciliado', 'workflow_execution_id', 'updated_at']
                );
            }

            $stats['processed'] += count($chunk);
        }

        return $stats;
    }

    /**
     * Guardar turnos procesados
     */
    private function saveTurnos(WorkflowExecution $execution, int $clientId, array $records): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        $data = array_map(fn($r) => $this->mapShift($execution, $clientId, $r), $records);

        ConciliationShift::upsert(
            $data,
            ['client_id', 'fecha_apertura', 'hora_apertura', 'turno'],
            ['cantidad_comensales', 'recuento_efectivo', 'apertura_caja', 'workflow_execution_id', 'updated_at']
        );

        $stats['processed'] = count($records);
        return $stats;
    }

    /**
     * Guardar devoluciones
     */
    private function saveRefunds(WorkflowExecution $execution, int $clientId, array $records): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        $data = array_map(fn($r) => $this->mapRefund($execution, $clientId, $r), $records);

        ConciliationRefund::upsert(
            $data,
            ['client_id', 'unique_hash'],
            ['workflow_execution_id', 'updated_at']
        );

        $stats['processed'] = count($records);
        return $stats;
    }

    /**
     * Guardar movimientos de caja
     */
    private function saveCashMovements(WorkflowExecution $execution, int $clientId, array $records): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        foreach (array_chunk($records, 200) as $chunk) {
            $data = array_map(fn($r) => $this->mapCashMovement($execution, $clientId, $r), $chunk);

            ConciliationCashMovement::upsert(
                $data,
                ['client_id', 'unique_hash'],
                ['workflow_execution_id', 'updated_at']
            );

            $stats['processed'] += count($chunk);
        }

        return $stats;
    }

    /**
     * Guardar MP negativos
     */
    private function saveMpNegatives(WorkflowExecution $execution, int $clientId, array $records): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        $data = array_map(fn($r) => $this->mapMpNegative($execution, $clientId, $r), $records);
        $data = array_filter($data, fn($r) => !empty($r['id_operacion_mp']));

        if (!empty($data)) {
            ConciliationMpNegative::upsert(
                array_values($data),
                ['client_id', 'id_operacion_mp'],
                ['workflow_execution_id', 'updated_at']
            );
        }

        $stats['processed'] = count($records);
        return $stats;
    }

    // ==================== MAPPERS ====================

    private function mapShift(WorkflowExecution $execution, int $clientId, array $r): array
    {
        return [
            'workflow_execution_id' => $execution->id,
            'client_id' => $clientId,
            'fecha_apertura' => $this->parseDate($r['Fecha Apertura'] ?? $r['Fecha'] ?? null),
            'hora_apertura' => $this->parseTime($r['Hs Ap. Caja'] ?? null),
            'fecha_cierre' => $this->parseDate($r['Fecha Cierre'] ?? null),
            'hora_cierre' => $this->parseTime($r['Hs Cierre Caja'] ?? null),
            'turno' => $r['TURNO'] ?? null,
            'encargado' => $r['Encargado'] ?? null,
            'cantidad_comensales' => $r['Cantidad de comensales'] ?? 0,
            'recuento_efectivo' => $r['Recuento Efectivo'] ?? 0,
            'apertura_caja' => $r['APERTURA CAJA Efectivo'] ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function mapGetnetTransaction(WorkflowExecution $execution, int $clientId, array $r): array
    {
        return [
            'workflow_execution_id' => $execution->id,
            'client_id' => $clientId,
            'nro_establecimiento' => $r['Nro de Establecimiento'] ?? null,
            'nombre_establecimiento' => $r['Nombre Establecimiento'] ?? null,
            'fecha_operacion' => $this->parseDateTime($r['Fecha de operacion'] ?? null),
            'billetera' => $r['Billetera'] ?? null,
            'marca' => $r['Marca'] ?? null,
            'tipo_tarjeta' => $r['Tipo'] ?? null,
            'tarjeta_ultimos4' => $r['Tarjeta'] ?? null,
            'tipo_transaccion' => $r['Tipo de Transaccion'] ?? null,
            'canal' => $r['Canal'] ?? null,
            'modo_canal' => $r['Modo de canal'] ?? null,
            'codigo_pos' => $r['Codigo del POS'] ?? null,
            'estado_venta' => $r['Estado venta'] ?? null,
            'cod_transaccion' => $r['Cod de Transaccion'] ?? null,
            'cod_transaccion_externo' => $r['Cod. Transaccion Externo'] ?? null,
            'nro_cupon' => $r['Nro de cupon'] ?? null,
            'cod_autorizacion' => $r['Cod. Aut.'] ?? null,
            'plan_cuotas' => $r['Plan cuotas'] ?? null,
            'moneda' => $r['Moneda'] ?? 'ARS',
            'monto_bruto' => $this->parseDecimal($r['Monto Bruto Transaccion'] ?? 0),
            'arancel' => $this->parseDecimal($r['Arancel'] ?? 0),
            'iva_arancel' => $this->parseDecimal($r['IVA Arancel'] ?? 0),
            'propina' => $this->parseDecimal($r['Propina'] ?? 0),
            'monto_neto' => $this->parseDecimal($r['Monto Neto Transaccion'] ?? 0),
            'fecha_liquidacion' => $this->parseDate($r['Fecha de Liquidacion'] ?? null),
            'fecha_pago' => $this->parseDate($r['Fecha estimada de Pago'] ?? null),
            'cod_liquidacion' => $r['Cod. de Liquidacion'] ?? null,
            'turno' => $r['TURNO'] ?? null,
            'estado_conciliacion' => $r['Estado'] ?? null,
            'tipo_match' => $r['Tipo Match'] ?? null,
            'id_venta_sistema' => $this->cleanId($r['ID Venta Sistema (Conc.)'] ?? null),
            'fecha_solo' => $this->parseDate($r['Fecha (Solo)'] ?? null),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function mapMpTransaction(WorkflowExecution $execution, int $clientId, array $r): array
    {
        return [
            'workflow_execution_id' => $execution->id,
            'client_id' => $clientId,
            'fecha' => $this->parseDate($r['Fecha (Solo)'] ?? $r['FECHA DE ORIGEN'] ?? null),
            'hora' => $this->extractTimeFromDateTime($r['FECHA DE ORIGEN'] ?? null),
            'id_operacion_mp' => $r['ID DE OPERACIÓN EN MERCADO PAGO'] ?? null,
            'monto_neto' => $this->parseDecimal($r['MONTO NETO DE LA OPERACIÓN QUE IMPACTÓ TU DINERO'] ?? $r['MONTO NETO DE LA OPERACIÓN'] ?? 0),
            'tipo_movimiento' => $r['TIPO DE OPERACIÓN'] ?? null,
            'medio_pago' => $r['MEDIO DE PAGO'] ?? null,
            'metodo_pago' => $r['TIPO DE MEDIO DE PAGO'] ?? null,
            'cuotas' => $r['CUOTAS'] ?? null,
            'estado' => $r['Estado'] ?? null,
            'turno' => $r['TURNO'] ?? null,
            'estado_conciliacion' => $r['Estado'] ?? null,
            'tipo_match' => $r['Tipo Match'] ?? null,
            'id_venta_sistema' => $this->cleanId($r['ID Venta Sistema (Conc.)'] ?? null),
            'fecha_solo' => $this->parseDate($r['Fecha (Solo)'] ?? null),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function mapSystemSale(WorkflowExecution $execution, int $clientId, array $r, string $source): array
    {
        $idTicket = $r['ID de venta'] ?? $r['Pago'] ?? null;
        $fechaHora = $r['Fecha_DT'] ?? $r['Fecha'] ?? $r['datetime_col'] ?? null;
        $monto = $r['A Pagar'] ?? $r['Total'] ?? $r['monto_col_numeric'] ?? 0;
        $metodoPago = $r['Medio de cobro'] ?? null;
        $estadoConciliacion = $r['Estado'] ?? null;
        $idOperacionMp = $r['ID Operación MP (Conc.)'] ?? null;
        $idOperacionGetnet = $r['ID Operación Getnet (Conc.)'] ?? null;
        $idOperacionConciliado = $idOperacionMp ?: $idOperacionGetnet;

        return [
            'workflow_execution_id' => $execution->id,
            'client_id' => $clientId,
            'id_ticket' => $this->cleanId($idTicket),
            'fecha_hora' => $this->parseDateTime($fechaHora),
            'hora' => $this->extractTimeFromDateTime($fechaHora),
            'items_count' => 0,
            'monto_total' => $this->parseDecimal($monto),
            'tipo_venta' => null,
            'estado_venta' => $r['Estado_Auditoria'] ?? $estadoConciliacion,
            'metodo_pago' => $metodoPago,
            'turno' => $r['TURNO'] ?? null,
            'estado_conciliacion' => $estadoConciliacion,
            'tipo_match' => $r['Tipo Match'] ?? null,
            'id_operacion_conciliado' => $this->cleanId($idOperacionConciliado),
            'fecha_solo' => $this->parseDate($r['Fecha (Solo)'] ?? null),
            'conciliado' => $estadoConciliacion === 'Conciliado',
            'source' => $source,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function mapCashMovement(WorkflowExecution $execution, int $clientId, array $r): array
    {
        $data = [
            'workflow_execution_id' => $execution->id,
            'client_id' => $clientId,
            'fecha_contable' => $this->parseDate($r['Fecha Contable'] ?? null),
            'fecha_modificacion' => $this->parseDateTime($r['Fecha Modificación'] ?? $r['Fecha'] ?? null),
            'origen' => $r['Origen'] ?? null,
            'clase' => $r['Clase'] ?? null,
            'proveedor_para' => $r['Proveedor / Para'] ?? null,
            'monto' => $this->parseDecimal($r['Monto'] ?? 0),
            'comentario' => $r['Comentario'] ?? null,
            'usuario' => $r['Usuario'] ?? null,
            'tipo' => $r['Tipo'] ?? null,
            'forma_pago' => $r['Forma de Pago'] ?? null,
            'cuenta_contable' => $r['Cuenta Contable'] ?? null,
            'turno' => $r['TURNO'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $data['unique_hash'] = ConciliationCashMovement::generateUniqueHash($data);

        return $data;
    }

    private function mapMpNegative(WorkflowExecution $execution, int $clientId, array $r): array
    {
        return [
            'workflow_execution_id' => $execution->id,
            'client_id' => $clientId,
            'fecha' => $this->parseDate($r['Fecha'] ?? null),
            'hora' => $r['Hora'] ?? null,
            'id_operacion_mp' => $r['ID DE OPERACIÓN EN MERCADO PAGO'] ?? null,
            'monto_neto' => $this->parseDecimal($r['MONTO NETO DE LA OPERACIÓN QUE IMPACTÓ TU DINERO'] ?? 0),
            'turno' => $r['TURNO'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function mapRefund(WorkflowExecution $execution, int $clientId, array $r): array
    {
        $data = [
            'workflow_execution_id' => $execution->id,
            'client_id' => $clientId,
            'producto' => $r['Producto'] ?? null,
            'precio' => $this->parseDecimal($r['Precios'] ?? 0),
            'comentario' => $r['Comentario'] ?? null,
            'fecha_hora_pedido' => $this->parseDateTime($r['Fecha Hora pedido'] ?? null),
            'hora_pedido' => $this->parseTime($r['Hora pedido'] ?? null),
            'hora_anulacion' => $r['Hora Anulación'] ?? null,
            'turno' => $r['TURNO'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $data['unique_hash'] = ConciliationRefund::generateUniqueHash($data);

        return $data;
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
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}\s+\d{2}:\d{2}:\d{2}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y H:i:s', $value)->format('Y-m-d');
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
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}\s+\d{2}:\d{2}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y H:i', $value)->format('Y-m-d H:i:s');
            }
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseTime($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
                return $value;
            }
            if (preg_match('/^\d{2}:\d{2}$/', $value)) {
                return $value . ':00';
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function extractTimeFromDateTime($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('H:i:s');
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

    private function cleanId($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        return trim(str_replace('"', '', (string) $value));
    }
}
