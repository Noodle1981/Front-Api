<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Grupo Xamanen CRM') }} - Administración</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts y Estilos -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
    </head>
    <body class="font-sans antialiased text-light-text">
        
        {{-- Contenedor principal con el fondo --}}
        <div class="min-h-screen bg-white-void">
            
            {{-- Navegación de administración --}}
            @include('layouts.admin-navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="pt-8 pb-4">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h1 class="font-headings text-3xl font-bold text-light-text">
                            {{ $header }}
                        </h1>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="pb-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>