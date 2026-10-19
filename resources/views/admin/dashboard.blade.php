@extends('layouts.app', ['title' => 'Dashboard Administrativo'])

@section('page_title', 'Dashboard de Administración')

@section('content')
<div class="space-y-6">

  <!-- Welcome banner con estilo BLANKISOL -->
  <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-[#020d1c] via-[#051f42] to-[#0a254e] border border-[#cbcfd4]/15 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-2xl relative overflow-hidden">
    <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-[#f5782b]/10 blur-3xl pointer-events-none"></div>

    <div class="relative z-10">
      <span class="px-3 py-1 rounded-full bg-[#f5782b]/15 text-[#ff934f] text-xs font-bold uppercase tracking-wider border border-[#f5782b]/30">
        Panel Ejecutivo &bull; Torre de Control
      </span>
      <h3 class="text-2xl sm:text-3xl font-black text-white mt-2.5">
        Hola, {{ auth()->user()->name }}
      </h3>
      <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl">
        Resumen operativo de usuarios registrados, roles del sistema SCGI y estado de infraestructura.
      </p>
    </div>
    <div class="flex flex-wrap items-center gap-3 relative z-10">
      <a href="{{ route('admin.users.create') }}" class="btn-corporate-primary text-xs !py-3 !px-5 shadow-lg">
        <i class="fa-solid fa-user-plus mr-1"></i>
        <span>Crear Usuario</span>
      </a>
      <a href="{{ route('admin.users.index') }}" class="btn-corporate-secondary text-xs !py-3 !px-5">
        <i class="fa-solid fa-users mr-1 text-[#f5782b]"></i>
        <span>Ver Usuarios</span>
      </a>
    </div>
  </div>

  <!-- Metric Cards Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    
    <!-- Total Users -->
    <div class="card-corporate p-5 border border-[#cbcfd4]/15 flex items-center justify-between shadow-sm hover:border-[#f5782b]/40 transition-all">
      <div>
        <p class="text-xs font-semibold text-[#cbcfd4] uppercase tracking-wider">Total Usuarios</p>
        <h4 class="text-3xl font-black text-white mt-1">{{ $totalUsers }}</h4>
        <span class="text-[11px] text-slate-400 mt-0.5 block font-mono">PostgreSQL SCGI</span>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-[#0a254e] border border-[#cbcfd4]/20 text-[#f5782b] flex items-center justify-center text-xl shadow-md">
        <i class="fa-solid fa-users"></i>
      </div>
    </div>

    <!-- Admins / Directores -->
    <div class="card-corporate p-5 border border-[#cbcfd4]/15 flex items-center justify-between shadow-sm hover:border-[#f5782b]/40 transition-all">
      <div>
        <p class="text-xs font-semibold text-[#cbcfd4] uppercase tracking-wider">Directores</p>
        <h4 class="text-3xl font-black text-[#ff934f] mt-1">{{ $adminCount }}</h4>
        <span class="text-[11px] text-slate-400 mt-0.5 block">Rol Director (Omni)</span>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-[#f5782b]/15 border border-[#f5782b]/30 text-[#f5782b] flex items-center justify-center text-xl shadow-md">
        <i class="fa-solid fa-user-shield"></i>
      </div>
    </div>

    <!-- Standard Users / Operadores -->
    <div class="card-corporate p-5 border border-[#cbcfd4]/15 flex items-center justify-between shadow-sm hover:border-[#f5782b]/40 transition-all">
      <div>
        <p class="text-xs font-semibold text-[#cbcfd4] uppercase tracking-wider">Operadores</p>
        <h4 class="text-3xl font-black text-white mt-1">{{ $standardUserCount }}</h4>
        <span class="text-[11px] text-slate-400 mt-0.5 block">Perfiles Operativos</span>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-[#0a254e] border border-[#cbcfd4]/20 text-[#cbcfd4] flex items-center justify-center text-xl shadow-md">
        <i class="fa-solid fa-user-gear"></i>
      </div>
    </div>

    <!-- Active Accounts -->
    <div class="card-corporate p-5 border border-[#cbcfd4]/15 flex items-center justify-between shadow-sm hover:border-[#f5782b]/40 transition-all">
      <div>
        <p class="text-xs font-semibold text-[#cbcfd4] uppercase tracking-wider">Cuentas Habilitadas</p>
        <h4 class="text-3xl font-black text-emerald-400 mt-1">{{ $activeUserCount }}</h4>
        <span class="text-[11px] text-emerald-400/80 mt-0.5 block flex items-center gap-1 font-mono">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Acceso Activo
        </span>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl shadow-md">
        <i class="fa-solid fa-circle-check"></i>
      </div>
    </div>

  </div>

  <!-- Recent Users Table & System Details -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Recent Users Table (2 cols) -->
    <div class="lg:col-span-2 card-corporate border border-[#cbcfd4]/15 rounded-3xl overflow-hidden shadow-xl">
      <div class="p-6 border-b border-[#cbcfd4]/15 flex items-center justify-between">
        <div>
          <h4 class="text-base font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-users text-[#f5782b]"></i>
            <span>Usuarios Recientes</span>
          </h4>
          <p class="text-xs text-slate-400">Últimos operadores y directores registrados</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-[#ff934f] hover:text-white flex items-center gap-1">
          <span>Ver todos</span>
          <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-[#020d1c] text-[#cbcfd4] uppercase tracking-wider font-semibold border-b border-[#cbcfd4]/15">
            <tr>
              <th class="px-5 py-3.5">Usuario</th>
              <th class="px-5 py-3.5">Rol Funcional</th>
              <th class="px-5 py-3.5">Estado</th>
              <th class="px-5 py-3.5 text-right">Acción</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#cbcfd4]/10 bg-[#020d1c]/40">
            @forelse($recentUsers as $user)
              <tr class="hover:bg-[#0a254e]/50 transition-colors">
                <td class="px-5 py-3.5">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#051f42] border border-[#cbcfd4]/20 flex items-center justify-center font-bold text-xs text-[#ff934f] font-mono">
                      {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                      <p class="font-bold text-white">{{ $user->name }}</p>
                      <p class="text-[11px] text-slate-400 font-mono">{{ $user->email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-3.5">
                  @if($user->isAdmin())
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#f5782b]/15 text-[#ff934f] border border-[#f5782b]/30">
                      <i class="fa-solid fa-crown text-[9px]"></i> Director
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#0a254e] text-slate-200 border border-[#cbcfd4]/20">
                      <i class="fa-solid fa-user text-[9px] text-[#f5782b]"></i> {{ $user->role?->label() ?? 'Operador' }}
                    </span>
                  @endif
                </td>
                <td class="px-5 py-3.5">
                  @if($user->is_active)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Activo
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                      <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Inactivo
                    </span>
                  @endif
                </td>
                <td class="px-5 py-3.5 text-right">
                  <a href="{{ route('admin.users.edit', $user) }}" class="px-3 py-1.5 rounded-xl bg-[#0a254e] hover:bg-[#051f42] border border-[#cbcfd4]/20 text-slate-200 hover:text-white font-semibold transition-all">
                    Editar
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-5 py-8 text-center text-slate-500">
                  No hay usuarios registrados.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- System info card (1 col) -->
    <div class="space-y-4">
      <div class="card-corporate p-6 border border-[#cbcfd4]/15 rounded-3xl shadow-xl space-y-4">
        <h4 class="text-sm font-bold text-white flex items-center gap-2 pb-3 border-b border-[#cbcfd4]/15">
          <i class="fa-solid fa-server text-[#f5782b]"></i>
          <span>Entorno de Ejecución</span>
        </h4>
        
        <div class="space-y-2.5 text-xs">
          <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/10">
            <span class="text-[#cbcfd4]">Framework:</span>
            <span class="font-bold text-white">Laravel {{ app()->version() }}</span>
          </div>
          <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/10">
            <span class="text-[#cbcfd4]">Versión PHP:</span>
            <span class="font-mono font-bold text-[#ff934f]">PHP {{ phpversion() }}</span>
          </div>
          <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/10">
            <span class="text-[#cbcfd4]">Base de Datos:</span>
            <span class="font-bold text-emerald-400 font-mono">PostgreSQL 16 (scgi_db)</span>
          </div>
          <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#020d1c]/80 border border-[#cbcfd4]/10">
            <span class="text-[#cbcfd4]">Servidor / Entorno:</span>
            <span class="font-bold text-white">XAMPP Apache + SCGI</span>
          </div>
        </div>
      </div>

      <!-- Quick Role Info -->
      <div class="card-corporate p-6 border border-[#cbcfd4]/15 rounded-3xl shadow-xl">
        <h4 class="text-sm font-bold text-white flex items-center gap-2 mb-3 pb-3 border-b border-[#cbcfd4]/15">
          <i class="fa-solid fa-key text-[#f5782b]"></i>
          <span>Matriz de Roles (RBAC 7)</span>
        </h4>
        <ul class="text-xs space-y-2 text-slate-300">
          <li class="flex items-start gap-2">
            <i class="fa-solid fa-check text-[#f5782b] mt-0.5"></i>
            <span><strong>Director:</strong> Omni-acceso ejecutivo y configuración del sistema.</span>
          </li>
          <li class="flex items-start gap-2">
            <i class="fa-solid fa-check text-emerald-400 mt-0.5"></i>
            <span><strong>Operadores:</strong> Acceso funcional segmentado (Aduana, Despacho, Chofer, Cajero, Auditor, PVP).</span>
          </li>
        </ul>
      </div>
    </div>

  </div>

</div>
@endsection