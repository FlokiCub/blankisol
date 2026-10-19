@extends('layouts.public')

@section('title', 'BLANKISOL | Tu carga. Nuestro compromiso.')
@section('meta_description', 'BLANKISOL es su operador logístico integral: transporte marítimo, aéreo y terrestre con sistema de trazabilidad y control aduanal en tiempo real.')

@section('content')
    <!-- Navbar Corporativo -->
    @include('partials.landing.navbar')

    <!-- Hero Principal con Eslogan -->
    @include('partials.landing.hero')

    <!-- Módulo de Rastreo / Búsqueda de PIN Integrado -->
    @include('partials.landing.tracking_box')

    <!-- Sección Institucional: Quiénes Somos -->
    @include('partials.landing.about')

    <!-- Portafolio de Servicios Logísticos Multimodales -->
    @include('partials.landing.services')

    <!-- Proceso / Cómo Funciona en 4 Pasos -->
    @include('partials.landing.process')

    <!-- Propuesta de Valor y Diferenciales -->
    @include('partials.landing.benefits')

    <!-- Formulario de Captación y Cotización -->
    @include('partials.landing.contact')

    <!-- Pie de Página Corporativo -->
    @include('partials.landing.footer')
@endsection

@section('scripts')
<script>
    // Asignar PIN de prueba desde los botones rápidos
    function setTrackingPin(val) {
        const input = document.getElementById('tracking-pin-input');
        if (input) {
            input.value = val;
            input.focus();
            // Desplazar suavemente a la caja de búsqueda
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // Preseleccionar modalidad en el formulario de contacto al hacer click en una tarjeta
    function selectModalidad(modalidad) {
        const select = document.getElementById('contact-modalidad');
        if (select) {
            select.value = modalidad;
        }
    }

    // Toggle de Menú Móvil Accesible
    document.addEventListener('DOMContentLoaded', function () {
        const menuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const iconOpen = document.getElementById('menu-icon-open');
        const iconClose = document.getElementById('menu-icon-close');

        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', function () {
                const isExpanded = menuBtn.getAttribute('aria-expanded') === 'true';
                menuBtn.setAttribute('aria-expanded', !isExpanded);
                mobileMenu.classList.toggle('hidden');
                iconOpen.classList.toggle('hidden');
                iconClose.classList.toggle('hidden');
            });

            // Cerrar menú móvil al hacer click en cualquier enlace de ancla
            const mobileLinks = document.querySelectorAll('.mobile-nav-link');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function () {
                    mobileMenu.classList.add('hidden');
                    iconOpen.classList.remove('hidden');
                    iconClose.classList.add('hidden');
                    menuBtn.setAttribute('aria-expanded', 'false');
                });
            });
        }

        // Efecto visual al enviar el formulario de tracking
        const trackingForm = document.getElementById('tracking-form');
        const submitBtn = document.getElementById('tracking-submit-btn');
        if (trackingForm && submitBtn) {
            trackingForm.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i> <span>Consultando...</span>';
                submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
            });
        }
    });
</script>
@endsection