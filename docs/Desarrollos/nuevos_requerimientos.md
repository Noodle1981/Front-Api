
## NUEVOS REQUERIMIENTOS

# Cambio de estructura de http://127.0.0.1:8000/programadores/workflows/upload

Analisa la estructura actual para entrar en contexto, y vamos a modifica como es el nuevo requerimiento

primero que nada el wizard de carga de archivos "Cargar Archivos de Workflow" tendra que cambiar a 3 pasos

1. seleccionar cliente y sucursal
2. seleccionar tipo de workflow
3. cargar archivos, analizar archivos y ejecutar workflow

la diferencia es que el paso 2 y 3 se hara en el mismo paso, y se necesita que se visualice una barra de proceso. visualmente sería así

Analizando tipo de archivo...

Analizando archivos...

Analizando contenido..

Ejecutando workflow...

Esperando respuesta del servidor...

Generando reporte...


Bien, ahora lo que tenemos que tener en cuenta que los excel, deben respetar las estructuras original de cada uno, digamos si originalmente trae 20 columnas, encontes el excel debe pasar por el filtro de validacion de columnas que tenga 20 columnas, si no tiene 20 columnas, debe darte error, porque el filtrado de columnas lo hará el servidor bajo el calculo de python. y asi susecivamente con cada columna, cada tipo de workflow tiene su propia validacion de columnas, por lo que el excel debe respetar la estructura original de cada uno. pero por el momento estamos con conciliación, como workflow, eso significa que el servidor no espera json maestro como actualmente, espera todo el conjunto de arhivos, por lo tanto el front tiene que cumplir con esta estructura, osea elegir clientes, sedes, workflow y cargar archivos, analizar archivos y ejecutar workflow, enviar esos archivos a la api de python para que haga el calculo y devolverá otro excel que eso lo convertirá en un informe pdf.



ahora para la estructura de la base de datos, controladores y apis cambiara, esto lo encontraras en D:\Front-Api\docs\procedimientos\workflows_nuevaversion.md


Entonces analiza el nuevo requerimiento, revisea el workflow_nuevaversion.md y dame las pautas de como poder testearlo si todavia no esta el servidor activo, lo estan trabajando, y como puedo tomar ese excel para muestre en http://127.0.0.1:8000/programadores/workflows/history y en acciones ver PDF lo muestre como unos diseños, ese excel motrara una X cantidad de pestañas lo cual el informe deberá mostrarlos como una x cantidad de hojas! el diseño lo tengo en un excel que se llama "arqueo.xlsx" igual lo voy a preparar mientras implementamos todo.

Debatamos armamos un plan maestros, 