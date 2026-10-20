<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Definir Nueva Contraseña — BLANKISOL SHOP | Tienda Oficial B2C & B2B</title>
  
  <!-- Favicon Oficial para Fondos Claros -->
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon-light/favicon.ico') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-light/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-light/favicon-16x16.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon-light/apple-touch-icon.png') }}">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- FontAwesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Google Fonts: Plus Jakarta Sans, Inter, JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Estilos Estilo Tuambia Oficial -->
  <link rel="stylesheet" href="{{ asset('css/shop.css') }}">

  <style>
    body {
      font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
      background-color: #F8F9FA;
    }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
  </style>
</head>
<body class="min-h-full flex flex-col justify-between selection:bg-emerald-500 selection:text-white">

  <!-- Barra Superior Minimalista con Logo Oficial para Fondos Claros -->
  <header class="w-full bg-white border-b border-slate-200/80 py-4 px-4 sm:px-8">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <a href="{{ route('shop.index') }}" class="flex items-center gap-3.5 group">
        <div class="w-11 h-11 rounded-2xl bg-white border border-slate-200/90 p-1 flex items-center justify-center shadow-xs group-hover:scale-105 group-hover:border-emerald-500/50 transition-all duration-300 overflow-hidden">
          <img src="{{ asset('favicon-light/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL" class="w-full h-full object-contain">
        </div>
        <div>
          <div class="flex items-center gap-1.5">
            <span class="font-extrabold text-base tracking-tight text-slate-900 group-hover:text-emerald-600 transition-colors">BLANKISOL</span>
            <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 font-black uppercase font-mono">SHOP</span>
          </div>
          <p class="text-[9px] text-slate-500 font-semibold tracking-wide">ENTREGAS A CUBA · USD</p>
        </div>
      </a>

      <a href="{{ route('shop.login') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-700 transition px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200">
        <i class="fa-solid fa-arrow-left text-[11px]"></i>
        <span>Volver a Iniciar Sesión</span>
      </a>
    </div>
  </header>

  <!-- Contenedor Principal -->
  <main class="flex-1 flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6">

      <!-- Tarjeta Blanca Estilo Tuambia -->
      <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl shadow-slate-200/60 space-y-6 relative overflow-hidden">
        
        <!-- Acento decorativo sutil superior -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-amber-500"></div>

        <!-- Encabezado de la Tarjeta con Logo Oficial -->
        <div class="text-center space-y-2">
          <div class="w-12 h-12 mx-auto rounded-2xl bg-white border border-slate-200/90 p-1.5 flex items-center justify-center shadow-xs overflow-hidden shrink-0" style="width: 48px; height: 48px; max-width: 48px; max-height: 48px;">
            <img src="{{ asset('favicon-light/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL" class="w-full h-full object-contain block" style="width: 100%; height: 100%; max-width: 100%; max-height: 100%; object-fit: contain;">
          </div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight">Establecer Nueva Contraseña</h1>
          <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
            Ingresa tu nueva clave de acceso seguro para tu cuenta comercial de BLANKISOL SHOP.
          </p>
        </div>

        <!-- Notificaciones de Alerta Flash -->
        @if(session('error'))
          <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-start gap-2.5 shadow-2xs">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base mt-0.5 shrink-0"></i>
            <span>{{ session('error') }}</span>
          </div>
        @endif

        @if($errors->any())
          <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-2xs">
            @foreach($errors->all() as $error)
              <div class="flex items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-xs mt-0.5 shrink-0"></i>
                <span>{{ $error }}</span>
              </div>
            @endforeach
          </div>
        @endif

        <!-- Formulario -->
        <form method="POST" action="{{ route('shop.reset-password.submit') }}" class="space-y-4">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
              Correo Electrónico
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fa-regular fa-envelope text-slate-500"></i>
              </div>
              <input 
                id="email" 
                name="email" 
                type="email" 
                required 
                value="{{ old('email', $email ?? '') }}" 
                class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
              >
            </div>
          </div>

          <div>
            <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
              Nueva Contraseña
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-lock text-slate-500"></i>
              </div>
              <input 
                id="password" 
                name="password" 
                type="password" 
                required 
                placeholder="Mínimo 8 caracteres"
                class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
              >
            </div>
          </div>

          <div>
            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
              Confirmar Nueva Contraseña
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-shield-check text-slate-500"></i>
              </div>
              <input 
                id="password_confirmation" 
                name="password_confirmation" 
                type="password" 
                required 
                placeholder="Repite tu nueva contraseña"
                class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
              >
            </div>
          </div>

          <button 
            type="submit" 
            class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-bold text-xs uppercase tracking-wider transition shadow-md shadow-emerald-600/25 flex items-center justify-center gap-2"
          >
            <i class="fa-solid fa-key"></i>
            <span>Actualizar Contraseña</span>
          </button>
        </form>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
          <a href="{{ route('shop.login') }}" class="font-bold text-emerald-700 hover:text-emerald-900 underline">
            &larr; Volver a Iniciar Sesión
          </a>
        </div>

      </div>

    </div>
  </main>

  <!-- Footer Minimalista con Logo Oficial -->
  <footer class="w-full bg-white border-t border-slate-200/80 py-4 px-4 text-center text-xs text-slate-400">
    <p>© {{ date('Y') }} BLANKISOL SURL · Plataforma Comercial Segura con Entregas en Cuba · USD</p>
  </footer>

</body>
</html>
