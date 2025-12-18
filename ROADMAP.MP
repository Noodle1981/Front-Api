 Olvidamos el concepto de "CRM de Ventas" (adiós leads, scoring, pipelines) y nos enfocamos en un Gestor de Integraciones (Middleware Hub) para un estudio contable.
Tu objetivo es construir la interfaz para que el estudio contable gestione las credenciales de sus clientes. El backend (equipo de datos) usará esas credenciales guardadas para ir a buscar la data (facturas, pagos, etc.).
Aquí está la propuesta de refactorización adaptada a este nuevo enfoque:
Etapa 1. Limpieza del Proyecto ("De-bloating")
Dado que partimos de una base CRM, primero eliminamos lo que sobra para no confundir:
Eliminar: Lógica de Lead Scoring, Estados de "Lead" (prospecto, convertido, etc.), Tableros Kanban.
Mantener: User (Auth), Roles, Client (ahora visto como la empresa/persona a la que el estudio le lleva los números).
Etapa 2. Arquitectura de Datos: El problema de "APIs diferentes"
Para resolver tu duda de "¿cómo hago con el tema de las APIs si todas piden datos distintos?", la mejor solución en Laravel es usar el patrón de Catálogo vs. Instancia apoyándonos en columnas JSON y Encriptación.
No crees una tabla mercado_pago_configs y otra afip_configs. Hazlo genérico.
Nuevos Modelos Propuestos:
A. Modelo ApiService (El Catálogo / CRUD de APIs)
Aquí el Administrador define qué APIs soporta el sistema.
id
name: "Mercado Pago", "AFIP", "Tienda Nube".
slug: "mercado-pago".
base_url: La URL base de la API (opcional).
required_fields: (CLAVE) Una columna JSON donde defines qué campos necesita esta API.
Ejemplo para MP: ["access_token", "public_key"]
Ejemplo para Otra: ["api_key", "secret_id", "region"]
B. Modelo ClientCredential (La Conexión)
Aquí guardas la relación entre el Cliente y la API, junto con sus claves.
id
client_id: Relación con el Cliente.
api_service_id: Relación con el ApiService (ej. Mercado Pago).
credentials: (CLAVE) Columna JSON ENCRIPTADA que guarda los valores reales.
is_active: Boolean.
Etapa 3. Implementación Técnica (Cómo funciona la magia)
Paso 1: Definir los campos variables
Cuando el Admin crea la API "Mercado Pago" en el CRUD de APIs, en un campo de texto (o una UI dinámica sencilla) define que necesita public_key y access_token.
Paso 2: El formulario dinámico (Front-end)
Cuando el Usuario va a un Cliente y le da a "Conectar Mercado Pago":
Tu Front lee api_service->required_fields.
Con un bucle foreach en Blade/Alpine, dibujas un <input> por cada campo requerido.
El usuario pega las claves.
Paso 3: Guardado Seguro (Back-end)
Esto es vital. Como es un estudio contable, no puedes guardar tokens en texto plano. Si hackean la DB, roban el acceso a las cuentas de los clientes.
En tu modelo ClientCredential:
code
PHP
// app/Models/ClientCredential.php
use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;

class ClientCredential extends Model
{
    protected $casts = [
        // Laravel encripta/desencripta automáticamente al guardar/leer
        'credentials' => AsEncryptedArrayObject::class, 
    ];
}
Etapa 4. Roles y Flujo de Trabajo
Rol Admin (IT / Tech Lead):
Tiene acceso al CRUD de APIs (ApiService).
Define: "Hoy agregamos soporte para Mercado Pago". Configura los campos que se pedirán.
Rol Contador/Operador:
Tiene acceso al CRUD de Clientes.
Entra al perfil de "Cliente Juan Pérez".
Ve una pestaña "Integraciones".
Selecciona "Mercado Pago" -> El sistema le pide los tokens -> Guarda.
El Equipo de Backend (Consumidores):
Crearás un endpoint interno o un Comando que ellos usarán.
Ejemplo: ClientCredential::where('client_id', 1)->get(). Laravel desencripta las claves automáticamente y ellos ya pueden hacer los GET a Mercado Pago.
5. ¿Cómo empezar la refactorización?
Limpieza: Borra todo lo relacionado a "pipeline" o "funnel".
Migraciones:
Crea la tabla api_services (name, required_fields [json]).
Crea la tabla client_credentials (client_id, api_service_id, credentials [text/longtext]).
CRUD APIs: Haz un ABM simple donde puedas crear "Mercado Pago" y definir sus campos requeridos en un JSON simple.
Vista Cliente: En la vista de detalle del Cliente, agrega la sección para agregar credenciales dinámicamente.

Etapa 5. 


Esta migración que muestras tiene una mezcla de conceptos del proyecto anterior ("CRM de H&S") con lo que necesitas ahora. Vamos a limpiarla y adaptarla al contexto de Estudio Contable / Gestor de APIs.
1. Sobre tu pregunta: ¿SoftDeletes?
SÍ, ABSOLUTAMENTE.
En sistemas contables o de gestión de datos críticos, nunca se borra información.
Auditoría: Si el backend falla procesando datos de un cliente que "ya no existe", necesitas saber quién era.
Regreso del Cliente: Es muy común que una empresa se vaya y vuelva al año siguiente. Con SoftDeletes, solo "restauras" al cliente y mantienes su historial y sus configuraciones de API viejas (que quizás solo necesiten renovar token).
Integridad Referencial: Si borras un cliente "físicamente" (hard delete), podrías romper registros históricos de logs o credenciales.
2. Análisis de los Campos para un Estudio Contable
Para un Hub de Integraciones Contables, necesitas identificar inequívocamente a la empresa fiscal.
Lo que está bien: cuit (Es tu ID real), company (Razón Social), fiscal_address.
Lo que sobra o confunde: Los campos de ART y H&S (art_provider, hs_manager...) suenan a "Higiene y Seguridad". Si el estudio contable NO hace liquidación de sueldos ni RRHH, estos campos sobran. Si solo les vas a gestionar las facturas (Mercado Pago, AFIP), bórralos.
Lo que falta:
tax_condition: (Responsable Inscripto, Monotributo, Exento). Es vital para saber qué APIs o endpoints usar.
uuid: Es buena práctica exponer un UUID en lugar del ID numérico si vas a conectar sistemas externos.
3. Migración Refactorizada
Aquí tienes la versión limpia, enfocada en Contabilidad/APIs y con SoftDeletes.
code
PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Identificador público para sistemas externos');

            // --- Identificación Fiscal (Lo más importante) ---
            $table->string('cuit', 11)->unique()->comment('Clave Única de Identificación Tributaria (sin guiones)');
            $table->string('company')->comment('Razón Social (Nombre legal en facturas)');
            $table->string('fantasy_name')->nullable()->comment('Nombre de fantasía / Marca comercial');
            
            // --- Clasificación Contable ---
            // Ejemplo: 'responsable_inscripto', 'monotributo', 'exento'
            $table->string('tax_condition')->nullable()->comment('Condición frente al IVA'); 

            // --- Contacto Principal (Para avisar si se cae una API) ---
            $table->string('email')->nullable()->comment('Email para notificaciones de sistema/alertas');
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            
            // --- Domicilio Fiscal ---
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable(); // Provincia
            $table->string('zip_code')->nullable();

            // --- Gestión Interna y Auditoría ---
            $table->text('internal_notes')->nullable()->comment('Notas internas del estudio contable');
            
            // ID externo si vienen de otro sistema (H&S o sistema legacy del estudio)
            $table->unsignedBigInteger('external_reference_id')->nullable()->index()->comment('ID de referencia si se importó de otro sistema');

            // Asignación de Responsable
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Contador o gestor asignado a este cliente');

            $table->boolean('is_active')->default(true)->comment('Switch rápido para desactivar acceso a APIs sin borrar');
            
            $table->timestamps();
            $table->softDeletes(); // <--- Aquí está la magia para no borrar datos
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
Cambios Clave que hice:
Limpieza de Nombres: Cambié name por fantasy_name y dejé company como la Razón Social. Esto evita confusiones contables.
cuit como string de 11: En Argentina el CUIT son 11 números. Es mejor guardarlo sin guiones para buscar fácil.
SoftDeletes: Agregado al final. Recuerda agregar el trait use SoftDeletes; en tu modelo Client.
is_active vs SoftDelete:
SoftDelete = El cliente rescindió contrato (Baja).
is_active = El cliente no pagó este mes, le "apago" el uso de las APIs temporalmente, pero sigue siendo cliente.
Relación User: Le puse nullable y onDelete('set null'). Si despides al contador (User), no querrás que se borren sus clientes ni que explote la base de datos. Simplemente, el campo queda vacío hasta que asignes otro.

