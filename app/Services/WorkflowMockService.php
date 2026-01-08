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
                    'fecha' => '08/01/2026',
                    'dia' => 'Jueves',
                    'turno' => 'NOCHE',
                    'encargado' => 'Carlos Méndez',
                    'hs_apertura' => '18:00',
                    'hs_cierre' => '02:30',
                    'sucursal' => 'CENTRAL',
                ],
                'enviar_sucursal' => [
                    'total_ventas' => '4,285,750.00',
                    'parador' => [
                        'cantidad_tickets' => 87,
                        'ticket_promedio' => '49,261.49',
                        'cantidad_comensales' => 312,
                        'comensales_promedio' => '13,735.10',
                    ],
                    'horarios_venta' => [
                        'apertura' => '18:00',
                        'primera_venta' => '6:12:00 pm',
                        'ultima_venta' => '2:15',
                        'cierre' => '2:30',
                        'intervalo_primera_venta' => '0:12:00',
                        'duracion_jornada' => '8:30:00',
                        'intervalo_ultima_venta' => '0:15',
                    ],
                    'diferencias_caja' => [
                        'mercado_pago' => [
                            'real' => '1,245,800.00',
                            'real_no_conciliado' => '45,200.00',
                            'sistema' => '1,198,600.00',
                            'sistema_no_conciliado' => '2,000.00',
                            'diferencia' => '47,200.00',
                            'porcentaje' => '3.79',
                            'porcentaje_conciliacion' => '96.37',
                        ],
                        'getnet' => [
                            'real' => '1,876,450.00',
                            'real_no_conciliado' => '12,500.00',
                            'sistema' => '1,889,950.00',
                            'sistema_no_conciliado' => '26,000.00',
                            'diferencia' => '-13,500.00',
                            'porcentaje' => '-0.72',
                            'porcentaje_conciliacion' => '99.33',
                        ],
                        'efectivo' => [
                            'apertura_caja' => '150,000.00',
                            'efectivo_real' => '1,163,500.00',
                            'pagos' => '285,400.00',
                            'recuento_real' => '1,028,100.00',
                            'diferencia' => '-3,200.00',
                            'porcentaje' => '-0.31',
                            'porcentaje_conciliacion' => '99.69',
                        ],
                        'cta_cte' => [
                            'sistema' => '156,800.00',
                            'conciliado_sistema' => '156,800.00',
                            'real' => '156,800.00',
                        ],
                    ],
                    'ventas_por_hora' => [
                        ['hora' => '18:00', 'monto' => 245600],
                        ['hora' => '19:00', 'monto' => 387450],
                        ['hora' => '20:00', 'monto' => 652300],
                        ['hora' => '21:00', 'monto' => 845200],
                        ['hora' => '22:00', 'monto' => 723150],
                        ['hora' => '23:00', 'monto' => 568400],
                        ['hora' => '00:00', 'monto' => 456200],
                        ['hora' => '01:00', 'monto' => 287450],
                        ['hora' => '02:00', 'monto' => 120000],
                    ],
                    'facturacion' => [
                        'real' => '4,442,550.00',
                        'ideal' => '4,285,750.00',
                        'diferencia' => '156,800.00',
                        'desvio_porcentaje' => '3.66',
                    ],
                ],
                'enviar_egresos' => [
                    'caja_adicion' => [
                        ['importe' => '45,000.00', 'hora' => '19:15', 'detalle' => 'Compra de hielo - Proveedor FREEZE'],
                        ['importe' => '28,500.00', 'hora' => '20:30', 'detalle' => 'Verduras frescas - Mercado Central'],
                        ['importe' => '15,200.00', 'hora' => '21:45', 'detalle' => 'Bebidas gaseosas - Distribuidora Sur'],
                        ['importe' => '62,000.00', 'hora' => '22:10', 'detalle' => 'Carne vacuna - Frigorífico Norte'],
                        ['importe' => '8,700.00', 'hora' => '23:00', 'detalle' => 'Productos de limpieza'],
                        ['importe' => '35,000.00', 'hora' => '23:45', 'detalle' => 'Pago a repartidor delivery'],
                        ['importe' => '91,000.00', 'hora' => '01:30', 'detalle' => 'Cierre cuenta proveedor mensual'],
                    ],
                    'mercado_pago' => [
                        ['importe' => '18,500.00', 'hora' => '19:40', 'detalle' => 'Devolución por error en pedido - Mesa 12'],
                        ['importe' => '12,300.00', 'hora' => '21:20', 'detalle' => 'Reembolso cliente insatisfecho - Delivery'],
                        ['importe' => '45,000.00', 'hora' => '22:50', 'detalle' => 'Ajuste por promoción no aplicada'],
                        ['importe' => '8,200.00', 'hora' => '00:15', 'detalle' => 'Devolución producto agotado'],
                    ],
                    'total_caja_adicion' => '285,400.00',
                    'total_mercado_pago' => '84,000.00',
                ],
                'enviar_no_conciliados' => [
                    'mercado_pago' => [
                        'total_real_no_conciliado' => '45,200.00',
                        'total_sistema_no_conciliado' => '2,000.00',
                        'total_no_conciliado' => '47,200.00',
                        'items_real' => [
                            ['id_venta' => 'MP-78541', 'hora' => '19:23', 'monto' => '12,500.00'],
                            ['id_venta' => 'MP-78562', 'hora' => '20:45', 'monto' => '8,700.00'],
                            ['id_venta' => 'MP-78589', 'hora' => '21:18', 'monto' => '15,000.00'],
                            ['id_venta' => 'MP-78612', 'hora' => '23:05', 'monto' => '9,000.00'],
                        ],
                        'items_sistema' => [
                            ['id_venta' => 'SIS-45231', 'hora' => '22:30', 'monto' => '2,000.00'],
                        ],
                    ],
                    'getnet' => [
                        'total_real_no_conciliado' => '12,500.00',
                        'total_sistema_no_conciliado' => '26,000.00',
                        'total_no_conciliado' => '38,500.00',
                        'items_real' => [
                            ['id_venta' => 'GN-34521', 'hora' => '20:12', 'monto' => '7,500.00'],
                            ['id_venta' => 'GN-34587', 'hora' => '22:48', 'monto' => '5,000.00'],
                        ],
                        'items_sistema' => [
                            ['id_venta' => 'SIS-45289', 'hora' => '19:55', 'monto' => '8,000.00'],
                            ['id_venta' => 'SIS-45312', 'hora' => '21:30', 'monto' => '6,500.00'],
                            ['id_venta' => 'SIS-45345', 'hora' => '23:15', 'monto' => '7,000.00'],
                            ['id_venta' => 'SIS-45378', 'hora' => '00:45', 'monto' => '4,500.00'],
                        ],
                    ],
                    'efectivo_cta_cte' => [
                        'total_real_no_conciliado' => '8,500.00',
                        'total_sistema_no_conciliado' => '5,300.00',
                        'total_no_conciliado' => '13,800.00',
                        'items_real' => [
                            ['id_venta' => 'EF-12478', 'hora' => '20:30', 'monto' => '3,500.00'],
                            ['id_venta' => 'EF-12512', 'hora' => '22:15', 'monto' => '2,800.00'],
                            ['id_venta' => 'EF-12545', 'hora' => '01:10', 'monto' => '2,200.00'],
                        ],
                        'items_sistema' => [
                            ['id_venta' => 'SIS-45401', 'hora' => '21:45', 'monto' => '3,200.00'],
                            ['id_venta' => 'SIS-45423', 'hora' => '23:50', 'monto' => '2,100.00'],
                        ],
                    ],
                ],
                'enviar_anulaciones' => [
                    [
                        'id_comanda' => 'CMD-8745',
                        'camarero_mesa' => 'Roberto - Mesa 3',
                        'producto' => 'Milanesa Napolitana',
                        'comentario' => 'Cliente alérgico a queso',
                        'hora_anulacion' => '19:35',
                        'precio' => '18,500.00',
                        'total_anulaciones' => '156,800.00',
                    ],
                    [
                        'id_comanda' => 'CMD-8752',
                        'camarero_mesa' => 'Laura - Mesa 8',
                        'producto' => 'Pizza Fugazzeta Grande',
                        'comentario' => 'Demora excesiva en preparación',
                        'hora_anulacion' => '20:20',
                        'precio' => '24,000.00',
                        'total_anulaciones' => '156,800.00',
                    ],
                    [
                        'id_comanda' => 'CMD-8768',
                        'camarero_mesa' => 'Martín - Mesa 15',
                        'producto' => 'Bife de Chorizo 400g',
                        'comentario' => 'Punto de cocción incorrecto',
                        'hora_anulacion' => '21:10',
                        'precio' => '32,500.00',
                        'total_anulaciones' => '156,800.00',
                    ],
                    [
                        'id_comanda' => 'CMD-8791',
                        'camarero_mesa' => 'Ana - Mesa 22',
                        'producto' => 'Ensalada Caesar + Limonada',
                        'comentario' => 'Error al tomar pedido',
                        'hora_anulacion' => '22:05',
                        'precio' => '15,800.00',
                        'total_anulaciones' => '156,800.00',
                    ],
                    [
                        'id_comanda' => 'CMD-8823',
                        'camarero_mesa' => 'Roberto - Delivery #456',
                        'producto' => 'Combo Familiar x4',
                        'comentario' => 'Cliente canceló antes de despacho',
                        'hora_anulacion' => '23:30',
                        'precio' => '45,000.00',
                        'total_anulaciones' => '156,800.00',
                    ],
                    [
                        'id_comanda' => 'CMD-8856',
                        'camarero_mesa' => 'Laura - Mesa 5',
                        'producto' => 'Tabla de Quesos Premium',
                        'comentario' => 'Producto agotado',
                        'hora_anulacion' => '00:15',
                        'precio' => '21,000.00',
                        'total_anulaciones' => '156,800.00',
                    ],
                ],
            ],
        ];
    }
}
