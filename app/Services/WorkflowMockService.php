<?php

namespace App\Services;

/**
 * Mock service to simulate Python server response
 * This will be replaced with actual HTTP call to Python server
 */
class WorkflowMockService
{
    /**
     * Generate mock data for workflow execution
     * This simulates what the Python server will return
     */
    public static function getMockConciliacionData(): array
    {
        return [
            'success' => true,
            'execution_time_ms' => 1250,
            'data' => [
                'metadata' => [
                    'fecha' => '12/02/2025',
                    'dia' => 'Martes',
                    'turno' => 'MAÑANA',
                    'encargado' => 'Felipe',
                    'hs_apertura' => '11:10',
                    'hs_cierre' => '20:19',
                    'sucursal' => 'PARADOR',
                ],
                'enviar_sucursal' => [
                    'total_ventas' => '816,500.00',
                    'parador' => [
                        'cantidad_tickets' => 9,
                        'ticket_promedio' => '90,722.22',
                        'cantidad_comensales' => 32,
                        'comensales_promedio' => '25,515.63',
                    ],
                    'horarios_venta' => [
                        'apertura' => '11:10',
                        'primera_venta' => '14:37',
                        'ultima_venta' => '20:09',
                        'cierre' => '20:19',
                        'intervalo_primera_venta' => '3:27',
                        'duracion_jornada' => '9:09',
                        'intervalo_ultima_venta' => '0:10',
                    ],
                    'diferencias_caja' => [
                        'mercado_pago' => [
                            'real' => '169,100.00',
                            'real_no_conciliado' => '0.00',
                            'sistema' => '0.00',
                            'sistema_no_conciliado' => '169,100.00',
                            'diferencia' => '-169,100.00',
                            'porcentaje' => '0.00',
                            'porcentaje_conciliacion' => '0.00',
                        ],
                        'getnet' => [
                            'real' => '747,740.00',
                            'real_no_conciliado' => '0.00',
                            'sistema' => '747,740.00',
                            'sistema_no_conciliado' => '0.00',
                            'diferencia' => '0.00',
                            'porcentaje' => '0.00',
                            'porcentaje_conciliacion' => '0.00',
                        ],
                        'efectivo' => [
                            'apertura_caja' => '138,260.00',
                            'efectivo_real' => '143,900.00',
                            'pagos' => '91,100.00',
                            'recuento_real' => '191,060.00',
                            'diferencia' => '191,060.00',
                            'porcentaje' => '0.00',
                            'porcentaje_conciliacion' => '0.00',
                        ],
                        'cta_cte' => [
                            'sistema' => '0.00',
                            'conciliado_sistema' => '0.00',
                            'real' => '0.00',
                        ],
                    ],
                    'ventas_por_hora' => [
                        ['hora' => '14:00', 'monto' => 150600],
                        ['hora' => '15:00', 'monto' => 566700],
                        ['hora' => '16:00', 'monto' => 102440],
                    ],
                    'facturacion' => [
                        'real' => '841,700.00',
                        'ideal' => '747,740.00',
                        'diferencia' => '93,960.00',
                        'desvio_porcentaje' => '12.57',
                    ],
                ],
                'enviar_egresos' => [
                    'caja_adicion' => [
                        ['importe' => '5,000.00', 'hora' => '15:30', 'detalle' => 'Compra de insumos'],
                        ['importe' => '2,500.00', 'hora' => '17:45', 'detalle' => 'Pago a proveedor'],
                    ],
                    'mercado_pago' => [
                        ['importe' => '1,200.00', 'hora' => '16:20', 'detalle' => 'Devolución cliente'],
                    ],
                    'total_caja_adicion' => '7,500.00',
                    'total_mercado_pago' => '1,200.00',
                ],
                'enviar_no_conciliados' => [
                    'mercado_pago' => [
                        'total_real_no_conciliado' => '5,000.00',
                        'total_sistema_no_conciliado' => '3,500.00',
                        'total_no_conciliado' => '8,500.00',
                        'items_real' => [
                            ['id_venta' => 'MP-001', 'hora' => '14:30', 'monto' => '2,500.00'],
                            ['id_venta' => 'MP-002', 'hora' => '15:45', 'monto' => '2,500.00'],
                        ],
                        'items_sistema' => [
                            ['id_venta' => 'MP-003', 'hora' => '16:00', 'monto' => '3,500.00'],
                        ],
                    ],
                    'getnet' => [
                        'total_real_no_conciliado' => '100.00',
                        'total_sistema_no_conciliado' => '100.00',
                        'total_no_conciliado' => '200.00',
                        'items_real' => [
                            ['id_venta' => '1', 'hora' => '12:00', 'monto' => '100.00'],
                        ],
                        'items_sistema' => [
                            ['id_venta' => '1', 'hora' => '12:00', 'monto' => '100.00'],
                        ],
                    ],
                    'efectivo_cta_cte' => [
                        'total_real_no_conciliado' => '1,500.00',
                        'total_sistema_no_conciliado' => '1,500.00',
                        'total_no_conciliado' => '3,000.00',
                        'items_real' => [
                            ['id_venta' => 'EF-001', 'hora' => '18:00', 'monto' => '1,500.00'],
                        ],
                        'items_sistema' => [
                            ['id_venta' => 'EF-001', 'hora' => '18:00', 'monto' => '1,500.00'],
                        ],
                    ],
                ],
                'enviar_anulaciones' => [
                    [
                        'id_comanda' => 'CMD-123',
                        'camarero_mesa' => 'Juan - Mesa 5',
                        'producto' => 'Hamburguesa Completa',
                        'comentario' => 'Cliente canceló pedido',
                        'hora_anulacion' => '15:30',
                        'precio' => '8,500.00',
                    ],
                    [
                        'id_comanda' => 'CMD-124',
                        'camarero_mesa' => 'María - Mesa 8',
                        'producto' => 'Pizza Napolitana',
                        'comentario' => 'Error en pedido',
                        'hora_anulacion' => '16:45',
                        'precio' => '12,000.00',
                    ],
                ],
            ],
        ];
    }
}
