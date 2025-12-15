<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark"> {{-- Forzamos el modo oscuro si es necesario
--}}

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Grupo Xamanen CRM') }}</title>

    <!-- Fonts -->
    {{-- Reemplazamos Figtree por las fuentes de nuestro diseño: Inter y Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">

    {{-- Añadimos Font Awesome si lo vas a usar para iconos --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- ...código existente... -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <!-- Scripts y Estilos -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">

    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-brand-dark flex-shrink-0 hidden md:block border-r border-white/10">
            @if(auth()->check() && auth()->user()->isAdmin())
                @include('layouts.admin-navigation')
            @else
                @include('layouts.navigation')
            @endif
        </aside>

        <!-- Mobile Header & Content -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden relative">

            {{-- Background Image --}}
            <div class="absolute inset-0 z-0 bg-cover bg-center"
                style="background-image: url('{{ asset('img/marketing-bg8.png') }}');">
            </div>

            {{-- Mobile Hamburger (Visible only on small screens) --}}
            <div class="md:hidden bg-brand-dark text-white p-4 flex justify-between items-center relative z-10">
                <span class="font-bold text-lg">Menu</span>
                <button @click="open = !open" x-data="{ open: false }">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/80 backdrop-blur-sm shadow relative z-10">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <h2 class="font-headings font-semibold text-xl text-gray-800 leading-tight">
                            {{ $header }}
                        </h2>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 relative z-10">
                {{-- Eliminar max-w-7xl si queremos full width, o mantenerlo para centrar contenido --}}
                {{-- Soporta tanto layout por secciones (@extends/@section) como componentes ({{ $slot }}) --}}
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </div>
</body>

</html>