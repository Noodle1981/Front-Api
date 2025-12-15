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
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts y Estilos -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50">

    <div class="flex min-h-screen bg-gray-100">

        {{-- Sidebar Admin --}}
        <aside class="w-64 bg-brand-dark flex-shrink-0 hidden md:block border-r border-white/10">
            @include('layouts.admin-navigation')
        </aside>

        {{-- Main Content Column --}}
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden relative">

            {{-- Background Image --}}
            <div class="absolute inset-0 z-0 bg-cover bg-center"
                style="background-image: url('{{ asset('img/marketing-bg8.png') }}');">
            </div>

            {{-- Mobile Hamburger (Placeholder) --}}
            <div class="md:hidden bg-brand-dark text-white p-4 flex justify-between items-center relative z-10">
                <span class="font-bold text-lg">Admin Panel</span>
                {{-- Mobile menu logic needing JS/Alpine --}}
            </div>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/80 backdrop-blur-sm shadow relative z-10">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <h1 class="font-headings text-2xl font-bold text-gray-800 leading-tight">
                            {{ $header }}
                        </h1>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 relative z-10">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>

</html>