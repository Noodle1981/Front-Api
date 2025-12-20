INTRODUCCION -

    Se van a cambiar algunos nombres, 
    Analistas por Programadores
    Usuarios por Operadores
    Administrador se mantiene

    quiere decir que los nombres de las vistas y rutas cambiaran

ETAPA 1 ANALISTA se cambiara por PROGRAMADORES

    Se cambiaran los nombres de las vistas y rutas

    Panel Inspector se cambiara por Panel de Programadores [DONE]
    http://127.0.0.1:8000/analistas/dashboard por http://127.0.0.1:8000/programadores/dashboard

    http://127.0.0.1:8000/analistas/api-dashboard por http://127.0.0.1:8000/programadores/api-dashboard

    Clientes se mantiente [DONE]

    http://127.0.0.1:8000/analistas/clients por http://127.0.0.1:8000/programadores/clients

    los pogramadores deben tener la capacidad de asociar clientes a apis (1 o varias, teniendo encuenta si son sedes o sucursales) [DONE]

    Se agregara 

    Enterprise http://127.0.0.1:8000/programadores/enterprise [DONE - Renombrado de APIs a Enterprise + Wizard de Integración]

    los pogramadores deben tener la capacidad de crear apis 

    Endpoints http://127.0.0.1:8000/programadores/services/{id}/endpoints [DONE - Gestor de Endpoints]

    los pogramadores deben tener la capacidad de crear endpoints segun sus apis tanto get como post

    Phyton Builder http://127.0.0.1:8000/programadores/python

    Reglas de Negocio http://127.0.0.1:8000/programadores/reglas [DONE - Sistema ETL con Pyodide + Monaco Editor + Glassmorphism]

    los pogramadores deben tener la capacidad de crear reglas de negocio usando phyton con los datos de los clientes con sus apis y endpoints
    osea que se crea las reglas de negocio con los datos de los clientes con sus apis y endpoints, para que los operarios tengan reglas predifinidas para ejecutar
    sin saber como se programan

    Workflow Builder http://127.0.0.1:8000/programadores/workflow [DONE - Integrado en Reglas de Negocio]

    es donde el programador crean  y testean los workflows para los operarios eligiendo las reglas de negocio y las apis y endpoints
    
    
    Monitoreo de APIs se dejara 

    http://127.0.0.1:8000/analistas/api-dashboard por http://127.0.0.1:8000/programadores/api-dashboard

    DATOS A DEFINIR


    Ejecución de código Python en el navegador (Pyodide) [DONE - Integrado en Workflow Builder]
    
ETAPA 2  USUARIOS se cambiara por OPERADORES

    
    Dashboard http://127.0.0.1:8000/usuarios/dashboard por http://127.0.0.1:8000/operadores/dashboard

    DATOS A DEFINIR
    
    Clientes http://127.0.0.1:8000/usuarios/clients por http://127.0.0.1:8000/operadores/clients

    la finalidad es la misma, solo que los operadores no crean apis, endpoints, reglas de negocio ni workflows, osea que solo usan el CRUD de clientes

    workflow http://127.0.0.1:8000/programadores/workflow por http://127.0.0.1:8000/operadores/workflow    

    es donde usan las reglas de negocio y las apis y endpoints para ejecutar los workflows que fueron testeados por los programadores

ETAPA 3 - LIVEWIRE [DONE - Instalado v3.7]

    Livewire es un framework de Laravel para crear interfaces dinámicas sin escribir mucho JavaScript.
    
    INSTALACIÓN (Simple - 3 comandos): [DONE]
    
    1. composer require livewire/livewire ✓
    2. Agregar @livewireStyles y @livewireScripts en el layout ✓
    3. php artisan make:livewire NombreDelComponente ✓
    
    BENEFICIOS:
    - Componentes reactivos sin JavaScript manual
    - Mejor UX para filtros, búsquedas y paginación
    - Formularios en tiempo real
    - Compatible con Alpine.js (ya instalado)
    
    COMPONENTES CREADOS:
    - [x] BuscadorClientes - Búsqueda en tiempo real con filtros y paginación
    
    CASOS DE USO PENDIENTES:
    - [ ] Dashboard con actualización automática
    - [ ] Formularios de reglas de negocio
    - [ ] Notificaciones en tiempo real

