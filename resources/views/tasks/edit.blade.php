@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-text-main">Editar Tarea</h2>
        <p class="text-sm text-text-muted">Actualiza los detalles y el estado de la tarea</p>
    </div>

    <div class="bg-white border border-primary/20 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6">
            <form action="{{ route('tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}" class="mt-1 block w-full border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/40 focus:border-primary px-3 py-2" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea name="description" id="description" rows="4" class="mt-1 block w-full border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/40 focus:border-primary px-3 py-2">{{ old('description', $task->description) }}</textarea>
                        </div>

                        <!-- Video Link -->
                        <div class="mb-4">
                            <label for="video_link" class="block text-sm font-medium text-gray-700">Enlace del video</label>
                            <input type="text" name="video_link" id="video_link" value="{{ old('video_link', $task->video_link) }}" class="mt-1 block w-full border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/40 focus:border-primary px-3 py-2">
                        </div>

                        <!-- Due Date -->
                        <div class="mb-4">
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $task->due_date->format('Y-m-d')) }}" class="mt-1 block w-full border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/40 focus:border-primary px-3 py-2" required>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select name="status" id="status" class="mt-1 block w-full border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/40 focus:border-primary px-3 py-2" required>
                                <option value="pendiente" @selected($task->status == 'pendiente')>Pendiente</option>
                                <option value="en_proceso" @selected($task->status == 'en_proceso')>En Proceso</option>
                                <option value="completado" @selected($task->status == 'completado')>Completado</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('tasks.index') }}" class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Cancelar
                            </a>
                            <button type="submit" class="ml-4 inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150">
                                Guardar Cambios
                            </button>
                        </div>
            </form>
        </div>
    </div>
</div>
@endsection