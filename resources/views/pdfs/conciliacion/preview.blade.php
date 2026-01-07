@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header con botones de acción --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">Vista Previa: Arqueo de Caja</h2>
                    <p class="text-muted mb-0">
                        {{ $metadata['sucursal'] }} - {{ $metadata['fecha'] }} - {{ $metadata['turno'] }}
                    </p>
                </div>
                
                <div class="d-flex gap-2">
                    {{-- Botón: Descargar PDF --}}
                    <a href="{{ route('programadores.workflows.execution.pdf.download', $execution) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>
                        Descargar PDF
                    </a>
                    
                    {{-- Botón: Ejecutar Nuevo Workflow --}}
                    <a href="{{ route('programadores.workflows.index') }}" 
                       class="btn btn-success">
                        <i class="fas fa-play me-2"></i>
                        Ejecutar Nuevo Workflow
                    </a>
                    
                    {{-- Botón: Volver al Historial --}}
                    <a href="{{ route('programadores.workflows.history') }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>
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
    
    .gap-2 {
        gap: 0.5rem;
    }
    
    /* Botones con iconos */
    .btn i {
        font-size: 14px;
    }
</style>
@endsection
