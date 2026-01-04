
## NUEVOS REQUERIMIENTOS

# Cambio de estructura de http://127.0.0.1:8000/programadores/workflows/upload

Descripción definitiva de lo que necesito 


ya hemos logrado que el "Cargar Archivos de Workflow" tenga 3 pasos

1. seleccionar cliente y sucursal
2. seleccionar tipo de workflow
3. cargar archivos, analizar archivos y ejecutar workflow

Cuando hago ejecutar workflow, se debe mostrar una barra de proceso con los siguientes estados, mediante un modal:


Analizando tipo de archivo... (haga el conteo de los formatos permitidos)

Analizando  cantidad archivos... (haga el conteo de la cantidad de archivos permitidos)

Analizando contenido.. (analizar el contenido de los archivos, las cantidades de tablas)

Ejecutando workflow... (una vez que se ejecuta el workflow, se debe mostrar una barra de proceso con los siguientes estados)

Esperando respuesta del servidor...

Generando reporte...



bien despues en http://127.0.0.1:8000/programadores/workflows/batch/8

los campos estan vacios 

Información del Batch
Cliente

Distribuidora San Martín S.A.

Sede

Sucursal Palermo

Workflow

Conciliación

Usuario

Programador Principal

Fecha de Carga

04/01/2026 17:16

Archivos

0

Archivos Cargados
#	Tipo	Nombre Original	Tamaño	Estado


revisar si armar un json para tener esta data, de ultima se podria crear una tabla para ello.

esta es la vista de enviar a sucursal



esta es la vista de enviar egresos

(image-1.png)

esta es la vista enviar no conciliados en dos partes porque no lo alcanza

(image-2.png)

(image-3.png)

esta es la vista enviar anulaciones

(image-4.png)

recuerda que el logo lo tenemos

