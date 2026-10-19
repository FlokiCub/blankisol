<!DOCTYPE html>
<html lang="es" class="h-full bg-[#051f42] text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Acceso al Sistema' }} | BLANKISOL SCGI</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#051f42">

        <!-- Favicon Oficial -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            primary: '#051f42',
                            'primary-hover': '#03132a',
                            surface: '#0a254e',
                            dark: '#020d1c',
                            accent: '#f5782b',
                            'accent-hover': '#e0651a',
                            'accent-light': '#ff934f',
                            neutral: '#cbcfd4',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- Estilos Corporativos -->
    <link rel="stylesheet" href="{{ asset('css/blankisol.css') }}">
</head>
<body class="min-h-full flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 selection:bg-[#f5782b] selection:text-white relative overflow-x-hidden font-sans">

    <!-- Ambient background glow -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[900px] h-[450px] bg-gradient-to-b from-[#0a3166]/40 via-[#051f42]/30 to-transparent blur-[140px] pointer-events-none -z-10"></div>
    <div class="fixed bottom-0 right-0 w-[500px] h-[350px] bg-[#f5782b]/5 blur-[150px] pointer-events-none -z-10"></div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3.5 group focus:outline-none focus:ring-2 focus:ring-[#f5782b] rounded-2xl p-1">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#0a3166] via-[#051f42] to-[#020d1c] border border-[#f5782b]/40 flex items-center justify-center text-[#f5782b] shadow-xl group-hover:scale-105 group-hover:border-[#f5782b] transition-all duration-300">
                <i class="fa-solid fa-ship text-xl"></i>
            </div>
            <div class="text-left">
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-black tracking-tight text-white group-hover:text-[#ff934f] transition-colors">BLANKISOL</span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-[#f5782b]/15 text-[#ff934f] border border-[#f5782b]/30 font-bold uppercase font-mono">SCGI</span>
                </div>
                <p class="text-xs text-[#cbcfd4] font-medium">Torre de Control Logística & Trazabilidad</p>
            </div>
        </a>

        <h1 class="mt-6 text-xl sm:text-2xl font-black tracking-tight text-white">
            {{ $heading ?? 'Ingreso a Torre de Control' }}
        </h1>
        <p class="mt-1.5 text-xs text-slate-300">
            Autenticación corporativa con control de acceso basado en roles (RBAC)
        </p>
    </div>

    <!-- Main Card Slot -->
    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
        @yield('content')
    </div>

    <!-- Footer Simple -->
    <footer class="mt-8 text-center text-xs text-slate-400 space-y-2">
        <div class="flex items-center justify-center gap-4 text-[11px]">
            <a href="{{ route('home') }}" class="text-[#cbcfd4] hover:text-[#f5782b] transition-colors flex items-center gap-1">
                <i class="fa-solid fa-house text-[10px]"></i>
                <span>Portal Principal</span>
            </a>
            <span class="text-slate-600">&bull;</span>
            <a href="{{ route('tracking.index') }}" class="text-[#cbcfd4] hover:text-[#f5782b] transition-colors flex items-center gap-1">
                <i class="fa-solid fa-satellite-dish text-[10px]"></i>
                <span>Rastrear Envío sin Clave</span>
            </a>
        </div>
        <p class="text-[11px] text-slate-500">
            &copy; {{ date('Y') }} BLANKISOL SCGI &bull; «Tu carga. Nuestro compromiso.»
        </p>
    </footer>

</body>
</html>