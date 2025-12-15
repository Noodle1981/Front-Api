@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto py-12">
    <x-card class="bg-white border border-primary-light shadow-lg">
        <x-slot name="header">
            <h2 class="font-headings text-3xl font-extrabold text-primary-dark tracking-tight mb-2">Configuración del Sistema de Correo</h2>
        </x-slot>
        <div class="p-8">
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            <form action="{{ route('admin.settings.email.update') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mail_mailer" class="block mb-2 font-bold text-black">Mailer (ej: smtp)</label>
                        <input id="mail_mailer" name="mail_mailer" type="text" class="block w-full rounded-md border border-primary-light p-2 text-black" value="{{ old('mail_mailer', $settings['mail_mailer'] ?? 'smtp') }}" required />
                    </div>
                    <div>
                        <label for="mail_host" class="block mb-2 font-bold text-black">Host SMTP (ej: smtp.gmail.com)</label>
                        <input id="mail_host" name="mail_host" type="text" class="block w-full rounded-md border border-primary-light p-2 text-black" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" required />
                    </div>
                    <div>
                        <label for="mail_port" class="block mb-2 font-bold text-black">Puerto (ej: 587)</label>
                        <input id="mail_port" name="mail_port" type="number" class="block w-full rounded-md border border-primary-light p-2 text-black" value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}" required />
                    </div>
                    <div>
                        <label for="mail_encryption" class="block mb-2 font-bold text-black">Cifrado (ej: tls, ssl)</label>
                        <input id="mail_encryption" name="mail_encryption" type="text" class="block w-full rounded-md border border-primary-light p-2 text-black" value="{{ old('mail_encryption', $settings['mail_encryption'] ?? 'tls') }}" />
                    </div>
                    <div class="md:col-span-2">
                        <label for="mail_username" class="block mb-2 font-bold text-black">Nombre de Usuario</label>
                        <input id="mail_username" name="mail_username" type="text" class="block w-full rounded-md border border-primary-light p-2 text-black" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" />
                    </div>
                    <div class="md:col-span-2">
                        <label for="mail_password" class="block mb-2 font-bold text-black">Contraseña / App Password</label>
                        <input id="mail_password" name="mail_password" type="password" class="block w-full rounded-md border border-primary-light p-2 text-black" />
                        <p class="text-xs text-gray-500 mt-1">La contraseña no se muestra por seguridad. Déjalo en blanco para mantener la contraseña actual.</p>
                    </div>
                    <div>
                        <label for="mail_from_address" class="block mb-2 font-bold text-black">Email Remitente</label>
                        <input id="mail_from_address" name="mail_from_address" type="email" class="block w-full rounded-md border border-primary-light p-2 text-black" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" required />
                    </div>
                    <div>
                        <label for="mail_from_name" class="block mb-2 font-bold text-black">Nombre Remitente</label>
                        <input id="mail_from_name" name="mail_from_name" type="text" class="block w-full rounded-md border border-primary-light p-2 text-black" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? config('app.name')) }}" required />
                    </div>
                </div>
                <div class="flex items-center justify-end mt-8 space-x-4">
                    <x-secondary-button type="button" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                        Cancelar
                    </x-secondary-button>
                    <x-primary-button>
                        Guardar Configuración
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-card>
</div>
@endsection