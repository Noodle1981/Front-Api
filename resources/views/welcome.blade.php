<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Grupo Xamanen Portal') }} - Portal de Clientes</title>

    <!-- Fonts y Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Poppins:wght@700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-white">

    {{-- Main Container: Brand Dark Blue Background --}}
    <div class="min-h-screen w-full bg-brand-dark relative overflow-hidden flex flex-col">

        {{-- Background Image --}}
        <div class="absolute inset-0 z-0 bg-cover bg-center"
            style="background-image: url('{{ asset('img/marketing-bg8.png') }}');">
        </div>

        {{-- Particles Container (Optional, kept for effect over the blue/image) --}}
        <div id="particles-js" class="absolute inset-0 z-0 opacity-30"></div>

        {{-- Content Wrapper --}}
        <div class="relative z-10 flex flex-col min-h-screen">
            <header class="w-full">
                <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex justify-between items-center">
                    {{-- Logo --}}
                    <a href="/" class="flex items-center space-x-2">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="block h-16 w-auto drop-shadow-lg">
                    </a>

                    {{-- Login Button --}}
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}"
                            class="px-6 py-2 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-full transition border border-white/20 backdrop-blur-sm">
                            Login
                        </a>
                    </div>
                </nav>
            </header>

            <main class="flex-grow flex items-center justify-center text-center px-4 relative">
                <div class="max-w-5xl relative z-10">

                    {{-- Typography Refined --}}
                    <h1
                        class="font-headings text-5xl md:text-7xl font-extrabold text-white mb-6 drop-shadow-2xl tracking-tight">
                        Gestión de Automatización <br />
                        <span class="text-brand-accent">Contable</span>
                    </h1>

                    <p class="max-w-2xl mx-auto text-xl text-gray-300 mb-12 font-light">
                        Accede a tu información financiera, reportes y gestión administrativa en un solo lugar.
                    </p>

                    <!-- Role Cards -->
                    <div class="grid md:grid-cols-2 gap-8 mb-12 max-w-4xl mx-auto">

                        {{-- Admin Card --}}
                        <a href="{{ route('login') }}"
                            class="group block bg-white/5 backdrop-blur-md rounded-2xl p-8 border border-white/10 hover:border-brand-accent/50 hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-1">
                            <div
                                class="text-4xl mb-4 text-brand-accent group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h3 class="text-2xl font-bold mb-3 text-white">Administrador</h3>
                            <p class="text-sm text-gray-400 group-hover:text-gray-200 transition-colors">
                                Control total del sistema, gestión de usuarios y métricas globales.
                            </p>
                        </a>

                        {{-- User Card --}}
                        <a href="{{ route('login') }}"
                            class="group block bg-white/5 backdrop-blur-md rounded-2xl p-8 border border-white/10 hover:border-brand-accent/50 hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-1">
                            <div
                                class="text-4xl mb-4 text-brand-accent group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="text-2xl font-bold mb-3 text-white">Usuario</h3>
                            <p class="text-sm text-gray-400 group-hover:text-gray-200 transition-colors">
                                Consulta tus estados de cuenta, servicios y actualizaciones.
                            </p>
                        </a>
                    </div>

                    {{-- CTA Button --}}
                    <a href="{{ route('login') }}" class="inline-block">
                        <x-primary-button class="px-10 py-4 text-lg">
                            Iniciar Sesión
                        </x-primary-button>
                    </a>

                    <p class="mt-8 text-sm text-gray-500">
                        v1.0.0
                    </p>
                </div>
            </main>

            <footer class="w-full py-6 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} Grupo Xamanen. Todos los derechos reservados.</p>
            </footer>
        </div>
    </div>

    {{-- Particles Script --}}
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            particlesJS('particles-js', { "particles": { "number": { "value": 40, "density": { "enable": true, "value_area": 800 } }, "color": { "value": "#ffffff" }, "shape": { "type": "circle" }, "opacity": { "value": 0.1, "random": false }, "size": { "value": 3, "random": true }, "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.1, "width": 1 }, "move": { "enable": true, "speed": 2, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false } }, "interactivity": { "detect_on": "canvas", "events": { "onhover": { "enable": true, "mode": "repulse" }, "onclick": { "enable": true, "mode": "push" }, "resize": true }, "modes": { "repulse": { "distance": 100, "duration": 0.4 }, "push": { "particles_nb": 4 } } }, "retina_detect": true });
        });
    </script>
</body>

</html>