@extends('layouts.app')

@section('page_title', 'Previsualización de Manifiesto — SCGI')

@section('content')
<div class="space-y-6 animate-fade-in">

  <!-- Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/60 p-6 rounded-3xl border border-slate-800 shadow-xl backdrop-blur-xl">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-xs font-bold uppercase tracking-wider mb-2">
        <i class="fa-solid fa-list-check"></i> Previsualización de Datos
      </div>
      <h2 class="text-2xl font-black text-white tracking-tight">Cotejo de Manifiesto: {{ $filename }}</h2>
      <p class="text-sm text-slate-400">Se detectaron <strong>{{ $totalRows }}</strong> bultos listos para importar a la base de datos.</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('manifests.import.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all border border-slate-700">
        Cancelar
      </a>

      <form method="POST" action="{{ route('manifests.import.execute') }}">
        @csrf
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-black transition-all shadow-lg shadow-emerald-600/30">
          <i class="fa-solid fa-cloud-arrow-down"></i>
          <span>Confirmar e Importar {{ $totalRows }} Bultos</span>
        </button>
      </form>
    </div>
  </div>

  @if($sendWhatsApp)
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold flex items-center gap-3">
      <i class="fa-brands fa-whatsapp text-lg"></i>
      <span>Envío automático de WhatsApp activado: Los clientes con número telefónico recibirán su PIN de seguimiento inmediatamente al crearse el equipo.</span>
    </div>
  @endif

  <!-- Preview Table Card -->
  <div class="bg-slate-900/60 rounded-3xl border border-slate-800 shadow-xl overflow-hidden backdrop-blur-xl">
    <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
      <table class="w-full text-left text-xs text-slate-300">
        <thead class="bg-slate-800/90 sticky top-0 text-[11px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-700 z-10">
          <tr>
            <th class="py-3 px-3">#</th>
            <th class="py-3 px-3">Contenedor</th>
            <th class="py-3 px-3">Chasis (VIN)</th>
            <th class="py-3 px-3">Motor</th>
            <th class="py-3 px-3">Marca / Modelo</th>
            <th class="py-3 px-3">Cód. Envío</th>
            <th class="py-3 px-3">Cliente</th>
            <th class="py-3 px-3">CI</th>
            <th class="py-3 px-3">Teléfono</th>
            <th class="py-3 px-3">Provincia</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
          @foreach($rows as $idx => $row)
            <tr class="hover:bg-slate-800/40 transition-all">
              <td class="py-2.5 px-3 text-slate-500 font-mono">{{ $idx + 1 }}</td>
              <td class="py-2.5 px-3 font-mono font-bold text-indigo-400">{{ $row['contenedor'] ?: '—' }}</td>
              <td class="py-2.5 px-3 font-mono font-bold text-white">{{ $row['chasis'] ?: 'Autogenerado' }}</td>
              <td class="py-2.5 px-3 font-mono text-slate-400">{{ $row['motor'] ?: '—' }}</td>
              <td class="py-2.5 px-3 font-semibold text-white">
                {{ $row['marca'] }} {{ $row['modelo'] }}
                @if($row['color'])
                  <span class="text-[10px] text-slate-400 block font-normal">{{ $row['color'] }}</span>
                @endif
              </td>
              <td class="py-2.5 px-3 font-mono text-slate-300">{{ $row['codigo_envio'] ?: '—' }}</td>
              <td class="py-2.5 px-3 font-bold text-white">{{ $row['cliente_nombre'] ?: 'Consignatario General' }}</td>
              <td class="py-2.5 px-3 font-mono text-slate-400">{{ $row['ci'] ?: '—' }}</td>
              <td class="py-2.5 px-3 font-mono text-emerald-400 font-bold">{{ $row['telefono'] ?: '—' }}</td>
              <td class="py-2.5 px-3 font-semibold text-slate-300">{{ $row['provincia'] ?: 'La Habana' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
