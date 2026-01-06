# Cargar Workflows

> **Estado:** ✅ OK  
> **Última actualización:** 2026-01-06

## Descripción

Interfaz para que los programadores carguen y procesen archivos mediante workflows. Utiliza un asistente (Wizard) para guiar al usuario en la subida de los archivos requeridos según el tipo de proceso.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/programadores/workflows/upload` |
| **Ruta nombrada** | `programmer.workflows.upload` |
| **Componente Livewire** | `App\Livewire\WorkflowFileUploadWizard` |
| **Controlador** | `App\Http\Controllers\WorkflowController` |
| **Layout** | `layouts/programmer` |
| **Middleware** | `auth`, `role:Programador` |

---

## Ejemplo de Implementación (Arqueo)
curl -X POST "http://localhost:8000/procesar" \
  -F "turnos=@Turnos.xlsx" \
  -F "ventas=@Reporte_Ventas.xlsx" \
  -F "getnet=@Reporte_getnet.xlsx" \
  -F "mercado_pago=@Prueba_MP.xlsx" \
  -F "devoluciones=@Devoluciones.xlsx" \
  -F "caja_adicion=@Caja_Adicion.xlsx" \
  --output arqueo_resultado.xlsx
1.2 Archivos Requeridos
Campo	Archivo Ejemplo	Descripción
turnos	Turnos.xlsx	Registro de turnos
ventas	Reporte_Ventas.xlsx	Reporte de ventas
getnet	Reporte_getnet.xlsx	Transacciones Getnet
mercado_pago	Prueba_MP.xlsx	Transacciones MercadoPago
devoluciones	Devoluciones.xlsx	Registro de devoluciones
caja_adicion	Caja_Adicion.xlsx	Caja adicional


2. Implementación en Laravel
2.1 Estructura de Archivos
app/
├── Http/
│   ├── Controllers/
│   │   └── ArqueoController.php
│   └── Requests/
│       └── ArqueoRequest.php
resources/
└── views/
    └── arqueo/
        └── form.blade.php
routes/
└── web.php
2.2 Definición de Rutas
routes/web.php
<?php

use App\Http\Controllers\ArqueoController;

Route::get('/arqueo', [ArqueoController::class, 'index'])
    ->name('arqueo.index');

Route::post('/arqueo/procesar', [ArqueoController::class, 'procesar'])
    ->name('arqueo.procesar');

Route::get('/arqueo/historial', [ArqueoController::class, 'historial'])
    ->name('arqueo.historial');

Route::get('/arqueo/descargar/{filename}', [ArqueoController::class, 'descargar'])
    ->name('arqueo.descargar');
2.3 Form Request (Validación)
app/Http/Requests/ArqueoRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArqueoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $excelRule = "required|file|mimes:xlsx,xls";

        return [
            'turnos'       => $excelRule,
            'ventas'       => $excelRule,
            'getnet'       => $excelRule,
            'mercado_pago' => $excelRule,
            'devoluciones' => $excelRule,
            'caja_adicion' => $excelRule,
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'El archivo :attribute es obligatorio.',
            '*.mimes'    => 'El archivo :attribute debe ser Excel (.xlsx).',
        ];
    }
}


2.4 Controlador
app/Http/Controllers/ArqueoController.php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArqueoRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArqueoController extends Controller
{
    public function index()
    {
        return view('arqueo.form');
    }

    public function procesar(ArqueoRequest $request)
    {
        $campos = [
            'turnos', 'ventas', 'getnet',
            'mercado_pago', 'devoluciones', 'caja_adicion'
        ];

        $multipart = [];
        foreach ($campos as $campo) {
            $file = $request->file($campo);
            $multipart[] = [
                'name'     => $campo,
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        // Enviar al servicio externo
        $response = Http::asMultipart()
            ->timeout(120)
            ->post(config('services.arqueo.url'), $multipart);

        if ($response->failed()) {
            return back()->withErrors([
                'error' => 'Error al procesar el arqueo.'
            ]);
        }

        // Almacenar archivo en storage
        $fecha = now()->format('Y-m-d_H-i-s');
        $filename = "arqueo_{$fecha}.xlsx";
        $path = "arqueos/{$filename}";

        Storage::disk('local')->put($path, $response->body());

        // Retornar descarga del archivo almacenado
        return Storage::disk('local')->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // Listar archivos de arqueo almacenados
    public function historial()
    {
        $archivos = Storage::disk('local')->files('arqueos');

        $historial = collect($archivos)->map(function ($path) {
            return [
                'nombre'    => basename($path),
                'path'      => $path,
                'tamaño'    => Storage::disk('local')->size($path),
                'fecha'     => Storage::disk('local')->lastModified($path),
            ];
        })->sortByDesc('fecha');

        return view('arqueo.historial', compact('historial'));
    }

    // Descargar archivo específico del historial
    public function descargar(string $filename)
    {
        $path = "arqueos/{$filename}";

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path);
    }
}


2.5 Vista Blade
resources/views/arqueo/form.blade.php
@extends("layouts.app")

@section("content")
<div class="container mx-auto max-w-2xl py-8">
    <h1 class="text-3xl font-bold mb-6">Procesador de Arqueo</h1>

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 p-4 mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('arqueo.procesar') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6 bg-white shadow-md rounded-lg p-6">

        @csrf

        @php
            $campos = [
                'turnos'       => 'Turnos',
                'ventas'       => 'Reporte de Ventas',
                'getnet'       => 'Reporte Getnet',
                'mercado_pago' => 'Mercado Pago',
                'devoluciones' => 'Devoluciones',
                'caja_adicion' => 'Caja Adicional',
            ];
        @endphp

        @foreach ($campos as $name => $label)
            <div>
                <label for="{{ $name }}"
                       class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $label }}
                    <span class="text-red-500">*</span>
                </label>
                <input type="file"
                       id="{{ $name }}"
                       name="{{ $name }}"
                       accept=".xlsx,.xls"
                       required
                       class="block w-full text-sm border border-gray-300
                              rounded-lg cursor-pointer bg-gray-50
                              file:mr-4 file:py-2 file:px-4
                              file:border-0 file:bg-blue-600 file:text-white
                              hover:file:bg-blue-700">
                @error($name)
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white
                       font-bold py-3 px-4 rounded-lg transition">
            Procesar Arqueo
        </button>
    </form>
</div>
@endsection
2.6 Configuración del Servicio
config/services.php
// Agregar al array de servicios:
'arqueo' => [
    'url' => env('ARQUEO_SERVICE_URL', 'http://localhost:8000/procesar'),
],
.env
ARQUEO_SERVICE_URL=http://localhost:8000/procesar


3. Resumen de Componentes
Componente	Responsabilidad
routes/web.php	Define rutas para formulario, procesamiento, historial y descarga
ArqueoRequest	Valida que los 6 archivos Excel sean obligatorios y del tipo correcto
ArqueoController	Procesa, almacena en storage y gestiona historial de arqueos
Storage (local)	Almacena archivos en storage/app/arqueos/
form.blade.php	Vista con el formulario y manejo de errores de validación
config/services.php	URL configurable del servicio de arqueo
3.1 Estructura de Almacenamiento
storage/
└── app/
    └── arqueos/
        ├── arqueo_2024-01-15_10-30-00.xlsx
        ├── arqueo_2024-01-15_14-45-22.xlsx
        └── ...
Nota: Los archivos se almacenan con timestamp para evitar colisiones y facilitar el historial. El controlador incluye métodos para listar y descargar archivos previos.