etapa 6

Rubro (industry)
Empleados (employees_count)
Sede / Anexo (Lógica de parent_id)
⚠️ ATENCIÓN AL CUIT:
Tuve que quitarle el unique() al CUIT.
¿Por qué? Porque si cargás la Casa Central (CUIT X) y después querés cargar el Anexo (mismo CUIT X), si dejás el unique, la base de datos te va a dar error. Ahora solo tiene index() para que busque rápido, pero permite repetidos (para las sucursales).
Acá tenés la migración lista para copiar y pegar:
code
PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Identificador público para sistemas externos');

            // --- NUEVO: Lógica Sede vs Anexo ---
            // Si está vacío (NULL) es la CASA CENTRAL. Si tiene un ID, es un ANEXO de esa central.
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('clients')
                  ->onDelete('cascade')
                  ->comment('Jerarquía: NULL = Sede, ID = Anexo/Sucursal');
                  
            $table->string('branch_name')->nullable()->comment('Nombre de la sucursal si es anexo (Ej: Local Centro)');

            // --- Identificación Fiscal ---
            // CAMBIO IMPORTANTE: Quitamos 'unique()' para permitir cargar sucursales con mismo CUIT
            $table->string('cuit', 11)->index()->comment('CUIT (sin guiones). No es único para permitir sucursales.');
            
            $table->string('company')->comment('Razón Social (Nombre legal en facturas)');
            $table->string('fantasy_name')->nullable()->comment('Nombre de fantasía / Marca comercial');

            // --- Clasificación Contable y Datos de Negocio ---
            $table->string('tax_condition')->nullable()->comment('Condición frente al IVA');
            
            // --- NUEVO: Lo que pediste recién ---
            $table->string('industry')->nullable()->comment('Rubro (Ej: Gastronomía, Salud)');
            $table->integer('employees_count')->nullable()->unsigned()->comment('Cantidad de empleados');

            // --- Contacto Principal ---
            $table->string('email')->nullable()->comment('Email para notificaciones');
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            // --- Domicilio Fiscal ---
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable(); 
            $table->string('zip_code')->nullable();

            // --- Gestión Interna ---
            $table->text('internal_notes')->nullable();
            $table->unsignedBigInteger('external_reference_id')->nullable()->index();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Contador asignado');

            $table->boolean('active')->default(true); // Mantengo 'active' como en tu código original

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
Resumen de los agregados:
parent_id: Es el campo mágico. Si creás un cliente y dejás esto vacío, es una empresa normal (Sede). Si le ponés el ID de otro cliente, se convierte en "Anexo" de ese cliente.
branch_name: Para ponerle nombre al anexo (ej: "Depósito", "Local 2").
industry: Aquí guardás el Rubro.
employees_count: Aquí guardás la Cantidad de Empleados.
cuit: Pasó de ser unique() a index(). Esto es vital para que no te explote el sistema cuando cargues dos locales de la misma empresa.

etapa 7

Esta es una decisión de arquitectura crítica. Para que el sistema sea seguro y no se rompa, debemos separar claramente **quién configura las reglas del juego** (Definiciones de API) de **quién juega** (Carga de datos).

Te propongo una estructura de **3 Roles** para mantener el orden.

### La Filosofía: "Definición" vs "Uso"
El punto clave para responder tu duda sobre las APIs es entender la diferencia entre:
1.  **Definir la API (Catálogo):** Decir "El sistema soporta Mercado Pago y necesita `client_id` y `secret`". -> **Esto rompe el sistema si se hace mal.**
2.  **Usar la API (Credenciales):** Decir "El Cliente Juan Pérez tiene el secret `abc-123`". -> **Esto es trabajo diario.**

