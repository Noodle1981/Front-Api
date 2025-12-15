<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'Grupo Xamanen CRM') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Poppins:wght@700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-white">
    <div class="min-h-screen w-full bg-brand-dark relative overflow-hidden flex flex-col justify-center items-center">

        {{-- Background Image --}}
        <div class="absolute inset-0 z-0 bg-cover bg-center"
            style="background-image: url('{{ asset('img/marketing-bg8.png') }}');">
        </div>

        <div id="particles-js" class="absolute inset-0 z-0 opacity-30"></div>

        <div class="relative z-10 flex flex-col min-h-screen justify-center items-center w-full">
            <main class="flex-grow flex flex-col items-center justify-center text-center px-4 relative w-full">

                {{-- Logo --}}
                <div class="mb-8">
                    <a href="/">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-20 w-auto drop-shadow-lg">
                    </a>
                </div>

                <div
                    class="max-w-md w-full mx-auto bg-white/5 backdrop-blur-md rounded-2xl shadow-2xl border border-white/10 p-8">
                    <h2 class="text-3xl font-extrabold mb-6 text-white tracking-tight">Iniciar Sesión</h2>
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />
                    <form method="POST" action="{{ route('login') }}" class="space-y-6 text-left">
                        @csrf
                        <!-- Email Address -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-white" />
                            <x-text-input id="email"
                                class="block mt-1 w-full bg-white/20 text-white placeholder-white/70 border-white/30 focus:border-cyan-400 focus:ring-cyan-400"
                                type="email" name="email" :value="old('email')" required autofocus
                                autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-300" />
                        </div>
                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Password')" class="text-white" />
                            <x-text-input id="password"
                                class="block mt-1 w-full bg-white/20 text-white placeholder-white/70 border-white/30 focus:border-cyan-400 focus:ring-cyan-400"
                                type="password" name="password" required autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-300" />
                        </div>
                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox"
                                class="rounded border-white/20 bg-gray-900/50 text-cyan-400 shadow-sm focus:ring-cyan-400"
                                name="remember">
                            <label for="remember_me" class="ms-2 text-sm text-white/80">{{ __('Recordarme') }}</label>
                        </div>
                        <div class="flex items-center justify-between">
                            @if (Route::has('password.request'))
                                <a class="underline text-sm text-white/70 hover:text-white"
                                    href="{{ route('password.request') }}">
                                    {{ __('¿Olvidaste tu contraseña?') }}
                                </a>
                            @endif
                            <button type="submit"
                                class="px-6 py-3 font-bold rounded-full bg-white transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-white/40"
                                style="color: #166264;">Entrar</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            particlesJS('particles-js', { "particles": { "number": { "value": 80, "density": { "enable": true, "value_area": 800 } }, "color": { "value": "#ffffff" }, "shape": { "type": "circle" }, "opacity": { "value": 0.5, "random": false }, "size": { "value": 3, "random": true }, "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 }, "move": { "enable": true, "speed": 6, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false } }, "interactivity": { "detect_on": "canvas", "events": { "onhover": { "enable": true, "mode": "repulse" }, "onclick": { "enable": true, "mode": "push" }, "resize": true }, "modes": { "repulse": { "distance": 100, "duration": 0.4 }, "push": { "particles_nb": 4 } } }, "retina_detect": true });
        });
    </script>
</body>

</html>