<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-history mr-2"></i> {{ __('Historial de Emails') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filters --}}
            <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-4 border border-white/20 mb-6 hover:bg-white/80 transition-all">
                <form method="GET" action="{{ route('admin.email.history') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tipo</label>
                        <select name="type" class="w-full text-sm border-gray-300 rounded-md">
                            <option value="">Todos</option>
                            <option value="test" {{ request('type') == 'test' ? 'selected' : '' }}>Prueba</option>
                            <option value="api_error" {{ request('type') == 'api_error' ? 'selected' : '' }}>Error API</option>
                            <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>Sistema</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                        <select name="status" class="w-full text-sm border-gray-300 rounded-md">
                            <option value="">Todos</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Enviado</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Fallido</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Desde</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Hasta</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="md:col-span-4 flex justify-end gap-2">
                        <a href="{{ route('admin.email.history') }}" class="px-3 py-1.5 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Limpiar</a>
                        <button type="submit" class="px-3 py-1.5 text-sm bg-brand-dark text-white rounded hover:bg-gray-800"><i class="fas fa-filter mr-1"></i> Filtrar</button>
                    </div>
                </form>
            </div>

            {{-- Email History Table --}}
            <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg border border-white/20 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destinatario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asunto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white/30 divide-y divide-gray-200">
                            @forelse($emails as $email)
                                <tr class="hover:bg-white/60 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $email->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $email->recipient_email }}
                                        @if($email->user)
                                            <span class="text-xs text-gray-500">({{ $email->user->name }})</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $email->subject }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($email->type === 'test')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Prueba</span>
                                        @elseif($email->type === 'api_error')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Error API</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Sistema</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($email->status === 'sent')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle"></i> Enviado
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800" title="{{ $email->error_message }}">
                                                <i class="fas fa-times-circle"></i> Fallido
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No hay emails en el historial
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($emails->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $emails->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