---

### Distribución de Roles Propuesta

#### 1. Rol: `SUPER ADMIN` (IT / Tech Lead)
Es el "Dios" del sistema. Probablemente seas tú o el jefe de tecnología.
*   **Gestión de Usuarios:** Crea, edita y elimina a los empleados (Contadores).
*   **Gestión de APIs (Catálogo - `ApiService`):**
    *   Es el **ÚNICO** que puede crear/editar/borrar en la tabla `api_services`.
    *   Define qué campos JSON se piden (ej: define que para "AFIP" se pide certificado y clave).
    *   *Por qué:* Si un usuario normal borra "Mercado Pago" del catálogo, se rompen las integraciones de 500 clientes.
*   **Acceso Global:** Puede ver y editar cualquier cliente si hay una emergencia.

#### 2. Rol: `MANAGER` (Gerente del Estudio / Supervisor)
Es el jefe de los contadores. No toca código ni configuraciones técnicas.
*   **Gestión de Clientes:**
    *   Puede Reasignar clientes (mover un cliente del Contador A al Contador B).
    *   Puede ver Reportes (cuántos clientes hay, quién cargó qué).
    *   Puede Restaurar clientes eliminados (SoftDeletes).
*   **Usuarios:** Puede ver la lista de empleados, pero no crear nuevos (opcional).

#### 3. Rol: `ANALISTA` (Contador / Usuario Estándar)
Es el usuario que trabaja día a día cargando datos.
*   **Gestión de Clientes:**
    *   **Create/Read/Update:** Crea clientes, carga sus datos (CUIT, Empleados, etc.).
    *   **Delete:** No debería poder borrar permanentemente. Solo "Desactivar" o SoftDelete.
*   **Gestión de APIs (Credenciales - `ClientCredential`):**
    *   **Read (Catálogo):** Solo puede *ver* la lista de APIs disponibles ("Ah, puedo conectar MP o AFIP"). **No puede crear nuevas APIs.**
    *   **Create/Update (Credenciales):** Es quien hace la conexión. Entra al perfil de "Ferretería Juan", selecciona "Mercado Pago" y pega los tokens que le pasó el cliente.

---

### Resumen de Permisos (Matriz CRUD)

| Recurso / Acción | Super Admin | Manager | Analista (User) |
| :--- | :---: | :---: | :---: |
| **Usuarios** (Crear login) | ✅ FULL | 👁️ Ver | ❌ |
| **Catálogo APIs** (Definir estructura) | ✅ FULL | 👁️ Ver | 👁️ Ver (Solo lista) |
| **Clientes** (Datos, CUIT) | ✅ FULL | ✅ FULL | ✅ (Sin Borrado Final) |
| **Credenciales** (Cargar Tokens del cliente) | ✅ FULL | 👁️ Ver | ✅ Cargar y Editar |

### ¿Cómo implementar esto en código?

Dado que estás usando Laravel 12, te recomiendo encarecidamente usar el paquete **`spatie/laravel-permission`**. Es el estándar de la industria.

**Ejemplo de cómo se vería la protección en tus Rutas o Controladores:**

```php
// Rutas protegidas para configuración del sistema
Route::middleware(['role:super-admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('api-services', ApiServiceController::class); // <-- NADIE MÁS ENTRA ACÁ
});

// Rutas operativas (el día a día)
Route::middleware(['role:analista|manager|super-admin'])->group(function () {
    Route::resource('clients', ClientController::class);
    
    // El analista puede guardar credenciales, pero NO crear servicios nuevos
    Route::post('clients/{client}/credentials', [ClientCredentialController::class, 'store']);
});
```

### Respuesta a tu miedo: "¿Para que no vayan a romper nada?"

Con esta estructura:
1.  El **Analista** nunca entra al menú "Configuración de APIs". Solo ve un dropdown en la ficha del cliente que dice "Conectar Servicio".
2.  Si el Analista se equivoca cargando un token, solo rompe la conexión de **ESE** cliente, no de todo el sistema.
3.  Solo el **Admin** puede tocar la estructura JSON que define cómo funciona la API.

Etapa 8  


