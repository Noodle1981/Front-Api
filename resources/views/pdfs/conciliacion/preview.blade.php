@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header con título estilo wizard --}}
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Vista Previa de Arqueo - {{ $execution->fileBatch->client->company ?? 'Cliente' }} - {{ $execution->fileBatch->branch->branch_name ?? 'Sede' }}
        </h1>
    </div>
    
    {{-- Botón de descarga flotante en la esquina --}}
    <div class="fixed top-20 right-8 z-50">
        <a href="{{ route('programmer.workflows.execution.pdf.download', $execution) }}" 
           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-lg text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
            <i class="fas fa-download mr-2"></i>
            Descargar PDF
        </a>
    </div>
    
    {{-- Contenido del PDF (iframe o inclusión directa) --}}
    <div class="row">
        <div class="col-12">
            {{-- Incluir directamente el contenido del PDF --}}
            @include('pdfs.conciliacion.main')
        </div>
    </div>
</div>

<style>
    /* Ajustes para la vista de preview */
    .container-fluid {
        max-width: 1400px;
    }
    
    /* Header limpio */
    .bg-white {
        background-color: white;
    }
    
    .border-bottom {
        border-bottom: 1px solid #dee2e6;
    }
    
    /* Botón con icono */
    .btn i {
        font-size: 14px;
    }
</style>
@endsection
