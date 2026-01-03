# Análisis de Archivos Excel Reales

## Resumen

Se analizaron los 6 archivos Excel proporcionados y se extrajeron las estructuras reales de columnas.

## Hallazgos Importantes

### Discrepancias con Requerimientos Originales

| Requerimiento Original | Archivo Real | Nota |
|------------------------|--------------|------|
| `Prueba_MP.xlsx` | `Ventas MP.xlsx` | ⚠️ Nombre diferente |
| `Reporte_Ventas.xlsx` | `Reporte Ventas.xlsx` | ⚠️ Sin guion bajo |
| `Reporte_getnet.xlsx` | `Reporte getnet.xlsx` | ⚠️ Sin guion bajo |
| `Caja_Adicion.xlsx` | `Caja Adicion.xlsx` | ⚠️ Sin guion bajo |

### Nombres de Columnas

Varios nombres de columnas difieren de los especificados en el requerimiento original.

---

## Estructura Detallada por Archivo

### 1. Turnos.xlsx

**Estadísticas:**
- Columnas: 10
- Filas de datos: 41

**Columnas:**
1. Fecha
2. Fecha Apertura
3. Hs Ap. Caja
4. Fecha Cierre
5. Hs Cierre Caja
6. TURNO
7. Encargado
8. APERTURA CAJA Efectivo
9. Recuento Efectivo
10. Cantidad de comensales ⚠️ (Nueva, no estaba en requerimientos)

---

### 2. Reporte Ventas.xlsx

**Estadísticas:**
- Columnas: 28
- Filas de datos: 1,619

**Columnas:**
1. FechaCierre
2. Comanda
3. Caja
4. Mesa
5. Pago
6. Total
7. Descuentos
8. A Pagar
9. Propina
10. Pagos
11. Boleta
12. #Boleta
13. Comentario Descuento
14. Efectivo
15. Tarjeta Crédito
16. Tarjeta Débito
17. MercadoPago QR
18. MercadoPago Checkout
19. Cta Cte
20. Getnet
21. Cortesia
22. Mercado Pago
23. Consumo Dueños
24. Consumo Personal
25. Comentarios
26. Cliente
27. Tipo Documento
28. Cliente Facturación

**Diferencias con requerimiento:**
- Requerimiento decía: `FechaCierre, Comanda, Total, Propina, Pagos, Boleta, Efectivo, Getnet, Mercado Pago, Cta Cte`
- Real tiene 28 columnas vs 10 esperadas

---

### 3. Reporte getnet.xlsx

**Estadísticas:**
- Columnas: 40
- Filas de datos: 711

**Columnas:**
1. Nro de Establecimiento
2. Nombre Establecimiento
3. Fecha de operacion
4. Billetera
5. Marca
6. Tipo
7. Tarjeta
8. Tipo de Transaccion
9. Canal
10. Modo de canal
11. Codigo del POS
12. Estado
13. Cod de Transaccion
14. Cod. Transaccion Externo
15. Nro de cupon
16. Cod. Aut.
17. Plan cuotas
18. Moneda
19. Monto Bruto Transaccion
20. Arancel
21. IVA Arancel
22. Costo Financiero
23. IVA CFT
24. Tipo de Producto
25. Costo Anticipacion
26. IVA Anticipacion
27. Costo P. Inmediato
28. IVA P. Inmediato
29. Nombre de Promocion
30. Porc Reintegro
31. Porc Aporte Comercio
32. Monto Aporte Comercio
33. Propina
34. Monto Neto Transaccion
35. Fecha de Liquidacion
36. Fecha estimada de Pago
37. Cod. de Liquidacion
38. Modo de Integración
39. Tipo de Integración
40. Conexión

**Diferencias con requerimiento:**
- Requerimiento decía: `Fecha de operacion, Cod de Transaccion, Monto Bruto Transaccion, Arancel, Estado`
- Real tiene 40 columnas vs 5 esperadas

---

### 4. Ventas MP.xlsx

**Estadísticas:**
- Columnas: 49
- Filas de datos: 927

**Columnas:**
1. NÚMERO DE IDENTIFICACIÓN
2. ID DE OPERACIÓN EN MERCADO PAGO
3. CÓDIGO DE LA CUENTA DEL VENDEDOR
4. TIPO DE MEDIO DE PAGO
5. MEDIO DE PAGO
6. PAÍS DE ORIGEN DE LA CUENTA DE MERCADO PAGO
7. TIPO DE OPERACIÓN
8. VALOR DE LA COMPRA
9. MONEDA
10. MONTO RECIBIDO POR COMPRAS POR SPLIT
11. FECHA DE ORIGEN
12. COMISIONES + IVA
13. MONTO NETO DE LA OPERACIÓN QUE IMPACTÓ TU DINERO
14. MONEDA DE LA LIQUIDACIÓN
15. FECHA DE APROBACIÓN
16. MONTO NETO DE LA OPERACIÓN
17. CUPÓN DE DESCUENTO
18. DATOS EXTRA
19. COMISIÓN DE MERCADO LIBRE + IVA
20. COMISIÓN POR OFRECER CUOTAS SIN INTERÉS
21. COSTO DE ENVÍO
22. IMPUESTOS COBRADOS POR RETENCIONES DE IIBB
23. CUOTAS
24. DETALLE DE IMPUESTOS
25. ID DE CAJA
26. ID DE LOCAL
27. NOMBRE DE LOCAL
28. ID DE CAJA DEFINIDO POR EL USUARIO
29. NOMBRE DE CAJA
30. ID DE LOCAL DEFINIDO POR EL USUARIO
31. ID DE LA ORDEN
32. ID DEL ENVÍO
33. MODO DE ENVÍO
34. ID DEL PAQUETE
35. IMPUESTOS DESAGREGADOS
36. NÚMERO DE SERIE DEL LECTOR (S/N)
37. BILLETERA VIRTUAL
38. BANCO DE ORIGEN
39. NÚMERO INICIAL DE TARJETA
40. OPERATION_TAGS
41. TIPO DE IDENTIFICACIÓN DEL PAGADOR
42. NÚMERO DE IDENTIFICACIÓN DEL PAGADOR
43. PAGADOR
44. CANAL DE VENTA
45. PLATAFORMA DE COBRO
46. FECHA DE LIQUIDACIÓN DEL DINERO
47. CÓDIGO DE PRODUCTO SKU
48. DETALLE DE LA VENTA
49. FRANCHISE

