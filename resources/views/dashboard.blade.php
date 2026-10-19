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