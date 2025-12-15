<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Configuración Personal
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-6">
                            <!-- Preferencias de Notificaciones -->
                            <div>
                                <h3 class="text-lg font-medium">Preferencias de Notificaciones</h3>
                                <div class="mt-4 space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="email_notifications" name="email_notifications" type="checkbox"
                                                   class="rounded border-gray-300 text-primary focus:ring-primary"
                                                   {{ old('email_notifications', $settings['email_notifications'] ?? true) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3">
                                            <label for="email_notifications" class="font-medium">Notificaciones por Email</label>
                                            <p class="text-gray-500">Recibir notificaciones importantes por correo electrónico</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="task_reminders" name="task_reminders" type="checkbox"
                                                   class="rounded border-gray-300 text-primary focus:ring-primary"
                                                   {{ old('task_reminders', $settings['task_reminders'] ?? true) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3">
                                            <label for="task_reminders" class="font-medium">Recordatorios de Tareas</label>
                                            <p class="text-gray-500">Recibir recordatorios de tareas pendientes</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preferencias de Visualización -->
                            <div class="pt-6">
                                <h3 class="text-lg font-medium">Preferencias de Visualización</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="date_format" class="block font-medium">Formato de Fecha</label>
                                        <select id="date_format" name="date_format" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <option value="d/m/Y" {{ old('date_format', $settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                            <option value="Y-m-d" {{ old('date_format', $settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                            <option value="m/d/Y" {{ old('date_format', $settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="timezone" class="block font-medium">Zona Horaria</label>
                                        <select id="timezone" name="timezone" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <option value="America/Argentina/Buenos_Aires" {{ old('timezone', $settings['timezone'] ?? '') === 'America/Argentina/Buenos_Aires' ? 'selected' : '' }}>Argentina (Buenos Aires)</option>
                                            <option value="America/Santiago" {{ old('timezone', $settings['timezone'] ?? '') === 'America/Santiago' ? 'selected' : '' }}>Chile (Santiago)</option>
                                            <!-- Agregar más zonas horarias según sea necesario -->
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuración SMTP -->
                            <div class="pt-6">
                                <h3 class="text-lg font-medium">Configuración de Correo SMTP</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="smtp_host" class="block font-medium">Servidor SMTP (Host)</label>
                                        <input id="smtp_host" name="smtp_host" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" value="{{ old('smtp_host', $settings['smtp_host'] ?? env('MAIL_HOST')) }}" required>
                                    </div>
                                    <div>
                                        <label for="smtp_port" class="block font-medium">Puerto SMTP</label>
                                        <input id="smtp_port" name="smtp_port" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" value="{{ old('smtp_port', $settings['smtp_port'] ?? env('MAIL_PORT')) }}" required>
                                    </div>
                                    <div>
                                        <label for="smtp_username" class="block font-medium">Usuario SMTP</label>
                                        <input id="smtp_username" name="smtp_username" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" value="{{ old('smtp_username', $settings['smtp_username'] ?? env('MAIL_USERNAME')) }}" required>
                                    </div>
                                    <div>
                                        <label for="smtp_password" class="block font-medium">Contraseña SMTP</label>
                                        <input id="smtp_password" name="smtp_password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" value="{{ old('smtp_password', $settings['smtp_password'] ?? env('MAIL_PASSWORD')) }}" required>
                                    </div>
                                    <div>
                                        <label for="smtp_encryption" class="block font-medium">Encriptación</label>
                                        <select id="smtp_encryption" name="smtp_encryption" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <option value="tls" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? env('MAIL_ENCRYPTION')) === 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? env('MAIL_ENCRYPTION')) === 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? env('MAIL_ENCRYPTION')) === '' ? 'selected' : '' }}>Sin encriptación</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="smtp_from_address" class="block font-medium">Correo Remitente (From)</label>
                                        <input id="smtp_from_address" name="smtp_from_address" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" value="{{ old('smtp_from_address', $settings['smtp_from_address'] ?? env('MAIL_FROM_ADDRESS')) }}" required>
                                    </div>
                                    <div>
                                        <label for="smtp_from_name" class="block font-medium">Nombre Remitente</label>
                                        <input id="smtp_from_name" name="smtp_from_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" value="{{ old('smtp_from_name', $settings['smtp_from_name'] ?? env('MAIL_FROM_NAME')) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                Guardar Preferencias
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>