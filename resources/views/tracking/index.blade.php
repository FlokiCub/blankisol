@extends('layouts.public')

@section('title', 'BLANKISOL SURL | Soluciones Integrales para Empresas e Instituciones')
@section('meta_description', 'En BLANKISOL SURL conectamos soluciones logísticas y servicios tecnológicos integrales para responder a las necesidades operativas de nuestros clientes.')

@section('content')
    <!-- Navbar Corporativo Unificado -->
    @include('partials.landing.navbar')

    <!-- Hero Principal Corporativo con las 2 Líneas de Negocio -->
    @include('partials.landing.hero')

    <!-- Bloques Principales de Negocio: Logística y Trazabilidad / Servicios Tecnológicos Integrales -->
    @include('partials.landing.business_areas')

    <!-- Módulo de Rastreo / Búsqueda de PIN de Carga en Vivo -->
    @include('partials.landing.tracking_box')

    <!-- Sección Institucional: Identidad y Propósito Corporativo -->
    @include('partials.landing.about')

    <!-- Portafolio de Servicios de la División Logística -->
    @include('partials.landing.services')

    <!-- Proceso / Cómo Funciona el Traslado Logístico -->
    @include('partials.landing.process')

    <!-- Propuesta de Valor y Diferenciales -->
    @include('partials.landing.benefits')

    <!-- Centro Interactivo de Preguntas Frecuentes (FAQ) -->
    @include('partials.landing.faq')

    <!-- Formulario de Captación y Cotización Logística -->
    @include('partials.landing.contact')

    <!-- Pie de Página Corporativo Actualizado -->
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
            const contactSection = document.getElementById('contacto');
            if (contactSection) {
                contactSection.scrollIntoView({ behavior: 'smooth' });
            }
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