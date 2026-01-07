# nuevos requerimientos

## 1. cambio de flujo

en http://127.0.0.1:8000/programadores/workflows/upload 

vamos a cambiar

son 3 etapas y una etapa de finalizacion que seria la vista del resultado

la etapa 1 Cliente/Sede se mantiene
la etapa 2 Workflow se mantiene
la etapa 3 Archivos & Ejecución se hace modificaciones
la etapa 4 es nueva

Etapa 3 Archivos & Ejecución

Cargue los Archivos Requeridos, aqui se mantiene junto con los tarjetas que se muestran abajo, al momento de cargar el achivo, lo que hace es validar, y luego se puede ejecutar el workflow, cuando se ejecuta el workflow sale el modal con los procesos.

lo que vamos a cambiar es que es que al momento de elegir los archivos, en vez de decir validando arhivos y que me muestre el siguiente resultado

6 archivo(s) seleccionado(s)
Arhivos correctos
Campos obligatorios correctos
Archivos validados.

Cuando esa verificación pase, recien se podrá ejecutar el workflow, y al momento de ejecutar el workflow sale el modal con los procesos. El modal debe decir conectando con Servidor de reglas de negocio, y una vez que se conecte, el servidor arroja los resultados y eso se mostrará en una vista nueva que es

http://127.0.0.1:8000/programadores/workflows/execution/21/pdf/preview

# 2 cambio de link para acceder a esta funcion

Se llama Cargar Workflows, debemos renombrar por Ejecutar Workflow, se deba cambiar ese nombre en el sidebar.
