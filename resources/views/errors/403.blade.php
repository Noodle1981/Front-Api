@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
                <div class="mb-4">
                    <i class="fas fa-shield-alt text-red-500 text-6xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Acceso Denegado</h2>
                <p class="text-gray-600 mb-6">No tienes permisos para acceder a esta área del sistema.</p>
                
                <div class="space-y-2">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Ir al Panel de Administración
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-briefcase mr-2"></i>
                            Ir al CRM
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
