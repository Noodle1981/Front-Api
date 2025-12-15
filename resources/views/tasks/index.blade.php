@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-text-main">Mis Tareas</h2>
            <p class="text-sm text-text-muted">Organiza y da seguimiento a tus actividades</p>
        </div>
        <div>
            <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary-dark transition">
                <i class="fas fa-plus mr-2"></i> Nueva tarea
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-primary/20 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-orange-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Título</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Relacionado con</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vencimiento</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($tasks as $task)
                        <tr class="hover:bg-orange-50/40">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $task->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if ($task->taskable)
                                    <a href="{{ route(strtolower(class_basename($task->taskable_type)).'s.show', $task->taskable->id) }}" class="text-primary hover:text-primary-dark hover:underline">
                                        {{ $task->taskable->name }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <x-smart-date :date="$task->due_date" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($task->status == 'completado')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completada</span>
                                @elseif ($task->status == 'en_proceso')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-primary/10 text-primary">En Proceso</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                <a href="{{ route('tasks.edit', $task) }}" class="text-primary hover:text-primary-dark">Editar</a>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta tarea?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-sm text-gray-500 text-center">
                                No tienes tareas asignadas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t bg-white">
            {{ $tasks->links() }}
        </div>
    </div>
</div>
@endsection
