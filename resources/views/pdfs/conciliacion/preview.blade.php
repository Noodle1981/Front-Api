@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header con título y botón de descarga --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center py-3 px-4 bg-white border-bottom">
                <div>
                    <h4 class="mb-0 fw-bold">Vista Previa de ARQUEO - {{ $execution->fileBatch->client->name ?? 'Cliente' }} - {{ $execution->fileBatch->branch->name ?? 'Sede' }}</h4>
                </div>
                
                <div>
                    {{-- Botón: Descargar PDF --}}
                    <a href="{{ route('programmer.workflows.execution.pdf.download', $execution) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>
                        Descargar PDF
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
