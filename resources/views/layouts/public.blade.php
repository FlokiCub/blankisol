<!DOCTYPE html>
<html lang="es" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'BLANKISOL | Tu carga. Nuestro compromiso.')</title>
    <meta name="description" content="@yield('meta_description', 'BLANKISOL SURL: Soluciones integrales para empresas e instituciones. Logística y trazabilidad avanzada, y Servicios Tecnológicos Integrales.')">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#051f42">

    <!-- Open Graph / Redes Sociales -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="BLANKISOL SURL">
    <meta property="og:title" content="@yield('title', 'BLANKISOL | Tu carga. Nuestro compromiso.')">
    <meta property="og:description" content="@yield('meta_description', 'BLANKISOL SURL: Soluciones integrales en Logística y Trazabilidad, y Servicios Tecnológicos Integrales.')">
    <meta property="og:locale" content="es_ES">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">

    <!-- Favicon Oficial Corporativo Adaptativo (Claro / Oscuro) -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon-light/favicon.ico') }}" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-light/favicon-32x32.png') }}" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-light/favicon-16x16.png') }}" media="(prefers-color-scheme: light)">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon-light/apple-touch-icon.png') }}" media="(prefers-color-scheme: light)">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon-dark/favicon.ico') }}" media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-dark/favicon-32x32.png') }}" media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-dark/favicon-16x16.png') }}" media="(prefers-color-scheme: dark)">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon-dark/apple-touch-icon.png') }}" media="(prefers-color-scheme: dark)">

    <!-- Fallback general -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon-dark/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'BLANKISOL | Tu carga. Nuestro compromiso.')">
    <meta name="twitter:description" content="@yield('meta_description', 'BLANKISOL SURL: Soluciones integrales en Logística y Trazabilidad, y Servicios Tecnológicos Integrales.')">
    <meta name="twitter:image" content="{{ asset('images/logo.png') }}">

    <!-- Preconexión a Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Pro/Free Icons -->
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
                            'neutral-light': '#f8fafc',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                    },
                    boxShadow: {
                        'accent-glow': '0 8px 25px -4px rgba(245, 120, 43, 0.45)',
                        'card-dark': '0 10px 30px -5px rgba(2, 13, 28, 0.6)',
                        'corporate-lg': '0 16px 40px -8px rgba(5, 31, 66, 0.2)',
                    }
                }
            }
        }
    </script>

    <!-- Hojas de Estilos Corporativas Personalizadas -->
    <link rel="stylesheet" href="{{ asset('css/blankisol.css') }}">

    @yield('head_extra')
</head>
<body class="min-h-full flex flex-col antialiased bg-[#051f42] text-slate-100 selection:bg-[#f5782b] selection:text-white relative overflow-x-hidden font-sans">

    <!-- Enlace de salto accesible para lectores de pantalla -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 z-[100] px-4 py-2 bg-[#f5782b] text-white font-bold rounded-lg shadow-xl">
        Saltar al contenido principal
    </a>

    <!-- Capa ambiental de fondo / Efecto lumínico sutil corporativo -->
    <div class="fixed inset-0 bg-grid-mesh pointer-events-none opacity-40 -z-10"></div>
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[1100px] h-[550px] bg-gradient-to-b from-[#0a3166]/50 via-[#051f42]/30 to-transparent blur-[140px] pointer-events-none animate-pulse-subtle -z-10"></div>
    <div class="fixed bottom-0 right-0 w-[650px] h-[450px] bg-[#f5782b]/8 blur-[160px] pointer-events-none -z-10"></div>
    <div class="fixed top-1/3 left-0 w-[550px] h-[550px] bg-[#020d1c]/90 blur-[130px] pointer-events-none -z-10"></div>

    <!-- Contenido Principal Dinámico -->
    <main id="main-content" class="flex-grow">
        @yield('content')
    </main>

    <!-- Widget Flotante de Contacto Inmediato (Speed Dial & WhatsApp) -->
    @include('partials.common.floating_contact')

    <!-- Scripts y complementos de página -->
    @yield('scripts')
</body>
</html>
