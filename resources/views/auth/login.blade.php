@extends('layouts.guest', ['title' => 'Ingreso al Sistema', 'heading' => 'Autenticación de Operadores & Clientes'])

@section('content')
<div class="card-corporate p-6 sm:p-8 border border-[#cbcfd4]/20 shadow-2xl relative">

    {{-- Flash Messages --}}
    @if(session('error'))
        <div class="mb-5 p-3.5 rounded-2xl bg-rose-950/80 border border-rose-500/40 text-rose-200 text-xs font-medium flex items-center gap-2.5 shadow-md">
            <i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-5 p-3.5 rounded-2xl bg-[#0a254e] border border-[#cbcfd4]/30 text-[#cbcfd4] text-xs font-medium flex items-center gap-2.5 shadow-md">
            <i class="fa-solid fa-circle-info text-[#f5782b] text-sm flex-shrink-0"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-5 p-3.5 rounded-2xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 text-xs font-medium flex items-center gap-2.5 shadow-md">
            <i class="fa-solid fa-circle-check text-emerald-400 text-sm flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
        @csrf

        {{-- Usuario o Email --}}
        <div>
            <label for="login" class="block text-xs font-bold uppercase tracking-wider text-[#cbcfd4] mb-1.5">
                Usuario o Correo Corporativo
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-user-shield text-[#f5782b]"></i>
                </div>
                <input 
                    id="login" 
                    name="login" 
                    type="text" 
                    autocomplete="username" 
                    required 
                    value="{{ old('login') }}" 
                    placeholder="usuario o correo@blankisol.com"
                    class="input-corporate pl-10 pr-3 py-3 text-sm @error('login') border-rose-500 @enderror"
                />
            </div>
            @error('login')
                <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#cbcfd4]">
                    Clave de Seguridad
                </label>
                <span class="text-[11px] text-slate-400">Protección SCGI</span>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-lock text-[#f5782b]"></i>
                </div>
                <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    autocomplete="current-password" 
                    required 
                    placeholder="••••••••"
                    class="input-corporate pl-10 pr-10 py-3 text-sm @error('password') border-rose-500 @enderror"
                />
                <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white" aria-label="Mostrar u ocultar contraseña">
                    <i id="eye-icon" class="fa-solid fa-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
            @enderror
        </div>

        {{-- Recordar sesión & Telemetría --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input 
                    id="remember" 
                    name="remember" 
                    type="checkbox" 
                    class="h-4 w-4 rounded bg-[#020d1c] border-slate-700 text-[#f5782b] focus:ring-[#f5782b]"
                />
                <label for="remember" class="ml-2 block text-xs text-slate-300 select-none cursor-pointer">
                    Recordar sesión
                </label>
            </div>
            <span class="text-[11px] text-[#cbcfd4] font-mono flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Servidor Activo</span>
            </span>
        </div>

        {{-- Botón Iniciar Sesión Naranja Corporativo --}}
        <div>
            <button 
                type="submit" 
                class="btn-corporate-primary w-full text-sm !py-3.5 shadow-xl flex items-center justify-center gap-2"
            >
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Iniciar Sesión</span>
            </button>
        </div>
    </form>

    {{-- Recuperación y Soporte --}}
    <div class="mt-6 pt-5 border-t border-[#cbcfd4]/15 text-center">
        <p class="text-[11px] text-slate-400 leading-relaxed">
            ¿Problemas para acceder? Contacta a la Dirección o Administrador del Sistema para restablecimiento seguro de claves.
        </p>
    </div>

</div>

<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection