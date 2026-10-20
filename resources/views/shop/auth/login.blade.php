<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acceso y Registro — BLANKISOL SHOP | Tienda Oficial B2C & B2B</title>
  
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

  <!-- Barra Superior Minimalista con Logo Oficial -->
  <header class="w-full bg-white border-b border-slate-200/80 py-4 px-4 sm:px-8">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <a href="{{ route('shop.index') }}" class="flex items-center gap-3.5 group">
        <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200/90 p-1 flex items-center justify-center shadow-xs group-hover:scale-105 group-hover:border-emerald-500/50 transition-all duration-300 overflow-hidden shrink-0" style="width: 40px; height: 40px; max-width: 40px; max-height: 40px;">
          <img src="{{ asset('favicon-light/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL" class="w-full h-full object-contain block" style="width: 100%; height: 100%; max-width: 100%; max-height: 100%; object-fit: contain;">
        </div>
        <div>
          <div class="flex items-center gap-1.5">
            <span class="font-extrabold text-base tracking-tight text-slate-900 group-hover:text-emerald-600 transition-colors">BLANKISOL</span>
            <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 font-black uppercase font-mono">SHOP</span>
          </div>
          <p class="text-[9px] text-slate-500 font-semibold tracking-wide">ENTREGAS A CUBA · USD</p>
        </div>
      </a>

      <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-700 transition px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200">
        <i class="fa-solid fa-arrow-left text-[11px]"></i>
        <span>Volver a la Tienda</span>
      </a>
    </div>
  </header>

  <!-- Contenedor Principal Unificado de Acceso / Registro -->
  <main class="flex-1 flex items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
    <div class="max-w-xl w-full space-y-5">

      <!-- Tarjeta Blanca de Acceso Unificado Estilo Tuambia / Jámazon -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl shadow-slate-200/60 space-y-5 relative overflow-hidden">
        
        <!-- Acento decorativo superior -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-amber-500"></div>

        <!-- Encabezado con Logo Oficial -->
        <div class="text-center space-y-2 pt-1">
          <div class="w-12 h-12 mx-auto rounded-2xl bg-white border border-slate-200/90 p-1.5 flex items-center justify-center shadow-xs overflow-hidden shrink-0" style="width: 48px; height: 48px; max-width: 48px; max-height: 48px;">
            <img src="{{ asset('favicon-light/apple-touch-icon.png') }}" alt="Logo Oficial BLANKISOL" class="w-full h-full object-contain block" style="width: 100%; height: 100%; max-width: 100%; max-height: 100%; object-fit: contain;">
          </div>
          
          <!-- Título dinámico según la pestaña activa -->
          <h1 id="auth-title-login" class="text-2xl font-black text-slate-900 tracking-tight">Iniciar Sesión</h1>
          <h1 id="auth-title-register" class="text-2xl font-black text-slate-900 tracking-tight hidden">Crear Cuenta en BLANKISOL SHOP</h1>
          
          <p id="auth-sub-login" class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
            Ingresa a tu cuenta comercial para comprar motos, paneles y equipamiento con entregas en Cuba.
          </p>
          <p id="auth-sub-register" class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed hidden">
            Únete gratis a BLANKISOL SHOP y accede a precios en USD para personas (B2C) y empresas (B2B).
          </p>
        </div>

        <!-- Notificaciones de Estado Flash -->
        @if(session('error'))
          <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-start gap-2.5 shadow-2xs">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base mt-0.5 shrink-0"></i>
            <span>{{ session('error') }}</span>
          </div>
        @endif

        @if(session('info'))
          <div class="p-3.5 rounded-2xl bg-sky-50 border border-sky-200 text-sky-800 text-xs font-semibold flex items-start gap-2.5 shadow-2xs">
            <i class="fa-solid fa-circle-info text-sky-600 text-base mt-0.5 shrink-0"></i>
            <span>{{ session('info') }}</span>
          </div>
        @endif

        @if(session('success'))
          <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-start gap-2.5 shadow-2xs">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base mt-0.5 shrink-0"></i>
            <span>{{ session('success') }}</span>
          </div>
        @endif

        @if(isset($errors) && $errors->any())
          <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-2xs">
            @foreach($errors->all() as $error)
              <div class="flex items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-xs mt-0.5 shrink-0"></i>
                <span>{{ $error }}</span>
              </div>
            @endforeach
          </div>
        @endif

        <!-- Segmented Control Limpio: Iniciar Sesión / Crear Cuenta -->
        <div class="grid grid-cols-2 p-1.5 rounded-2xl bg-slate-100 border border-slate-200 text-xs font-bold" id="auth-tabs" role="tablist">
          <button 
            type="button" 
            id="tab-btn-login" 
            onclick="switchAuthTab('login')" 
            class="py-2.5 px-3 rounded-xl transition text-center flex items-center justify-center gap-2 cursor-pointer shadow-xs bg-white text-emerald-700"
            role="tab"
            aria-selected="true"
          >
            <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
            <span>Iniciar Sesión</span>
          </button>
          
          <button 
            type="button" 
            id="tab-btn-register" 
            onclick="switchAuthTab('register')" 
            class="py-2.5 px-3 rounded-xl transition text-center flex items-center justify-center gap-2 cursor-pointer text-slate-600 hover:text-slate-900"
            role="tab"
            aria-selected="false"
          >
            <i class="fa-solid fa-user-plus text-xs"></i>
            <span>Crear Cuenta</span>
          </button>
        </div>

        <!-- Botón Universal Rápido: Google / Gmail OAuth -->
        <div>
          <a 
            href="{{ route('shop.auth.google') }}" 
            class="w-full py-3 px-4 rounded-2xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-bold text-xs transition shadow-xs flex items-center justify-center gap-3 hover:border-slate-400 group"
          >
            <!-- Google SVG Icon Oficial -->
            <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-105" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Continuar con Cuenta Gmail / Google</span>
          </a>

          <div class="relative my-4">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-[10px] uppercase tracking-wider">
              <span class="bg-white px-3 text-slate-400 font-bold" id="auth-divider-text">O con tus datos</span>
            </div>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- PESTAÑA 1: FORMULARIO DE INICIO DE SESIÓN -->
        <!-- ========================================== -->
        <div id="panel-login" class="space-y-4">
          <form method="POST" action="{{ route('shop.login.submit') }}" class="space-y-4">
            @csrf

            <!-- Campo Usuario o Email -->
            <div>
              <label for="login" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                Usuario o Correo Electrónico
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i class="fa-regular fa-envelope text-slate-500"></i>
                </div>
                <input 
                  id="login" 
                  name="login" 
                  type="text" 
                  autocomplete="username" 
                  required 
                  value="{{ old('login') }}" 
                  placeholder="usuario o correo@blankisol.com"
                  class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
                >
              </div>
            </div>

            <!-- Campo Contraseña -->
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label for="login-password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                  Contraseña
                </label>
                <a href="{{ route('shop.forgot-password') }}" class="text-[11px] text-emerald-700 hover:text-emerald-900 font-semibold underline">
                  ¿Olvidaste tu contraseña?
                </a>
              </div>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i class="fa-solid fa-lock text-slate-500"></i>
                </div>
                <input 
                  id="login-password" 
                  name="password" 
                  type="password" 
                  autocomplete="current-password" 
                  required 
                  placeholder="••••••••••••"
                  class="w-full pl-10 pr-10 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
                >
                <button 
                  type="button" 
                  onclick="togglePasswordVisibility('login-password', 'toggle-eye-login')" 
                  class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition"
                  tabindex="-1"
                  aria-label="Mostrar u ocultar contraseña"
                >
                  <i id="toggle-eye-login" class="fa-regular fa-eye text-xs"></i>
                </button>
              </div>
            </div>

            <!-- Recordar Sesión y Seguridad -->
            <div class="flex items-center justify-between pt-1">
              <label class="flex items-center gap-2 cursor-pointer">
                <input 
                  type="checkbox" 
                  name="remember" 
                  id="remember" 
                  class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500 focus:ring-offset-0"
                >
                <span class="text-xs text-slate-600 font-medium">Recordar sesión</span>
              </label>

              <span class="text-[11px] text-emerald-700 font-bold flex items-center gap-1">
                <i class="fa-solid fa-shield-halved text-xs"></i> SSL Seguro
              </span>
            </div>

            <!-- Botón de Iniciar Sesión -->
            <button 
              type="submit" 
              class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-bold text-xs uppercase tracking-wider transition shadow-md shadow-emerald-600/25 flex items-center justify-center gap-2 cursor-pointer"
            >
              <i class="fa-solid fa-arrow-right-to-bracket"></i>
              <span>Iniciar Sesión</span>
            </button>
          </form>
        </div>

        <!-- ========================================== -->
        <!-- PESTAÑA 2: FORMULARIO DE REGISTRO DIRECTO -->
        <!-- ========================================== -->
        <div id="panel-register" class="space-y-4 hidden">
          <form method="POST" action="{{ route('shop.register.submit') }}" class="space-y-4">
            @csrf

            <!-- Selector de Tipo de Cliente (B2C / B2B) -->
            <div class="p-1 rounded-2xl bg-slate-100 border border-slate-200 flex text-xs font-bold" id="customer-type-toggle">
              <label class="flex-1 text-center py-2 rounded-xl cursor-pointer transition select-none has-[:checked]:bg-white has-[:checked]:text-emerald-700 has-[:checked]:shadow-xs text-slate-600">
                <input type="radio" name="customer_type" value="individual" class="sr-only" checked onchange="toggleCompanyFields()">
                <i class="fa-regular fa-user mr-1.5"></i> Persona Natural (B2C)
              </label>
              <label class="flex-1 text-center py-2 rounded-xl cursor-pointer transition select-none has-[:checked]:bg-white has-[:checked]:text-emerald-700 has-[:checked]:shadow-xs text-slate-600">
                <input type="radio" name="customer_type" value="business" class="sr-only" {{ old('customer_type') === 'business' ? 'checked' : '' }} onchange="toggleCompanyFields()">
                <i class="fa-solid fa-building mr-1.5"></i> Empresa / Negocio (B2B)
              </label>
            </div>

            <!-- Nombre Completo -->
            <div>
              <label for="reg-name" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                Nombre Completo <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i class="fa-regular fa-user text-slate-500"></i>
                </div>
                <input 
                  id="reg-name" 
                  name="name" 
                  type="text" 
                  required 
                  value="{{ old('name') }}" 
                  placeholder="Nombre y Apellidos"
                  class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
                >
              </div>
            </div>

            <!-- Correo Electrónico -->
            <div>
              <label for="reg-email" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                Correo Electrónico <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i class="fa-regular fa-envelope text-slate-500"></i>
                </div>
                <input 
                  id="reg-email" 
                  name="email" 
                  type="email" 
                  autocomplete="email" 
                  required 
                  value="{{ old('email') }}" 
                  placeholder="tu.correo@ejemplo.com"
                  class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
                >
              </div>
            </div>

            <!-- Campos Condicionales B2B -->
            <div id="company-fields" class="{{ old('customer_type') === 'business' ? '' : 'hidden' }} space-y-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
              <div>
                <label for="company_name" class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">
                  Razón Social / Nombre de la Empresa
                </label>
                <input 
                  id="company_name" 
                  name="company_name" 
                  type="text" 
                  value="{{ old('company_name') }}" 
                  placeholder="Ej: Soluciones Caribe S.A."
                  class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
              </div>
              <div>
                <label for="tax_id" class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">
                  Identificación Fiscal / NIT / Registro
                </label>
                <input 
                  id="tax_id" 
                  name="tax_id" 
                  type="text" 
                  value="{{ old('tax_id') }}" 
                  placeholder="Ej: NIT-CU-123456"
                  class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
              </div>
            </div>

            <!-- Teléfono de Contacto -->
            <div>
              <label for="phone" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                Teléfono de Contacto / WhatsApp
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i class="fa-solid fa-phone text-slate-500"></i>
                </div>
                <input 
                  id="phone" 
                  name="phone" 
                  type="tel" 
                  value="{{ old('phone') }}" 
                  placeholder="+53 5 XXX XXXX"
                  class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
                >
              </div>
            </div>

            <!-- Contraseña y Confirmación -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label for="reg-password" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                  Contraseña <span class="text-rose-500">*</span>
                </label>
                <input 
                  id="reg-password" 
                  name="password" 
                  type="password" 
                  required 
                  placeholder="Mínimo 8 caracteres"
                  class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
                >
              </div>
              <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                  Confirmar <span class="text-rose-500">*</span>
                </label>
                <input 
                  id="password_confirmation" 
                  name="password_confirmation" 
                  type="password" 
                  required 
                  placeholder="Repite la clave"
                  class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"
                >
              </div>
            </div>

            <!-- Términos y Condiciones -->
            <div class="pt-1">
              <label class="flex items-start gap-2.5 cursor-pointer">
                <input 
                  type="checkbox" 
                  name="terms" 
                  id="terms" 
                  required 
                  class="w-4 h-4 mt-0.5 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500"
                >
                <span class="text-xs text-slate-600 leading-tight">
                  Acepto los términos de compra, entregas y despacho en Cuba con liquidación en USD de BLANKISOL SURL.
                </span>
              </label>
            </div>

            <!-- Botón de Registro -->
            <button 
              type="submit" 
              class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-bold text-xs uppercase tracking-wider transition shadow-md shadow-emerald-600/25 flex items-center justify-center gap-2 cursor-pointer"
            >
              <i class="fa-solid fa-user-plus"></i>
              <span>Crear Cuenta en BLANKISOL SHOP</span>
            </button>
          </form>
        </div>

      </div>

    </div>
  </main>

  <!-- Footer Minimalista con Logo Oficial -->
  <footer class="w-full bg-white border-t border-slate-200/80 py-4 px-4 text-center text-xs text-slate-400">
    <p>© {{ date('Y') }} BLANKISOL SURL · Plataforma Comercial Segura con Entregas en Cuba · USD</p>
  </footer>

  <script>
    function switchAuthTab(mode) {
      const isLogin = mode === 'login';
      
      const tabBtnLogin = document.getElementById('tab-btn-login');
      const tabBtnRegister = document.getElementById('tab-btn-register');
      const panelLogin = document.getElementById('panel-login');
      const panelRegister = document.getElementById('panel-register');
      
      const titleLogin = document.getElementById('auth-title-login');
      const titleRegister = document.getElementById('auth-title-register');
      const subLogin = document.getElementById('auth-sub-login');
      const subRegister = document.getElementById('auth-sub-register');
      const dividerText = document.getElementById('auth-divider-text');

      if (isLogin) {
        // Estilos del botón activo
        tabBtnLogin.className = "py-2.5 px-3 rounded-xl transition text-center flex items-center justify-center gap-2 cursor-pointer shadow-xs bg-white text-emerald-700";
        tabBtnLogin.setAttribute('aria-selected', 'true');
        tabBtnRegister.className = "py-2.5 px-3 rounded-xl transition text-center flex items-center justify-center gap-2 cursor-pointer text-slate-600 hover:text-slate-900";
        tabBtnRegister.setAttribute('aria-selected', 'false');

        // Paneles
        panelLogin.classList.remove('hidden');
        panelRegister.classList.add('hidden');

        // Títulos
        titleLogin.classList.remove('hidden');
        titleRegister.classList.add('hidden');
        subLogin.classList.remove('hidden');
        subRegister.classList.add('hidden');
        if (dividerText) dividerText.textContent = "O con tu usuario o email";
      } else {
        // Estilos del botón activo
        tabBtnRegister.className = "py-2.5 px-3 rounded-xl transition text-center flex items-center justify-center gap-2 cursor-pointer shadow-xs bg-white text-emerald-700";
        tabBtnRegister.setAttribute('aria-selected', 'true');
        tabBtnLogin.className = "py-2.5 px-3 rounded-xl transition text-center flex items-center justify-center gap-2 cursor-pointer text-slate-600 hover:text-slate-900";
        tabBtnLogin.setAttribute('aria-selected', 'false');

        // Paneles
        panelRegister.classList.remove('hidden');
        panelLogin.classList.add('hidden');

        // Títulos
        titleRegister.classList.remove('hidden');
        titleLogin.classList.add('hidden');
        subRegister.classList.remove('hidden');
        subLogin.classList.add('hidden');
        if (dividerText) dividerText.textContent = "O completa tus datos";
      }
    }

    function toggleCompanyFields() {
      const selected = document.querySelector('input[name="customer_type"]:checked')?.value;
      const box = document.getElementById('company-fields');
      if (box) {
        if (selected === 'business') {
          box.classList.remove('hidden');
        } else {
          box.classList.add('hidden');
        }
      }
    }

    function togglePasswordVisibility(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      if (input && icon) {
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
    }

    // Inicializar pestaña según hash, parámetro o errores de registro
    document.addEventListener('DOMContentLoaded', function () {
      const urlParams = new URLSearchParams(window.location.search);
      const hasRegisterErrors = @json($errors->has('name') || $errors->has('password_confirmation') || old('customer_type') !== null);
      const initialTabParam = @json($initialTab ?? 'login');
      const hash = window.location.hash;

      if (initialTabParam === 'register' || hash === '#register' || urlParams.get('tab') === 'register' || hasRegisterErrors) {
        switchAuthTab('register');
      } else {
        switchAuthTab('login');
      }
    });
  </script>
</body>
</html>
