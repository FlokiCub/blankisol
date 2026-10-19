@extends('layouts.app', ['title' => 'Carga Masiva de Manifiestos'])

@section('page_title', 'Importación Masiva de Manifiestos — 22 Columnas Oficiales')

@section('content')
<div class="space-y-8 animate-fade-in">

  <!-- Header Banner -->
  <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-emerald-950/80 via-slate-900 to-slate-900 border border-emerald-500/20 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold mb-2 border border-emerald-500/30">
        <i class="fa-solid fa-file-excel text-emerald-400"></i>
        <span>Procesador Inteligente de Manifiestos Portuarios</span>
      </div>
      <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Carga de Manifiestos Excel / CSV</h2>
      <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-2xl">
        Importa lotes completos de contenedores, motos eléctricas, sistemas solares y datos de consignatarios con validación estricta de 22 columnas y asignación provincial automática.
      </p>
    </div>

    <a href="{{ route('manifests.import.template') }}" class="inline-flex items-center gap-2 px-5 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-extrabold transition-all shadow-xl shadow-emerald-950/50 active:scale-98">
      <i class="fa-solid fa-download text-sm"></i>
      <span>Descargar Plantilla Oficial (.xlsx)</span>
    </a>
  </div>

  @if(session('import_errors') && count(session('import_errors')) > 0)
    <div class="p-5 rounded-3xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs shadow-xl">
      <h4 class="font-bold text-sm text-rose-400 mb-2 flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>Se detectaron incidencias en las siguientes filas:</span>
      </h4>
      <ul class="list-disc list-inside space-y-1 font-mono">
        @foreach(session('import_errors') as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Upload Area Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Upload Card -->
    <div class="lg:col-span-2 glass-card p-6 sm:p-8 rounded-3xl shadow-xl space-y-6">
      <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <h3 class="text-sm font-bold text-white flex items-center gap-2">
          <i class="fa-solid fa-cloud-arrow-up text-brand-400"></i>
          <span>Subir Archivo de Manifiesto</span>
        </h3>
        <span class="text-[11px] font-mono text-slate-400">XLSX, XLS, CSV (hasta 10MB)</span>
      </div>

      <form method="POST" action="{{ route('manifests.import.preview') }}" enctype="multipart/form-data" class="space-y-6" id="manifest-form">
        @csrf

        <!-- Dropzone Container -->
        <label for="manifest_file" id="dropzone-box" class="flex flex-col items-center justify-center border-2 border-dashed border-slate-700 hover:border-brand-500 bg-slate-950/60 hover:bg-slate-900/80 rounded-3xl p-10 cursor-pointer transition-all group">
          <div class="w-16 h-16 rounded-2xl bg-brand-500/10 border border-brand-500/20 text-brand-400 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform mb-3 shadow-inner">
            <i class="fa-solid fa-file-excel"></i>
          </div>
          <span class="text-sm sm:text-base font-bold text-white text-center">Haz clic para examinar o arrastra el archivo aquí</span>
          <span class="text-xs text-slate-400 mt-1">Soporta libros de Excel con el orden estándar de 22 columnas</span>
          <input type="file" id="manifest_file" name="file" accept=".xlsx,.xls,.csv" required class="hidden" onchange="handleFileSelected(this)">
          <div id="filename-display" class="mt-4 px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-mono font-bold text-emerald-400 hidden"></div>
        </label>

        <!-- Options Checkbox -->
        <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
          <label class="flex items-start gap-3 text-xs text-slate-200 cursor-pointer select-none">
            <input type="checkbox" name="send_whatsapp" value="1" class="mt-0.5 w-4 h-4 rounded bg-slate-900 border-slate-700 text-brand-600 focus:ring-brand-500">
            <div>
              <span class="font-bold text-white flex items-center gap-1.5">
                <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                <span>Disparar WhatsApp Automático de Arribo a Cuba</span>
              </span>
              <p class="text-slate-400 text-[11px] mt-0.5">Envía el PIN de rastreo y bienvenida a los números telefónicos registrados en el manifiesto.</p>
            </div>
          </label>
        </div>

        <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-brand-600 via-brand-500 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-extrabold text-sm shadow-xl shadow-brand-950/50 active:scale-98 transition-all flex items-center justify-center gap-2">
          <i class="fa-solid fa-magnifying-glass-chart"></i>
          <span>Subir y Previsualizar Cotejo de Columnas</span>
        </button>
      </form>
    </div>

    <!-- Instructions Column Guide Card -->
    <div class="glass-card p-6 sm:p-8 rounded-3xl shadow-xl space-y-4">
      <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200 flex items-center gap-2">
        <i class="fa-solid fa-circle-info text-brand-400"></i>
        <span>Estructura de las 22 Columnas</span>
      </h3>
      <p class="text-xs text-slate-400">El parser inteligente valida los siguientes encabezados oficiales:</p>

      <div class="space-y-2 text-xs max-h-[460px] overflow-y-auto pr-1 font-mono">
        <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
          <strong class="text-white">1. ESTADO</strong>: Condición del equipo
        </div>
        <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
          <strong class="text-brand-400">2. No CONTENEDOR</strong>: Sigla naviera (ej. TLLU5148616)
        </div>
        <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
          <strong class="text-white">3. No CHASSIS</strong>: VIN serial único
        </div>
        <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
          <strong class="text-white">4. No MOTOR</strong>: Serial del motor
        </div>
        <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
          <strong class="text-white">5. MARCA / 6. MODELO</strong>: Yadea, Taizhou, etc.
        </div>
        <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
          <strong class="text-brand-400">11. CODIGO DE ENVIO</strong>: Tracking SolveCargo o BL
        </div>
        <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
          <strong class="text-white">14-17. NOMBRES & APELLIDOS</strong>: Destinatario
        </div>
        <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
          <strong class="text-emerald-400">18. CI / 20. No TELEFONO</strong>: Carnet y móvil Cuba
        </div>
        <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800">
          <strong class="text-amber-400">21. PROVINCIA</strong>: Asignación de almacén HUB
        </div>
      </div>
    </div>

  </div>

</div>

<script>
  function handleFileSelected(input) {
    const display = document.getElementById('filename-display');
    if (input.files && input.files[0]) {
      display.innerText = '✓ Archivo seleccionado: ' + input.files[0].name;
      display.classList.remove('hidden');
    } else {
      display.classList.add('hidden');
    }
  }

  // Soporte Drag and Drop nativo
  const dropzone = document.getElementById('dropzone-box');
  const fileInput = document.getElementById('manifest_file');

  ['dragenter', 'dragover'].forEach(eventName => {
    dropzone.addEventListener(eventName, (e) => {
      e.preventDefault();
      dropzone.classList.add('border-brand-500', 'bg-slate-900');
    }, false);
  });

  ['dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, (e) => {
      e.preventDefault();
      dropzone.classList.remove('border-brand-500', 'bg-slate-900');
    }, false);
  });

  dropzone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    if (files.length) {
      fileInput.files = files;
      handleFileSelected(fileInput);
    }
  }, false);
</script>
@endsection
