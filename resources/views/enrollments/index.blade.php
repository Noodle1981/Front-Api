<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inscripciones Activas') }}
            </h2>
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Aquí puedes ver y gestionar todas las secuencias en curso
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(config('mail.default') === 'log' || empty(config('mail.mailers.smtp.host')))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-xl mr-2"></i>
                        <p>
                            <span class="font-bold">Modo de Desarrollo:</span>
                            Los correos electrónicos se registrarán en el archivo de logs en lugar de enviarse.
                            Esto no afecta a las tareas, llamadas o videollamadas.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Notifications Section --}}
            @if ($notifications->count())
                <div class="space-y-4 mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-bell text-yellow-500 mr-2"></i>
                        Acciones Pendientes ({{ $notifications->count() }})
                    </h3>
                    
                    @foreach ($notifications as $notification)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="border-l-4 
                                @switch($notification->data['step_type'] ?? 'task')
                                    @case('email') border-green-500 @break
                                    @case('call') border-yellow-500 @break
                                    @case('video_call') border-purple-500 @break
                                    @default border-blue-500 @break
                                @endswitch
                            ">
                                <div class="p-4">
                                    <!-- Header -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h4 class="font-semibold text-lg text-gray-800">{{ $notification->data['enrollable_name'] }}</h4>
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Secuencia:</span> {{ $notification->data['sequence_name'] }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($notification->data['step_type'] ?? 'task')
                                                @case('email') bg-green-100 text-green-800 @break
                                                @case('call') bg-yellow-100 text-yellow-800 @break
                                                @case('video_call') bg-purple-100 text-purple-800 @break
                                                @default bg-blue-100 text-blue-800 @break
                                            @endswitch
                                        ">
                                            @switch($notification->data['step_type'] ?? 'task')
                                                @case('email')
                                                    <i class="fas fa-envelope mr-1"></i> Email
                                                    @break
                                                @case('call')
                                                    <i class="fas fa-phone mr-1"></i> Llamada
                                                    @break
                                                @case('video_call')
                                                    <i class="fas fa-video mr-1"></i> Videollamada
                                                    @break
                                                @default
                                                    <i class="fas fa-tasks mr-1"></i> Tarea
                                                    @break
                                            @endswitch
                                        </span>
                                    </div>

                                    <!-- Content -->
                                    <div class="mb-4">
                                        <h5 class="font-medium text-gray-700 mb-2">
                                            {{ $notification->data['step_subject'] ?: 'Sin asunto' }}
                                        </h5>
                                        <p class="text-gray-600 text-sm">
                                            {{ $notification->data['message'] }}
                                        </p>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                        <div class="flex items-center space-x-3">
                                            @if (!empty($notification->data['contact_phone']))
                                                @php
                                                    $phoneDigits = preg_replace('/\D+/', '', $notification->data['contact_phone']);
                                                    $waMessage = rawurlencode("Hola {$notification->data['enrollable_name']}, te contactamos desde {$notification->data['sequence_name']} respecto a: {$notification->data['step_subject']}");
                                                    $waUrl = "https://wa.me/{$phoneDigits}?text={$waMessage}";
                                                @endphp
                                                <a href="{{ $waUrl }}" target="_blank" 
                                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                                                    <i class="fab fa-whatsapp mr-2"></i>
                                                    Contactar por WhatsApp
                                                </a>
                                            @endif

                                            @if (!empty($notification->data['video_link']))
                                                <a href="{{ $notification->data['video_link'] }}" target="_blank" 
                                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 transition-colors">
                                                    <i class="fas fa-video mr-2"></i>
                                                    Abrir videollamada
                                                </a>
                                            @endif
                                        </div>

                                        @if ($notification->data['task_id'])
                                            <form action="{{ route('enrollments.completeStep', $notification->data['enrollment_id']) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="task_id" value="{{ $notification->data['task_id'] }}">
                                                <button type="submit" 
                                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                                    <i class="fas fa-check mr-2"></i>
                                                    Marcar como Completado
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Inscripciones Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Todas las Inscripciones</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contacto / Lead
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Secuencia
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado Actual
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Próximo Paso
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($enrollments as $enrollment)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $enrollment->enrollable->name }}
                                                    </div>
                                                    @if($enrollment->enrollable->email)
                                                        <div class="text-sm text-gray-500">
                                                            {{ $enrollment->enrollable->email }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ $enrollment->sequence->name }}</div>
                                            <div class="mt-1">
                                                <div class="flex items-center">
                                                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                        <div class="h-full bg-blue-600 rounded-full" 
                                                             style="width: {{ $enrollment->getProgressPercentage() }}%">
                                                        </div>
                                                    </div>
                                                    <span class="ml-2 text-xs text-gray-500">
                                                        {{ $enrollment->completedSteps()->count() }} / {{ $enrollment->sequence->steps()->count() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($enrollment->currentStep)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @switch($enrollment->currentStep->type)
                                                        @case('email') bg-green-100 text-green-800 @break
                                                        @case('call') bg-yellow-100 text-yellow-800 @break
                                                        @case('video_call') bg-purple-100 text-purple-800 @break
                                                        @default bg-blue-100 text-blue-800 @break
                                                    @endswitch">
                                                    @switch($enrollment->currentStep->type)
                                                        @case('email')
                                                            <i class="fas fa-envelope mr-1"></i> Email
                                                            @break
                                                        @case('call')
                                                            <i class="fas fa-phone mr-1"></i> Llamada
                                                            @break
                                                        @case('video_call')
                                                            <i class="fas fa-video mr-1"></i> Videollamada
                                                            @break
                                                        @default
                                                            <i class="fas fa-tasks mr-1"></i> Tarea
                                                            @break
                                                    @endswitch
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i> Completada
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($enrollment->next_step_due_at)
                                                <div class="text-sm text-gray-900">
                                                    {{ \Carbon\Carbon::parse($enrollment->next_step_due_at)->format('d/m/Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ \Carbon\Carbon::parse($enrollment->next_step_due_at)->diffForHumans() }}
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500">No hay próximos pasos</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                @php
                                                    $phone = $enrollment->enrollable->phone ?? null;
                                                @endphp
                                                @if($phone)
                                                    @php
                                                        $phoneDigits = preg_replace('/\D+/', '', $phone);
                                                        $waMessage = rawurlencode("Hola {$enrollment->enrollable->name}, te contactamos desde {$enrollment->sequence->name}");
                                                        $waUrl = "https://wa.me/{$phoneDigits}?text={$waMessage}";
                                                    @endphp
                                                    <a href="{{ $waUrl }}" target="_blank" 
                                                       class="text-green-600 hover:text-green-900" 
                                                       title="Contactar por WhatsApp">
                                                        <i class="fab fa-whatsapp text-xl"></i>
                                                    </a>
                                                @endif

                                                <button type="button" 
                                                        class="text-blue-600 hover:text-blue-900" 
                                                        title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <form action="{{ route('enrollments.destroy', $enrollment) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('¿Estás seguro? Esta acción no se puede deshacer.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900"
                                                            title="Eliminar inscripción">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            No hay inscripciones activas en este momento
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
