@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-text-main">Detalles de la Tarea</h2>
        <p class="text-sm text-text-muted">Información y acciones rápidas</p>
    </div>

    <div class="bg-white border border-primary/20 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900">{{ $task->title }}</h3>
            <p class="mt-2 text-sm text-gray-600">{{ $task->description }}</p>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div class="bg-orange-50 border border-orange-100 rounded-lg p-3">
                    <span class="text-gray-500 block">Vence</span>
                    <span class="font-medium text-gray-800">{{ $task->due_date->format('d/m/Y') }}</span>
                </div>
                <div class="bg-orange-50 border border-orange-100 rounded-lg p-3">
                    <span class="text-gray-500 block">Estado</span>
                    <span class="font-medium text-gray-800 capitalize">{{ str_replace('_',' ', $task->status) }}</span>
                </div>
                <div class="bg-orange-50 border border-orange-100 rounded-lg p-3">
                    <span class="text-gray-500 block">Relacionado con</span>
                    <span class="font-medium text-gray-800">
                        @if ($task->taskable)
                            {{ $task->taskable->name }} ({{ class_basename($task->taskable_type) }})
                        @else
                            N/A
                        @endif
                    </span>
                </div>
            </div>

            <div class="mt-6">
                <form action="{{ route('tasks.update', $task) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="video_link" class="block text-sm font-medium text-gray-700">Enlace de Videollamada</label>
                        <input type="text" name="video_link" id="video_link" value="{{ old('video_link', $task->video_link) }}" class="mt-1 block w-full border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/40 focus:border-primary px-3 py-2">
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition">
                        Guardar Enlace
                    </button>
                </form>
            </div>

            <div class="mt-6">
                @php
                    $phone = null;
                    $name = null;
                    if ($task->taskable) {
                        if ($task->taskable_type === 'App\\Models\\Contact' || $task->taskable_type === 'App\\Models\\Lead') {
                            $phone = $task->taskable->phone;
                            $name = $task->taskable->name;
                        } elseif ($task->taskable_type === 'App\\Models\\Deal' && $task->taskable->contact) {
                            $phone = $task->taskable->contact->phone;
                            $name = $task->taskable->contact->name;
                        }
                    }
                @endphp

                @if($phone)
                    @php
                        $phoneDigits = preg_replace('/\D+/', '', $phone);
                        $waMessage = rawurlencode("Hola {$name}, sobre la tarea: {$task->title}");
                        $waUrl = "https://wa.me/{$phoneDigits}?text={$waMessage}";
                    @endphp
                    <a href="{{ $waUrl }}" target="_blank" class="inline-flex items-center text-green-600 hover:text-green-700 mt-2" title="Enviar WhatsApp">
                        <i class="fab fa-whatsapp fa-lg mr-2"></i> Enviar WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
