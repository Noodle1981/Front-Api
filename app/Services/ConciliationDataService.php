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

class ConciliationDataService
{
    /**
     * Procesar y guardar datos de conciliación evitando duplicados por cliente
     */
    public function processAndSave(WorkflowExecution $execution, array $response): array
    {
        $stats = [
            'total_processed' => 0,
            'sections' => [],
        ];

        // 1. Validar status
        if (!$this->validateResponse($response)) {
            Log::warning('Conciliation response validation failed', [
                'execution_id' => $execution->id,
                'status' => $response['status'] ?? 'unknown',
            ]);
            return $stats;
        }

        // 2. Obtener client_id desde el batch
        // Si hay branch_id, usamos ese (es la sede). Si no, usamos client_id (empresa central)
        $clientId = $execution->fileBatch?->branch_id ?? $execution->fileBatch?->client_id;

        if (!$clientId) {
            Log::warning('No client_id found for execution', [
                'execution_id' => $execution->id,
            ]);
            return $stats;
        }

        // 3. Persistir en transacción
        DB::transaction(function () use ($execution, $response, $clientId, &$stats) {
            $data = $response['data'];

            $stats['sections']['summaries'] = $this->saveSummaries($execution, $clientId, $data['arqueo_por_turno'] ?? []);
            $stats['sections']['getnet'] = $this->saveGetnetTransactions($execution, $clientId, $data['getnet_conciliado'] ?? []);
            $stats['sections']['mp'] = $this->saveMpTransactions($execution, $clientId, $data['mp_conciliado'] ?? []);
            $stats['sections']['system_conciliado'] = $this->saveSystemSales($execution, $clientId, $data['sistema_conciliado'] ?? [], 'conciliado');
            $stats['sections']['system_ventas'] = $this->saveSystemSales($execution, $clientId, $data['ventas_sistema'] ?? [], 'sistema');
            $stats['sections']['shifts'] = $this->saveShifts($execution, $clientId, $data['turnos_procesados'] ?? []);
            $stats['sections']['cash'] = $this->saveCashMovements($execution, $clientId, $data['caja_adicion'] ?? []);
            $stats['sections']['refunds'] = $this->saveRefunds($execution, $clientId, $data['devoluciones'] ?? []);
            $stats['sections']['mp_negatives'] = $this->saveMpNegatives($execution, $clientId, $data['mp_negativos'] ?? []);

            // Calcular total
            foreach ($stats['sections'] as $section) {
                $stats['total_processed'] += $section['processed'] ?? 0;
            }
        });

        Log::info('Conciliation data processed', [
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
     * Guardar resúmenes por turno (upsert por client_id+fecha+turno+encargado)
     */
    private function saveSummaries(WorkflowExecution $execution, int $clientId, array $records): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        foreach (array_chunk($records, 100) as $chunk) {
            $data = array_map(fn($r) => $this->mapSummary($execution, $clientId, $r), $chunk);

            ConciliationSummary::upsert(
                $data,
                ['client_id', 'fecha', 'turno', 'encargado'],
                $this->getSummaryUpdateColumns()
            );

            $stats['processed'] += count($chunk);
        }

        return $stats;
    }

    /**
     * Guardar transacciones Getnet (upsert por client_id+cod_transaccion)
     */
    private function saveGetnetTransactions(WorkflowExecution $execution, int $clientId, array $records): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        foreach (array_chunk($records, 500) as $chunk) {
            $data = array_map(fn($r) => $this->mapGetnetTransaction($execution, $clientId, $r), $chunk);

            // Filtrar registros sin cod_transaccion
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
     * Guardar transacciones MercadoPago (upsert por client_id+id_operacion_mp)
     */
    private function saveMpTransactions(WorkflowExecution $execution, int $clientId, array $records): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        foreach (array_chunk($records, 500) as $chunk) {
            $data = array_map(fn($r) => $this->mapMpTransaction($execution, $clientId, $r), $chunk);

            // Filtrar registros sin id_operacion_mp
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
     * Guardar ventas del sistema (upsert por client_id+id_ticket+source)
     */
    private function saveSystemSales(WorkflowExecution $execution, int $clientId, array $records, string $source): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        foreach (array_chunk($records, 500) as $chunk) {
            $data = array_map(fn($r) => $this->mapSystemSale($execution, $clientId, $r, $source), $chunk);

            // Filtrar registros sin id_ticket
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
     * Guardar turnos (upsert por client_id+fecha_apertura+hora_apertura+turno)
     */
    private function saveShifts(WorkflowExecution $execution, int $clientId, array $records): array
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
     * Guardar movimientos de caja (upsert por client_id+unique_hash)
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
     * Guardar devoluciones (upsert por client_id+unique_hash)
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
     * Guardar MP negativos (upsert por client_id+id_operacion_mp)
     */
    private function saveMpNegatives(WorkflowExecution $execution, int $clientId, array $records): array
    {
        $stats = ['processed' => 0];

        if (empty($records)) {
            return $stats;
        }

        $data = array_map(fn($r) => $this->mapMpNegative($execution, $clientId, $r), $records);

        // Filtrar sin id_operacion_mp
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

    private function mapSummary(WorkflowExecution $execution, int $clientId, array $r): array
    {
        return [
            'workflow_execution_id' => $execution->id,
            'client_id' => $clientId,
            'fecha' => $this->parseDate($r['Fecha'] ?? null),
            'dia' => $r['Dia'] ?? null,
            'turno' => $r['TURNO'] ?? null,
            'encargado' => $r['Encargado'] ?? null,
            'apertura' => $this->parseDateTime($r['Apertura'] ?? null),
            'cierre' => $this->parseDateTime($r['Cierre'] ?? null),
            'horas_trabajadas' => $r['Horas Trabajadas'] ?? 0,
            'ventas_totales' => $r['Ventas Totales'] ?? 0,
            'cantidad_comensales' => $r['Cantidad de comensales'] ?? 0,
            'ticket_promedio' => $r['Ticket Promedio'] ?? 0,
            'cantidad_tickets' => $r['Cantidad de ticket'] ?? 0,
            'propina' => $r['Propina'] ?? 0,
            // MercadoPago
            'mp_ventas_real' => $r['Ventas MP Real'] ?? 0,
            'mp_ventas_sistema' => $r['Ventas Sistema MP Real'] ?? 0,
            'mp_conciliado' => $r['Conciliado MP REAL'] ?? 0,
            'mp_no_conciliado' => $r['No conciliado MP REAL'] ?? 0,
            'mp_diferencia' => $r['Diferencia MP sistema vs real'] ?? 0,
            'mp_porcentaje' => $r['% diferencia MP'] ?? 0,
            'mp_estado' => $r['Estado Dif MP'] ?? null,
            // Getnet
            'getnet_ventas_real' => $r['Ventas Getnet Real'] ?? 0,
            'getnet_ventas_sistema' => $r['Ventas sistema Real'] ?? 0,
            'getnet_conciliado' => $r['Conciliado Getnet REAL'] ?? 0,
            'getnet_no_conciliado' => $r['No conciliado Getnet REAL'] ?? 0,
            'getnet_diferencia' => $r['Diferencia Getnet sistema vs real'] ?? 0,
            'getnet_porcentaje' => $r['% diferencia Getnet'] ?? 0,
            'getnet_estado' => $r['Estado Dif Getnet'] ?? null,
            // Efectivo
            'efectivo_total' => $r['Efectivo Total'] ?? 0,
            'efectivo_apertura_caja' => $r['APERTURA CAJA Efectivo'] ?? 0,
            'efectivo_recuento' => $r['Recuento Efectivo'] ?? 0,
            'efectivo_diferencia' => $r['Diferencia Efectivo'] ?? 0,
            'efectivo_porcentaje' => $r['% diferencia efectivo'] ?? 0,
            'efectivo_estado' => $r['Estado Diferencia Efectivo'] ?? null,
            // Otros
            'cta_cte_total' => $r['Cta Cte Total'] ?? 0,
            'otros' => $r['Otros'] ?? 0,
            'descuentos' => $r['Descuentos'] ?? 0,
            'ventas_facturadas' => $r['Ventas facturadas'] ?? 0,
            'ideal_facturacion' => $r['Ideal facturacion'] ?? 0,
            'diferencia_facturacion' => $r['Diferencia de facturacion'] ?? 0,
            'porcentaje_facturacion' => $r['% diferencia facturacion'] ?? 0,
            'ventas_por_hora' => json_encode($this->extractHourlySales($r)),
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
            'monto_bruto' => $r['Monto Bruto Transaccion'] ?? 0,
            'arancel' => $this->parseDecimal($r['Arancel'] ?? 0),
            'iva_arancel' => $this->parseDecimal($r['IVA Arancel'] ?? 0),
            'propina' => $this->parseDecimal($r['Propina'] ?? 0),
            'monto_neto' => $r['Monto Neto Transaccion'] ?? 0,
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
            'fecha' => $this->parseDate($r['Fecha'] ?? null),
            'hora' => $r['Hora'] ?? null,
            'id_operacion_mp' => $r['ID DE OPERACIÓN EN MERCADO PAGO'] ?? $r['id_operacion_mp'] ?? null,
            'monto_neto' => $r['MONTO NETO DE LA OPERACIÓN QUE IMPACTÓ TU DINERO'] ?? $r['monto_neto'] ?? 0,
            'tipo_movimiento' => $r['TIPO DE MOVIMIENTO'] ?? null,
            'medio_pago' => $r['MEDIO DE PAGO DE ORIGEN'] ?? null,
            'metodo_pago' => $r['MÉTODO DE PAGO'] ?? null,
            'cuotas' => $r['CUOTAS'] ?? null,
            'estado' => $r['Estado'] ?? null,
            'turno' => $r['TURNO'] ?? null,
            'estado_conciliacion' => $r['Estado Conciliacion'] ?? $r['Estado'] ?? null,
            'tipo_match' => $r['Tipo Match'] ?? null,
            'id_venta_sistema' => $this->cleanId($r['ID Venta Sistema (Conc.)'] ?? null),
            'fecha_solo' => $this->parseDate($r['Fecha (Solo)'] ?? $r['Fecha'] ?? null),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function mapSystemSale(WorkflowExecution $execution, int $clientId, array $r, string $source): array
    {
        // Handle both sistema_conciliado format and ventas_sistema format
        $idTicket = $r['ID Ticket'] ?? $r['id_ticket'] ?? $r['ID de venta'] ?? $r['Pago'] ?? null;
        $fechaHora = $r['Fecha Hora'] ?? $r['Fecha_DT'] ?? $r['Fecha'] ?? $r['datetime_col'] ?? null;
        $monto = $r['Monto Total'] ?? $r['monto'] ?? $r['A Pagar'] ?? $r['Total'] ?? $r['monto_col_numeric'] ?? 0;
        $metodoPago = $r['Metodo Pago'] ?? $r['Medio de cobro'] ?? null;
        $estadoConciliacion = $r['Estado Conciliacion'] ?? $r['Estado'] ?? null;
        $idOperacionConciliado = $r['ID Operacion Conciliado'] ?? $r['ID Operación MP (Conc.)'] ?? $r['ID Operación Getnet (Conc.)'] ?? null;

        return [
            'workflow_execution_id' => $execution->id,
            'client_id' => $clientId,
            'id_ticket' => $this->cleanId($idTicket),
            'fecha_hora' => $this->parseDateTime($fechaHora),
            'hora' => $r['Hora'] ?? null,
            'items_count' => $r['Items'] ?? 0,
            'monto_total' => $monto,
            'tipo_venta' => $r['Tipo Venta'] ?? null,
            'estado_venta' => $r['Estado'] ?? $r['Estado_Auditoria'] ?? null,
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

    private function mapShift(WorkflowExecution $execution, int $clientId, array $r): array
    {
        return [
            'workflow_execution_id' => $execution->id,
            'client_id' => $clientId,
            'fecha_apertura' => $this->parseDate($r['Fecha Apertura'] ?? null),
            'hora_apertura' => $r['Hs Ap. Caja'] ?? null,
            'fecha_cierre' => $this->parseDate($r['Fecha Cierre'] ?? null),
            'hora_cierre' => $r['Hs Cierre Caja'] ?? null,
            'turno' => $r['TURNO'] ?? null,
            'encargado' => $r['Encargado'] ?? null,
            'cantidad_comensales' => $r['Cantidad de comensales'] ?? 0,
            'recuento_efectivo' => $r['Recuento Efectivo'] ?? 0,
            'apertura_caja' => $r['APERTURA CAJA Efectivo'] ?? 0,
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
            'fecha_modificacion' => $this->parseDateTime($r['Fecha Modificación'] ?? null),
            'origen' => $r['Origen'] ?? null,
            'clase' => $r['Clase'] ?? null,
            'proveedor_para' => $r['Proveedor / Para'] ?? null,
            'monto' => $r['Monto'] ?? 0,
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

    private function mapRefund(WorkflowExecution $execution, int $clientId, array $r): array
    {
        $data = [
            'workflow_execution_id' => $execution->id,
            'client_id' => $clientId,
            'producto' => $r['Producto'] ?? null,
            'precio' => $r['Precios'] ?? 0,
            'comentario' => $r['Comentario'] ?? null,
            'fecha_hora_pedido' => $this->parseDateTime($r['Fecha Hora pedido'] ?? null),
            'hora_pedido' => $r['Hora pedido'] ?? null,
            'hora_anulacion' => $r['Hora Anulación'] ?? null,
            'turno' => $r['TURNO'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $data['unique_hash'] = ConciliationRefund::generateUniqueHash($data);

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
            'monto_neto' => $r['MONTO NETO DE LA OPERACIÓN QUE IMPACTÓ TU DINERO'] ?? 0,
            'turno' => $r['TURNO'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // ==================== HELPERS ====================

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Formato "DD/MM/YYYY"
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            }
            // Formato ISO o similar
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
            // Formato "DD/MM/YYYY HH:MM:SS"
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}\s+\d{2}:\d{2}:\d{2}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y H:i:s', $value)->format('Y-m-d H:i:s');
            }
            // Formato ISO o similar
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

    private function cleanId($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        return trim(str_replace('"', '', $value));
    }

    private function extractHourlySales(array $r): array
    {
        $sales = [];
        for ($h = 0; $h < 24; $h++) {
            $key = sprintf('%02d:00 - %02d:00', $h, $h + 1);
            $sales[$key] = $r[$key] ?? 0;
        }
        return $sales;
    }

    private function getSummaryUpdateColumns(): array
    {
        return [
            'workflow_execution_id',
            'ventas_totales',
            'cantidad_comensales',
            'ticket_promedio',
            'cantidad_tickets',
            'propina',
            'mp_ventas_real',
            'mp_ventas_sistema',
            'mp_conciliado',
            'mp_no_conciliado',
            'mp_diferencia',
            'mp_porcentaje',
            'mp_estado',
            'getnet_ventas_real',
            'getnet_ventas_sistema',
            'getnet_conciliado',
            'getnet_no_conciliado',
            'getnet_diferencia',
            'getnet_porcentaje',
            'getnet_estado',
            'efectivo_total',
            'efectivo_apertura_caja',
            'efectivo_recuento',
            'efectivo_diferencia',
            'efectivo_porcentaje',
            'efectivo_estado',
            'cta_cte_total',
            'otros',
            'descuentos',
            'ventas_facturadas',
            'ideal_facturacion',
            'diferencia_facturacion',
            'porcentaje_facturacion',
            'ventas_por_hora',
            'updated_at',
        ];
    }
}
