@extends('layouts.public')

@section('title', '403 - Acceso Restringido | BLANKISOL')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-16">
    <div class="card-corporate max-w-xl w-full p-8 sm:p-12 text-center border border-[#cbcfd4]/20 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-amber-500/10 blur-3xl pointer-events-none"></div>

        <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-[#0a3166] to-[#051f42] border border-amber-500/40 text-amber-400 flex items-center justify-center text-3xl mx-auto mb-6 shadow-xl shadow-[#051f42]/50 animate-float-soft">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <span class="text-xs font-black uppercase tracking-widest text-amber-400 font-mono">Error 403 &bull; RBAC</span>
        <h1 class="text-3xl sm:text-4xl font-black text-white mt-2 mb-3">
            Acceso Restringido
        </h1>
        <p class="text-sm text-slate-300 leading-relaxed mb-8">
            Tu perfil de operador no dispone de las autorizaciones requeridas para visualizar este módulo o ejecutar esta acción en la plataforma SCGI.
        </p>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-corporate-primary w-full sm:w-auto text-xs !py-3.5 !px-6 shadow-lg">
                    <i class="fa-solid fa-gauge text-xs mr-1"></i>
                    <span>Ir a Mi Puesto Operativo</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-corporate-primary w-full sm:w-auto text-xs !py-3.5 !px-6 shadow-lg">
                    <i class="fa-solid fa-lock text-xs mr-1"></i>
                    <span>Iniciar Sesión con Credenciales</span>
                </a>
            @endauth
            <a href="{{ route('home') }}" class="btn-corporate-secondary w-full sm:w-auto text-xs !py-3.5 !px-6">
                <i class="fa-solid fa-house text-xs mr-1 text-[#f5782b]"></i>
                <span>Portal Principal</span>
            </a>
        </div>

        <div class="mt-8 pt-6 border-t border-[#cbcfd4]/10 text-xs text-slate-400">
            Para solicitar ampliación de permisos, contacta al <span class="text-white font-medium">Director General</span> o Administrador.
        </div>
    </div>
</div>
@endsection