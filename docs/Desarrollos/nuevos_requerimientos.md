
## NUEVOS REQUERIMIENTOS

Primero revisa la estructura actual del proyecto, y luego ve los nuevos requerimientos

## 1_ Subida de archivos

Ser requiere que el sistema, una vez que eliga el cliente y su sede, se nos permita hacer los siguientes pasos 
      
   a_ Seleccionar archivo Turnos
   b_ Seleccionar archivo reporte de ventas (sistema)
   c_ Seleccionar archivo reporte ventas getnet
   d_ Seleccionar archivo reporte ventas MP
   e_ Seleccionar archivo reporte devoluciones 
   f_ Seleccionar archivo caja adicion

De Estos archivos, tiene la siguiente estructura, la cual se debe respetar en el sistema, podemos revisar en laravel para certificar que la estructura sea la correcta.

Estructura de Archivos Excel
Archivo Columnas Clave

Turnos.xlsx 

Fecha Apertura, Hs Ap. Caja, Fecha Cierre, Hs Cierre Caja, TURNO, Encargado, APERTURA CAJA Efectivo, Recuento Efectivo

Reporte_Ventas.xlsx FechaCierre, Comanda, Total, Propina, Pagos, Boleta, Efectivo, Getnet,
Mercado Pago, Cta Cte


Reporte_getnet.xlsx 
Fecha de operacion, Cod de Transaccion, Monto Bruto Transaccion, Arancel, Estado

Prueba_MP.xlsx FECHA DE ORIGEN (ISO), ID DE OPERACIÓN EN MERCADO PAGO, VALOR DE LA COMPRA, MEDIO DE PAGO, NÚMERO DE SERIE DEL LECTOR

Devoluciones.xlsx ID Comanda, Producto, Precios, Hora pedido, Hora Anulación, Descuadre, DTE Emision

Caja_Adicion.xlsx Fecha Contable, Origen, Proveedor / Para, Monto, Forma de Pago, Comentario Toteat POS



   Abrir el ejecutable Conciliador
   a_ Seleccionar archivo Turnos
   b_ Seleccionar archivo reporte de ventas (sistema)
   c_ Seleccionar archivo reporte ventas getnet
   d_ Seleccionar archivo reporte ventas MP
   e_ Seleccionar archivo reporte devoluciones 
   f_ Seleccionar archivo caja adicion

Esto significa que el sistema debe tener la capacidad de hacer lo siguiente:

1_ tomar todos los archivos de una subida
2_ verificar que los nombres coincidan con los archivos que se subieron
3_ verificar que las los nombres y las cantidades de archivos coincidan con los archivos que se subieron
4_ Armar un Jason con la informacion de los archivos, que deberia mandarse a un servidor mediante una api
5_ como el servidor no está listo, visualizar esa api en un archivo json en un /test.html
6_ analizar la viabilidad de guardar ese jason en un campo asociado al cliente en si, si no es mucha información
7- la estructura de la carga deberia tener algun tipo de proceso visual que nos permita ver que esta pasando en el sistema, por ejemplo checklis de coincidencia de nombres de archivos, cantidades de archivos, coincidencia de tablas, porcentaje de carga.

## 2_ Ejecucion de workflow

Este procedimiento debe estar disponible solo para el usuario con el rol de Programador


el problema es que esto se llama workflow, y ya hay algo hecho, pero no esta bien que digamos

la idea es que el pogramador pueda cargar los archivos,  y el operador pueda ver el arhivo que genero la subida de archivos que fueron enviardos al servidor donde se ejecutan reglas del negocio, y entrega un resultado, ese resultado lo debe ver el programador para verificar que fue exitoso, y el operador para darle el uso que tiene que hacer

entonces el flujo es asi:

1_ el pogramador sube los archivos
2_ el progrmador ve los archivos subidos
3_ el programador ejecuta el workflow
4_ el programador ve el resultado
5_ el operador da el uso que tiene que hacer

entonces el programador elige empresa, sede, el workflow, que puede tener varias nombres, vamos a armar uno que se llama "Conciliación", este workflow, tiene sus cantidad de archivos, nombres de arhivos y cantidades de campos y sus nombres, carga los archivos,, todo eso debe visualizarse en proceso de carga, tranforma en un json general, prepara el json para ser enviado al servidor, ejecuta las reglas de negocios que estane el servidor, y entrega un resultado, osea todo esto en un workflow se que lleva a cabo en la palabra ejectuar que se ven en el front.

## 3_ Resultado de workflow

el resultado debe enviarse al Historial, y programado debe verlo, y tambiene el operador debe verlo

## 4_ Historial

el historial debe tener un boton para descargar un pdf

## 5

Analizar que va a pasar con las columans de la base de datos workflow, workflow_ejecution, tambien hay que analizar, si por cada tipo de workflow se va a crear una tabla, y si por cada tipo de ejecucion se va a crear una tabla, en donde se guarde el tipo la cantidad de archivos, nombres de archivos y cantidades de campos y sus nombres. por ejemplo conciliación. determinar si para json armado se guarda en un tabla, y para cada respuesta tambien se guarda en una tabla.


es todo un lio