**Diferencias con requerimiento:**
- Requerimiento decía: `FECHA DE ORIGEN (ISO), ID DE OPERACIÓN EN MERCADO PAGO, VALOR DE LA COMPRA, MEDIO DE PAGO, NÚMERO DE SERIE DEL LECTOR`
- Real tiene 49 columnas vs 5 esperadas
- Nombre de archivo: "Ventas MP.xlsx" vs "Prueba_MP.xlsx"

---

### 5. Devoluciones.xlsx

**Estadísticas:**
- Columnas: 14
- Filas de datos: 145

**Columnas:**
1. ID Comanda
2. Camarero Mesa
3. Mesa
4. Producto
5. Precios
6. Comentario
7. ID Usuario Pedidos
8. Hora pedido
9. ID Usuario Anula
10. Hora Anulación
11. Descuadre
12. Recuperable
13. DTE Emision
14. DTE Anulación

**Diferencias con requerimiento:**
- Requerimiento decía: `ID Comanda, Producto, Precios, Hora pedido, Hora Anulación, Descuadre, DTE Emision`
- Real tiene 14 columnas vs 7 esperadas

---

### 6. Caja Adicion.xlsx

**Estadísticas:**
- Columnas: 29
- Filas de datos: 12

**Columnas:**
1. Fecha Contable
2. Origen
3. Clase
4. Proveedor / Para
5. Monto
6. Comentario
7. Fecha Modificación
8. Usuario
9. Tipo
10. Forma de Pago
11. Fecha Pago/Venc.
12. N.I.F.
13. # Doc
14. Cuenta Contable
15. Dif
16. Monto EDIT.
17. LIN
18. COD.
19. PROD
20. Q.REC
21. UM.REC
22. Q.FAC
23. UM.FAC
24. PRECIO
25. DIF.P
26. DIF.TOT
27. APRO
28. MTO.NETO
29. IVA

**Diferencias con requerimiento:**
- Requerimiento decía: `Fecha Contable, Origen, Proveedor / Para, Monto, Forma de Pago, Comentario Toteat POS`
- Real tiene 29 columnas vs 6 esperadas
- No existe columna "Comentario Toteat POS"

---

## Recomendaciones para Implementación

### 1. Validación Flexible

En lugar de validar columnas exactas, propongo dos niveles de validación:

**Nivel 1: Validación Estricta (Columnas Mínimas Requeridas)**
- Verificar que existan las columnas clave para el negocio
- Permitir columnas adicionales

**Nivel 2: Validación de Nombre de Archivo**
- Usar coincidencia parcial (ej: "Ventas MP" coincide con "Ventas_MP" o "Prueba_MP")
- Ignorar guiones bajos y espacios

### 2. Mapeo de Nombres

Crear un sistema de aliases para nombres de archivo:

```php
'ventas_mp' => ['Ventas MP.xlsx', 'Prueba_MP.xlsx', 'Ventas_MP.xlsx'],
'reporte_ventas' => ['Reporte Ventas.xlsx', 'Reporte_Ventas.xlsx'],
```

### 3. Configuración por Workflow Type

Almacenar en `workflow_types.required_files` solo las columnas esenciales, no todas.

---

## JSON para Seeder (Actualizado)

```json
{
  "Turnos": ["Fecha", "Fecha Apertura", "Hs Ap. Caja", "Fecha Cierre", "Hs Cierre Caja", "TURNO", "Encargado", "APERTURA CAJA Efectivo", "Recuento Efectivo"],
  "Reporte_Ventas": ["FechaCierre", "Comanda", "Total", "Propina", "Pagos", "Boleta", "Efectivo", "Getnet", "Mercado Pago", "Cta Cte"],
  "Reporte_getnet": ["Fecha de operacion", "Cod de Transaccion", "Monto Bruto Transaccion", "Arancel", "Estado"],
  "Ventas_MP": ["FECHA DE ORIGEN", "ID DE OPERACIÓN EN MERCADO PAGO", "VALOR DE LA COMPRA", "MEDIO DE PAGO", "NÚMERO DE SERIE DEL LECTOR (S/N)"],
  "Devoluciones": ["ID Comanda", "Producto", "Precios", "Hora pedido", "Hora Anulación", "Descuadre", "DTE Emision"],
  "Caja_Adicion": ["Fecha Contable", "Origen", "Proveedor / Para", "Monto", "Forma de Pago", "Comentario"]
}
```

**Nota:** Este JSON contiene solo las columnas mínimas requeridas según el documento original, no todas las columnas reales.
