<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <!-- ... (código existente del head) ... -->
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts y Font Awesome -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/TU_KIT_DE_FONT_AWESOME.js" crossorigin="anonymous"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-white antialiased">
        <div class="min-h-screen w-full bg-orange-xamanen relative overflow-hidden flex flex-col justify-center items-center py-6 sm:py-0">
            <div id="particles-js" class="absolute inset-0"></div>
            
            <div class="relative z-10 flex flex-col items-center">
                {{-- Bloque del logo y título ajustado --}}
                <div class="flex flex-col items-center mb-8"> {{-- Contenedor para el logo y el texto, centrado y con margen inferior --}}
                    <a href="/" class="flex flex-col items-center"> {{-- Enlace que contiene ambos elementos --}}
                        {{-- DIV con fondo blanco para la imagen del logo --}}
                        <div class="bg-white p-1 rounded-md shadow mb-4"> {{-- Añadido mb-4 para separar el logo del texto "Acceso a Grupo Xamanen" --}}
                            <img src="{{ asset('img/EP.png') }}" alt="EP Consultora Logo" class="block h-9 w-auto"> 
                        </div>
                        <h2 class="font-headings text-3xl font-bold text-white">Acceso a CRM</h2>
                    </a>
                </div>
                {{-- FIN Bloque del logo y título ajustado --}}

                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white/10 backdrop-blur-sm shadow-xl overflow-hidden rounded-lg border border-white/20">
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </div>
            </div>
        </div>

        {{-- SCRIPT PARA LAS PARTÍCULAS --}}
        <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                particlesJS('particles-js', {"particles":{"number":{"value":80,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"circle"},"opacity":{"value":0.5,"random":false},"size":{"value":3,"random":true},"line_linked":{"enable":true,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":6,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"repulse"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"repulse":{"distance":100,"duration":0.4},"push":{"particles_nb":4}}},"retina_detect":true});
            });
        </script>
    </body>
</html>