Contexto del Proyecto:
Estoy desarrollando un "Hub de Integraciones Contables" (Accounting Middleware) usando Laravel 12, Blade, Tailwind CSS y Alpine.js. Ya tengo implementado el sistema de Usuarios y Clientes (clients).
Objetivo:
Necesito implementar el "Módulo de Gestión de APIs y Datos". Esto tiene dos partes fundamentales:
Centro de Control de APIs: Un dashboard para monitorear el estado de las conexiones (logs, errores, health check).
Almacenamiento de Datos (Transactions): Una estructura para guardar la información financiera que el backend descarga de las APIs (Mercado Pago, AFIP, Bancos) y mostrarla al usuario, evitando usar Excel como almacenamiento (el Excel será solo para exportar).
Por favor, genera las Migraciones, Modelos y Vistas (Blade) siguiendo estas especificaciones:
1. Base de Datos (Migraciones)
Necesito 2 tablas nuevas. Usa foreignId y restricciones adecuadas.
Tabla A: api_logs (Bitácora de Eventos)
Esta tabla registrará qué pasó cuando el backend intentó conectarse.
client_id: Relación con el cliente.
api_service_id: Relación con el servicio (ej: Mercado Pago).
status: Enum ('success', 'warning', 'error', 'pending').
event_type: String (Ej: "Sincronización Facturas", "Token Refresh").
details: Text/JSON (Para guardar el mensaje de error técnico si falla).
happened_at: Timestamp.
Tabla B: transactions (Datos Financieros Unificados)
Aquí se guardarán los datos reales descargados. No quiero guardar archivos, quiero guardar filas de datos.
client_id: Relación con el cliente.
api_service_id: De dónde vino el dato.
date_at: DateTime (Fecha real del movimiento).
amount: Decimal (15, 2).
currency: String (default 'ARS').
type: Enum ('income', 'expense').
description: String (Ej: "Cobro MP #1234").
status: Enum ('pending', 'verified', 'rejected') (Para control del contador).
raw_data: JSON (Campo crítico para guardar la data cruda específica de cada API que no encaje en las columnas estándar).
2. Modelos y Relaciones
Genera los modelos ApiLog y Transaction con sus relaciones:
Un Client tiene muchos ApiLogs y muchas Transactions.
Asegúrate de incluir casts para los campos JSON y fechas.
3. Vistas (Blade + Tailwind)
Vista A: "Centro de APIs" (Dashboard Técnico)
Crea una vista que funcione como "Torre de Control". Debe tener 3 secciones visuales:
Tarjetas de Estado (Health Check): Arriba. Muestra contadores (Total Errores hoy, Sincronizaciones Exitosas, Clientes Conectados).
Bitácora en Vivo (Live Feed): Al centro/izquierda. Una tabla mostrando los últimos ApiLogs. Si el status es 'error', la fila o badge debe ser roja. Si es 'success', verde.
Catálogo de Servicios: A la derecha. Una lista de las APIs disponibles indicando si son de tipo "Automático" (🤖) o "Manual" (📂).
Vista B: "Listado de Movimientos" (En el perfil del Cliente)
Dentro del detalle de un cliente, crea una sección/tab para ver las transactions.
Tabla Datatable con: Fecha, Origen (API), Descripción, Monto (colorear verde si es income, rojo si es expense), Estado.
Botón de Acción: Incluye un botón visual "Exportar Excel" arriba de la tabla (solo el botón, la lógica la haré luego).
Botón Ver Detalle: Que permita ver el contenido del campo raw_data (JSON) en un modal o detalle expandible.
Nota de Estilo:
Usa componentes de Tailwind CSS limpios. El diseño debe ser profesional (estilo SaaS corporativo). Usa Alpine.js para interacciones simples (como abrir el modal de detalles del JSON).

## Etapa 8: Estado -> COMPLETADO ✅
- [x] Migraciones (`api_logs`, `transactions`).
- [x] Modelos y Relaciones (`ApiLog`, `Transaction`).
- [x] Dashboard Técnico (`/admin/api-dashboard`) con gráficas y filtros.
- [x] Vista de Movimientos en Cliente.
- [x] Seeder de Datos de Prueba (`ApiLogTransactionSeeder`).