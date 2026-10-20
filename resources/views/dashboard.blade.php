@extends('layouts.app', ['title' => 'Mi Perfil & Panel'])

@section('page_title', 'Panel de Usuario')

@section('content')
<div class="space-y-6">

  <!-- Welcome banner con identidad corporativa BLANKISOL -->
  <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-[#020d1c] via-[#051f42] to-[#0a254e] border border-[#cbcfd4]/15 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-2xl relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-[#f5782b]/10 blur-2xl pointer-events-none"></div>

    <div class="flex items-center gap-4 relative z-10">
      <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#0a3166] to-[#051f42] border border-[#f5782b]/40 text-[#f5782b] flex items-center justify-center font-bold text-xl shadow-lg font-mono">
        {{ strtoupper(substr($user->name, 0, 2)) }}
      </div>
      <div>
        <div class="flex items-center gap-2">
          <h3 class="text-xl sm:text-2xl font-black text-white">{{ $user->name }}</h3>
          @if($user->isAdmin())
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#f5782b]/20 text-[#ff934f] border border-[#f5782b]/30">
              Director / Admin
            </span>
          @else
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
              {{ $user->role?->label() ?? 'Operador' }}
            </span>
          @endif
        </div>
        <p class="text-xs text-[#cbcfd4] mt-0.5 font-mono">{{ $user->email }}</p>
      </div>
    </div>
  </div>

  @if($user->isShopAdmin() || $user->isDirector())
    <!-- Módulo de Control de la Tienda Virtual (BLANKISOL SHOP) -->
    <div class="card-corporate p-6 sm:p-8 border border-emerald-500/40 bg-gradient-to-br from-[#0a254e] via-[#051f42] to-[#020d1c] shadow-2xl space-y-4">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-emerald-500/30">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 p-1 flex items-center justify-center shadow-md shrink-0">
            <img src="{{ asset('favicon-light/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL" class="w-full h-full object-contain">
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h4 class="text-base font-extrabold text-white">BLANKISOL SHOP — Administración de Tienda</h4>
              <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30 uppercase font-mono">B2C & B2B · USD</span>
            </div>
            <p class="text-xs text-slate-300 mt-0.5">Control directo de catálogo, productos en stock vs bajo pedido, cupones y órdenes comerciales.</p>
          </div>
        </div>

        <a href="{{ route('shop.admin.dashboard') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition shadow-lg shadow-emerald-950/50 flex items-center gap-2 shrink-0">
          <i class="fa-solid fa-gauge-high"></i>
          <span>Ir a la Administración</span>
        </a>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
        <a href="{{ route('shop.admin.products.index') }}" class="p-3.5 rounded-xl bg-[#020d1c]/90 hover:bg-[#0a3166]/80 border border-slate-700 hover:border-emerald-500/50 transition group flex flex-col justify-between">
          <div class="flex items-center justify-between text-slate-400 group-hover:text-emerald-400">
            <span class="text-xs font-bold text-slate-200">Productos</span>
            <i class="fa-solid fa-box-open text-sm"></i>
          </div>
          <span class="text-[11px] text-slate-400 mt-2">Catálogo & Stock</span>
        </a>

        <a href="{{ route('shop.admin.categories.index') }}" class="p-3.5 rounded-xl bg-[#020d1c]/90 hover:bg-[#0a3166]/80 border border-slate-700 hover:border-emerald-500/50 transition group flex flex-col justify-between">
          <div class="flex items-center justify-between text-slate-400 group-hover:text-emerald-400">
            <span class="text-xs font-bold text-slate-200">Categorías</span>
            <i class="fa-solid fa-tags text-sm"></i>
          </div>
          <span class="text-[11px] text-slate-400 mt-2">Familias de Equipos</span>
        </a>

        <a href="{{ route('shop.admin.orders.index') }}" class="p-3.5 rounded-xl bg-[#020d1c]/90 hover:bg-[#0a3166]/80 border border-slate-700 hover:border-emerald-500/50 transition group flex flex-col justify-between">
          <div class="flex items-center justify-between text-slate-400 group-hover:text-emerald-400">
            <span class="text-xs font-bold text-slate-200">Pedidos</span>
            <i class="fa-solid fa-receipt text-sm"></i>
          </div>
          <span class="text-[11px] text-slate-400 mt-2">Ventas & Cobranza</span>
        </a>

        <a href="{{ route('shop.index') }}" target="_blank" class="p-3.5 rounded-xl bg-[#020d1c]/90 hover:bg-slate-800 border border-slate-700 hover:border-emerald-500/50 transition group flex flex-col justify-between">
          <div class="flex items-center justify-between text-slate-400 group-hover:text-emerald-400">
            <span class="text-xs font-bold text-slate-200">Ver Tienda</span>
            <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
          </div>
          <span class="text-[11px] text-slate-400 mt-2">Tienda Pública</span>
        </a>
      </div>
    </div>
  @endif

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    
    <!-- Account Information Card -->
    <div class="card-corporate p-6 sm:p-8 border border-[#cbcfd4]/15 shadow-xl space-y-4">
      <h4 class="text-sm font-bold text-white flex items-center gap-2 pb-3 border-b border-[#cbcfd4]/15">
        <i class="fa-solid fa-id-badge text-[#f5782b]"></i>
        <span>Información de tu Cuenta</span>
      </h4>

      <div class="space-y-3 text-xs">
        <div class="flex items-center justify-between p-3 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/10">
          <span class="text-[#cbcfd4]">Nombre de Usuario:</span>
          <span class="font-mono font-bold text-white">{{ $user->username ?? 'No especificado' }}</span>
        </div>

        <div class="flex items-center justify-between p-3 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/10">
          <span class="text-[#cbcfd4]">Correo Electrónico:</span>
          <span class="font-mono font-bold text-white">{{ $user->email }}</span>
        </div>

        <div class="flex items-center justify-between p-3 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/10">
          <span class="text-[#cbcfd4]">Rol Asignado:</span>
          <span class="font-bold text-[#ff934f]">{{ $user->role?->label() ?? ($user->isAdmin() ? 'Director General' : 'Operador') }}</span>
        </div>

        <div class="flex items-center justify-between p-3 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/10">
          <span class="text-[#cbcfd4]">Estado de Cuenta:</span>
          <span class="inline-flex items-center gap-1.5 font-bold text-emerald-400">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Activa
          </span>
        </div>

        <div class="flex items-center justify-between p-3 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/10">
          <span class="text-[#cbcfd4]">Miembro desde:</span>
          <span class="text-slate-300 font-mono">{{ $user->created_at->format('d/m/Y') }}</span>
        </div>
      </div>
    </div>

    <!-- Permissions and Info Card -->
    <div class="card-corporate p-6 sm:p-8 border border-[#cbcfd4]/15 shadow-xl space-y-4">
      <h4 class="text-sm font-bold text-white flex items-center gap-2 pb-3 border-b border-[#cbcfd4]/15">
        <i class="fa-solid fa-shield-halved text-[#f5782b]"></i>
        <span>Permisos y Seguridad Operativa</span>
      </h4>

      <ul class="space-y-3 text-xs text-slate-300">
        <li class="flex items-center gap-3 p-3 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/10">
          <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
          <span>Sesión autenticada bajo matriz de control de acceso RBAC</span>
        </li>
        <li class="flex items-center gap-3 p-3 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/10">
          <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
          <span>Protección contra vulnerabilidades CSRF y transacciones auditables</span>
        </li>
        @if($user->isAdmin())
          <li class="flex items-center gap-3 p-3 rounded-xl bg-[#0a254e]/80 border border-[#f5782b]/30 text-slate-100">
            <i class="fa-solid fa-crown text-[#f5782b] text-sm"></i>
            <span>Permiso de Director: Supervisión integral, administración de usuarios y estados</span>
          </li>
        @else
          <li class="flex items-center gap-3 p-3 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/10 text-[#cbcfd4]">
            <i class="fa-solid fa-user-lock text-[#f5782b] text-sm"></i>
            <span>Acceso segmentado exclusivamente a los módulos de tu perfil funcional</span>
          </li>
        @endif
      </ul>

      <!-- Quick Action: Ver portal público -->
      <div class="pt-2">
        <a href="{{ route('home') }}" target="_blank" class="w-full btn-corporate-secondary text-xs !py-3 flex items-center justify-center gap-2">
          <i class="fa-solid fa-globe text-[#f5782b]"></i>
          <span>Visitar Portal Público BLANKISOL</span>
        </a>
      </div>
    </div>

  </div>

</div>
@endsection