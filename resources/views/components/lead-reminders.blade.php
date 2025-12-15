{{-- Lista de recordatorios --}}
<div class="space-y-4">
    @forelse($reminders as $reminder)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden
            @if($reminder->status === 'pending')
                @if($reminder->due_date->isPast())
                    border-l-4 border-red-500
                @elseif($reminder->due_date->isToday())
                    border-l-4 border-yellow-500
                @else
                    border-l-4 border-blue-500
                @endif
            @else
                border-l-4 border-green-500
            @endif">
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <span class="inline-flex items-center justify-center p-2 rounded-full
                            @if($reminder->priority === 'high')
                                bg-red-100 text-red-500
                            @elseif($reminder->priority === 'medium')
                                bg-yellow-100 text-yellow-500
                            @else
                                bg-blue-100 text-blue-500
                            @endif">
                            <i class="fas fa-bell"></i>
                        </span>
                        <h3 class="ml-2 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $reminder->title }}
                        </h3>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($reminder->status === 'pending')
                            <form action="{{ route('lead-reminders.complete', $reminder) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="text-green-600 hover:text-green-800"
                                        title="Marcar como completado">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('lead-reminders.destroy', $reminder) }}" 
                              method="POST" 
                              class="inline" 
                              onsubmit="return confirm('¿Estás seguro de eliminar este recordatorio?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-600 hover:text-red-800"
                                    title="Eliminar recordatorio">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                @if($reminder->description)
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-2">
                        {{ $reminder->description }}
                    </p>
                @endif

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-500 dark:text-gray-400">
                            <i class="far fa-clock mr-1"></i>
                            {{ $reminder->due_date->format('d/m/Y H:i') }}
                        </span>
                        <span class="text-gray-500 dark:text-gray-400">
                            <i class="far fa-user mr-1"></i>
                            {{ $reminder->user->name }}
                        </span>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($reminder->status === 'completed')
                            bg-green-100 text-green-800
                        @elseif($reminder->status === 'cancelled')
                            bg-gray-100 text-gray-800
                        @elseif($reminder->due_date->isPast())
                            bg-red-100 text-red-800
                        @elseif($reminder->due_date->isToday())
                            bg-yellow-100 text-yellow-800
                        @else
                            bg-blue-100 text-blue-800
                        @endif">
                        @if($reminder->status === 'completed')
                            Completado
                        @elseif($reminder->status === 'cancelled')
                            Cancelado
                        @elseif($reminder->due_date->isPast())
                            Vencido
                        @elseif($reminder->due_date->isToday())
                            Hoy
                        @else
                            Pendiente
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-4 text-gray-500 dark:text-gray-400">
            <i class="fas fa-check-circle text-4xl mb-2"></i>
            <p>No hay recordatorios pendientes</p>
        </div>
    @endforelse
</div>