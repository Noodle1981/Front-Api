en la seccion de reglas de negocio se debe agregar la capacidad de crear reglas de negocio, que se llama reglas de negocio ETL
practicamente en donde se crean las reglas de negocio ETL

segun la grafica seria asi:

1. Titulo : Reglas de Negocio ETL
2. Subtitulo : Gestiona tus reglas de Extracción, Transformación y Visualización.
3. Al lado sale una tarjeta chica que dice Motor Python "ready"
4. Abajo 4 tarjetas chicas en una misma fila, que dice Total Reglas, Extración (cantidad), Transformación (cantidad), Visualización (cantidad)
5. una tarjeta grande que dice Reglas de Negocio con filtros todas, extraccion, transformacion y visualizacion
6. debajo de esa tarjeta tarjetas tipos filas, donde dice Get Users APi (Extraccion) (Activo), debajo de ese titulo sale un subtitulo que dice que hace la regla, cuando fue creada y que tipo de api es  luego al otro costado sale 4 iconos, uno verde tipo play, que no se para que es, al lado editar, otro amarillo que tampoco se puede ser y uno eleminar
6. debajo de esa tarjeta otra regla
7 un boton que dice + nueva regla

workflow builder
una pequeña introduccion que dice que es el workflow builder despues vendria a hacer el workflow builder, digamos es donde el progamador toma los datos que trae un endpoint y los transforma para que el operador pueda usarlos de una api, que muestra el jason, al lado una consola de python y al lado los resultado
entonces visualmente se ve asi
1 titu  Nueva regla - supongo que viene de la etapa anterior de crear regla, al final ejecutar y guardar
2 despues campos nombre, tipo de regla, fuentes de datos estado y descripcion
3 abajo sale un cartel de informacion de alerta, que dice "no hay emrpesa seleccionada, ve al modulo entrepise selecciona una empresa ver sus endpoints disponibles"
4 abajo sale un cuadro que dice datos de consola de entrada json, tabla y arbol, al lado el editor de codigo pyhton y al lado el  otro cuadro de resultado de ejecucion


Enterprise

Desconodo como esta hecho no tengo imagen, pero se ve que tiene que ver con las apis y endpoints, es como buscar un clientes, ver la api que esta configuradad, y asignarles los endpoint.pero me confunde porque en, http://127.0.0.1:8000/programadores/apis hay algo asi


respuestas a tus preguntas

¿Qué hace exactamente el icono amarillo [⚠️] en las acciones de reglas? ¿Validar? ¿Ver logs? ** no lo agregamos aun, analizar si se puede hacer, 


¿El módulo "Enterprise" es diferente a /programadores/apis? Si sí, ¿qué lo diferencia? El modulo enterprice está en el rol admin y programador y sirve para cargar empresas, sus apis y sus endpoints, eso significa que programadores/api es Enterprise, entonces hay que cambiar los nombres y rutas para programadores/Enterprise

¿Python en navegador (Pyodide) o en servidor? Sí, es solo para probar el código, y en ninugn momento más que eso, cuando se guarde la regla el codigo pasa a texto y luego ese texto pasa a codigo pyhton y luego desde fasat api se interactua con el servidor, resumen: solo funciona para probar en el navegador en el momento


¿Los operadores ejecutarán estas reglas desde /operadores/workflow? ¿Cómo eligen qué regla ejecutar? eso lo veremos más adelante, aun no está definido