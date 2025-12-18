<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-envelope mr-2"></i> {{ __('Configuración de Email') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- SMTP Configuration Info --}}
            <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-white/20 mb-6 hover:bg-white/80 transition-all">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i> Información de Configuración SMTP
                </h3>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        <strong>Nota:</strong> El servidor de correo debe estar configurado en el archivo <code class="bg-gray-200 px-2 py-1 rounded">.env</code>
                    </p>
                    <p class="text-xs text-gray-600 mt-2">
                        Variables necesarias: MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-semibold text-gray-700">Mailer:</span>
                        <span class="text-gray-600">{{ config('mail.default') }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Host:</span>
                        <span class="text-gray-600">{{ config('mail.mailers.smtp.host') }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Puerto:</span>
                        <span class="text-gray-600">{{ config('mail.mailers.smtp.port') }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Encriptación:</span>
                        <span class="text-gray-600">{{ config('mail.mailers.smtp.encryption') }}</span>
                    </div>
                </div>
            </div>

            {{-- Test Email Form --}}
            <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-white/20 hover:bg-white/80 transition-all">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-paper-plane mr-2 text-green-500"></i> Enviar Email de Prueba
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Envía un email de prueba para verificar que la configuración SMTP funciona correctamente.
                </p>

                <form method="POST" action="{{ route('admin.email.test') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email de Destino
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               required
                               value="{{ old('email', auth()->user()->email) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-accent focus:border-brand-accent"
                               placeholder="ejemplo@correo.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Enviar Email de Prueba
                        </button>
                    </div>
                </form>
            </div>

            {{-- Quick Links --}}
            <div class="mt-6 grid grid-cols-2 gap-4">
                <a href="{{ route('admin.email.history') }}" 
                   class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-4 border border-white/20 hover:bg-white/90 transition-all flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-history text-2xl text-blue-500 mr-3"></i>
                        <div>
                            <h4 class="font-bold text-gray-800">Historial</h4>
                            <p class="text-xs text-gray-600">Ver emails enviados</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>

                <a href="{{ route('admin.email.stats') }}" 
                   class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-4 border border-white/20 hover:bg-white/90 transition-all flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-chart-bar text-2xl text-purple-500 mr-3"></i>
                        <div>
                            <h4 class="font-bold text-gray-800">Estadísticas</h4>
                            <p class="text-xs text-gray-600">Ver métricas</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
