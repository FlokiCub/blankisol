@extends('layouts.public')

@section('title', '500 - Interrupción Temporal del Servidor | BLANKISOL')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-16">
    <div class="card-corporate max-w-xl w-full p-8 sm:p-12 text-center border border-[#cbcfd4]/20 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-[#f5782b]/15 blur-3xl pointer-events-none"></div>

        <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-[#0a3166] to-[#051f42] border border-[#f5782b]/40 text-[#f5782b] flex items-center justify-center text-3xl mx-auto mb-6 shadow-xl shadow-[#051f42]/50 animate-float-soft">
            <i class="fa-solid fa-server"></i>
        </div>

        <span class="text-xs font-black uppercase tracking-widest text-[#f5782b] font-mono">Error 500 &bull; Sistema</span>
        <h1 class="text-3xl sm:text-4xl font-black text-white mt-2 mb-3">
            Interrupción Temporal
        </h1>
        <p class="text-sm text-slate-300 leading-relaxed mb-8">
            Hemos experimentado una contingencia técnica momentánea en nuestros servidores. Toda la información de tu carga y manifiestos se encuentra resguardada con total integridad.
        </p>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="btn-corporate-primary w-full sm:w-auto text-xs !py-3.5 !px-6 shadow-lg">
                <i class="fa-solid fa-rotate-right text-xs mr-1"></i>
                <span>Reintentar Navegación</span>
            </a>
            <a href="{{ route('home') }}#contacto" class="btn-corporate-secondary w-full sm:w-auto text-xs !py-3.5 !px-6">
                <i class="fa-solid fa-headset text-xs mr-1 text-[#f5782b]"></i>
                <span>Mesa de Ayuda Técnica</span>
            </a>
        </div>

        <div class="mt-8 pt-6 border-t border-[#cbcfd4]/10 text-xs text-slate-400">
            Nuestros ingenieros de sistemas han sido notificados automáticamente del incidente.
        </div>
    </div>
</div>
@endsection