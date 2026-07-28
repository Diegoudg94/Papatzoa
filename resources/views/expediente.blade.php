@php
  $activeTab = request('tab', 'emociones');

  if (! in_array($activeTab, ['emociones', 'citas', 'notas'], true)) {
    $activeTab = 'emociones';
  }

  $terapeutaId = session('usuario_id');
  $terapeuta = $terapeutaId ? Illuminate\Support\Facades\DB::table('users')->where('id', $terapeutaId)->first() : null;
  $terapeutaNombre = trim(($terapeuta->nombre ?? session('usuario_nombre', '')) . ' ' . ($terapeuta->apellido ?? session('usuario_apellido', '')));
  $terapeutaNombre = $terapeutaNombre !== '' ? $terapeutaNombre : 'Terapeuta';
  $terapeutaIniciales = collect(explode(' ', $terapeutaNombre))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('');
  $terapeutaFoto = null;

  if (! empty($terapeuta->profile_photo_path)) {
    $terapeutaFoto = Illuminate\Support\Facades\Storage::url($terapeuta->profile_photo_path);
  } elseif (! empty($terapeuta->avatar_url)) {
    $terapeutaFoto = $terapeuta->avatar_url;
  }

  $pacienteNombre = trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? ''));
  $pacienteNombre = $pacienteNombre !== '' ? $pacienteNombre : 'Paciente sin nombre';
  $fechaVinculo = $paciente->updated_at ? \Carbon\Carbon::parse($paciente->updated_at)->format('d/m/Y') : null;
  $citaInicio = function ($cita): \Carbon\Carbon {
    return $cita->starts_at
      ? \Carbon\Carbon::parse($cita->starts_at)->setTimezone($cita->timezone ?: 'America/Mexico_City')
      : \Carbon\Carbon::parse(($cita->fecha ?? now()->toDateString()) . ' ' . ($cita->hora ?: '00:00'));
  };
  $citasOrdenadas = $citas->sortBy(fn ($cita) => $citaInicio($cita)->timestamp);
  $ahora = now();
  $proximaCita = $citasOrdenadas->first(function ($cita) use ($ahora, $citaInicio) {
    $estado = strtolower($cita->estado ?: '');
    $fechaHora = $citaInicio($cita);
    return in_array($estado, ['aceptada', 'aceptado', 'confirmada', 'confirmado'], true) && $fechaHora->gte($ahora);
  });
  $ultimaSesion = $citas->first(function ($cita) use ($ahora, $citaInicio) {
    $estado = strtolower($cita->estado ?: '');
    $fechaHora = $citaInicio($cita);
    return in_array($estado, ['aceptada', 'aceptado', 'confirmada', 'confirmado'], true) && $fechaHora->lt($ahora);
  });
@endphp

<!DOCTYPE html>
<html class="light" lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Expediente del paciente | Papatzoa</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#376457',
            'primary-fixed': '#bceddb',
            'primary-fixed-dim': '#a0d1c0',
            'on-primary': '#ffffff',
            'on-primary-fixed-variant': '#204f42',
            secondary: '#6f5b44',
            'secondary-fixed': '#fadec1',
            'secondary-container': '#f7dbbe',
            'on-secondary-container': '#735f48',
            tertiary: '#804f48',
            'tertiary-fixed': '#ffdad5',
            'surface-container-lowest': '#ffffff',
            'surface-container-low': '#edf4ff',
            'surface-container': '#e4effd',
            'surface-container-high': '#dfe9f7',
            surface: '#f7f9ff',
            background: '#f7f9ff',
            'on-surface': '#121d26',
            'on-surface-variant': '#404945',
            outline: '#717975',
            'outline-variant': '#c0c8c4',
            error: '#ba1a1a',
            'error-container': '#ffdad6'
          },
          borderRadius: {
            DEFAULT: '1rem',
            lg: '2rem',
            full: '9999px'
          },
          fontFamily: {
            sans: ['Manrope', 'sans-serif']
          }
        }
      }
    };
  </script>
  <style>
    body { background: #f7f9ff; color: #121d26; font-family: 'Manrope', sans-serif; overflow-x: hidden; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
    .atmospheric-shadow { box-shadow: 0 10px 40px -10px rgba(55, 100, 87, 0.08); }
    [hidden] { display: none !important; }
  </style>
</head>
<body class="bg-background">
  <aside class="hidden lg:flex h-screen w-80 fixed left-0 top-0 border-r border-outline-variant bg-surface-container-lowest flex-col p-8 z-50">
    <div class="mb-12">
      <h1 class="text-2xl font-bold text-primary">Panel del terapeuta</h1>
      <p class="text-sm text-on-surface-variant">Papatzoa</p>
    </div>

    <nav class="flex-1 space-y-2" aria-label="Navegación principal">
      <a class="flex items-center gap-6 px-6 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/terapeuta') }}">
        <span class="material-symbols-outlined">dashboard</span><span class="text-sm font-medium">Inicio</span>
      </a>
      <a class="flex items-center gap-6 px-6 py-3 bg-secondary-container text-on-secondary-container rounded-lg font-bold" href="{{ url('/pacientes') }}" aria-current="page">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">group</span><span class="text-sm">Pacientes</span>
      </a>
      <a class="flex items-center gap-6 px-6 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/terapeuta') }}#proximas-citas">
        <span class="material-symbols-outlined">calendar_today</span><span class="text-sm font-medium">Próximas citas</span>
      </a>
      <a class="flex items-center gap-6 px-6 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/confirmar') }}">
        <span class="material-symbols-outlined">event_available</span><span class="text-sm font-medium">Confirmar citas</span>
      </a>
      <a class="flex items-center gap-6 px-6 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/pacientes') }}">
        <span class="material-symbols-outlined">folder_shared</span><span class="text-sm font-medium">Expedientes</span>
      </a>
      <a class="flex items-center gap-6 px-6 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/pacientes') }}">
        <span class="material-symbols-outlined">description</span><span class="text-sm font-medium">Notas clínicas</span>
      </a>
    </nav>

    <div class="mt-auto pt-6 border-t border-outline-variant space-y-1">
      <a class="flex items-center gap-6 px-6 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/terapeuta/mis-datos') }}">
        <span class="material-symbols-outlined">badge</span><span class="text-sm font-medium">Mi perfil profesional</span>
      </a>
      <a class="flex items-center gap-6 px-6 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/terapeuta/mis-datos') }}">
        <span class="material-symbols-outlined">account_circle</span><span class="text-sm font-medium">Mi cuenta</span>
      </a>
      <a class="flex items-center gap-6 px-6 py-3 text-error hover:bg-error-container/40 rounded-lg transition-colors" href="{{ url('/logout') }}">
        <span class="material-symbols-outlined">logout</span><span class="text-sm font-medium">Cerrar sesión</span>
      </a>
    </div>
  </aside>

  <main class="min-h-screen lg:ml-80">
    <header class="sticky top-0 z-40 bg-surface/90 backdrop-blur-md px-5 py-5 lg:px-20 lg:py-8">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-2xl font-bold text-primary sm:text-3xl">Expediente del paciente</h2>
          <div class="mt-2 flex items-center gap-2 text-sm">
            <a class="text-on-surface-variant hover:text-primary" href="{{ url('/terapeuta') }}">Inicio</a>
            <span class="text-outline">/</span>
            <a class="font-bold text-primary hover:underline" href="{{ url('/pacientes') }}">Pacientes</a>
          </div>
        </div>
        <div class="flex items-center gap-4">
          <div class="text-right hidden sm:block">
            <p class="text-sm font-bold text-on-surface">{{ $terapeutaNombre }}</p>
            <p class="text-xs text-on-surface-variant">Terapeuta</p>
          </div>
          @if ($terapeutaFoto)
            <img class="h-12 w-12 rounded-full object-cover border-2 border-primary-fixed-dim" src="{{ $terapeutaFoto }}" alt="Foto de perfil de {{ $terapeutaNombre }}">
          @else
            <div class="h-12 w-12 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-bold border-2 border-primary-fixed">
              {{ $terapeutaIniciales ?: 'T' }}
            </div>
          @endif
        </div>
      </div>
    </header>

    <div class="px-5 py-6 lg:px-20 lg:py-8 space-y-8">
      <section class="flex flex-col md:flex-row justify-between items-start gap-6">
        <div class="max-w-3xl">
          <h1 class="text-3xl font-extrabold text-primary sm:text-5xl">Expediente: <span class="font-semibold text-secondary">{{ $pacienteNombre }}</span></h1>
          <p class="mt-4 text-on-surface-variant">Expediente clínico con registros compartidos por el paciente y notas privadas del terapeuta. Los registros sensibles se almacenan cifrados en la plataforma.</p>
        </div>
        <a class="inline-flex items-center gap-2 rounded-full border border-outline-variant px-5 py-3 font-bold text-primary hover:bg-surface-container-low" href="{{ url('/pacientes') }}">
          <span class="material-symbols-outlined">arrow_back</span>
          Volver a pacientes
        </a>
      </section>

      @if (session('success_expediente'))
        <div class="rounded-lg border border-primary-fixed bg-primary-fixed/50 px-5 py-4 font-medium text-primary">{{ session('success_expediente') }}</div>
      @endif

      @if ($errors->any())
        <div class="rounded-lg border border-error-container bg-error-container/60 px-5 py-4 font-medium text-error">Revisa la nota antes de guardarla.</div>
      @endif

      <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <article class="bg-surface-container-lowest p-6 rounded-lg atmospheric-shadow flex flex-col gap-3 border border-outline-variant/30">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Vínculo</span>
            <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary"><span class="material-symbols-outlined text-sm">link</span></div>
          </div>
          <div>
            <p class="text-2xl font-bold text-on-surface">Activo</p>
            <p class="text-xs text-on-surface-variant">{{ $fechaVinculo ? 'Desde: ' . $fechaVinculo : 'Sin información' }}</p>
          </div>
        </article>
        <article class="bg-surface-container-lowest p-6 rounded-lg atmospheric-shadow flex flex-col gap-3 border border-outline-variant/30">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Última sesión</span>
            <div class="w-8 h-8 rounded-full bg-secondary-fixed flex items-center justify-center text-secondary"><span class="material-symbols-outlined text-sm">history</span></div>
          </div>
          <div>
            <p class="text-2xl font-bold text-on-surface">{{ $ultimaSesion ? $citaInicio($ultimaSesion)->format('d/m/Y') : 'Sin sesiones registradas' }}</p>
            <p class="text-xs text-on-surface-variant">{{ $ultimaSesion ? 'Hora: ' . $citaInicio($ultimaSesion)->format('H:i') : 'Sin información' }}</p>
          </div>
        </article>
        <article class="bg-surface-container-lowest p-6 rounded-lg atmospheric-shadow flex flex-col gap-3 border border-outline-variant/30">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Próxima cita</span>
            <div class="w-8 h-8 rounded-full bg-tertiary-fixed flex items-center justify-center text-tertiary"><span class="material-symbols-outlined text-sm">event</span></div>
          </div>
          <div>
            <p class="text-2xl font-bold text-on-surface">{{ $proximaCita ? $citaInicio($proximaCita)->format('d/m/Y') : 'Sin próxima cita' }}</p>
            <p class="text-xs text-on-surface-variant">{{ $proximaCita ? 'Hora: ' . $citaInicio($proximaCita)->format('H:i') : 'Sin información' }}</p>
          </div>
        </article>
      </section>

      <section>
        <div class="flex items-center gap-6 overflow-x-auto border-b border-outline-variant" role="tablist" aria-label="Secciones del expediente">
          <button class="tab shrink-0 flex items-center gap-2 px-2 pb-3 text-sm font-bold {{ $activeTab === 'emociones' ? 'border-b-2 border-primary text-primary' : 'text-on-surface-variant' }}" type="button" data-tab="emociones">
            <span class="material-symbols-outlined text-base">favorite</span>Emociones
          </button>
          <button class="tab shrink-0 flex items-center gap-2 px-2 pb-3 text-sm font-bold {{ $activeTab === 'citas' ? 'border-b-2 border-primary text-primary' : 'text-on-surface-variant' }}" type="button" data-tab="citas">
            <span class="material-symbols-outlined text-base">calendar_month</span>Citas / Sesiones
          </button>
          <button class="tab shrink-0 flex items-center gap-2 px-2 pb-3 text-sm font-bold {{ $activeTab === 'notas' ? 'border-b-2 border-primary text-primary' : 'text-on-surface-variant' }}" type="button" data-tab="notas">
            <span class="material-symbols-outlined text-base">sticky_note_2</span>Notas
          </button>
        </div>
      </section>

      <section class="tab-panel" id="tab-emociones" {{ $activeTab === 'emociones' ? '' : 'hidden' }}>
        <article class="bg-surface-container-lowest rounded-lg atmospheric-shadow overflow-hidden border border-outline-variant/20">
          <div class="p-6 bg-surface-container-low border-b border-outline-variant">
            <h2 class="text-2xl font-bold text-primary">Emociones registradas</h2>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full min-w-[780px] text-left" aria-label="Emociones registradas">
              <thead class="bg-surface/80 border-b border-outline-variant">
                <tr>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Fecha</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Hora</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Emoción</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Intensidad</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase text-right">Detalles</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/30">
                @forelse ($emociones as $emocion)
                  @php
                    $intensity = is_null($emocion->intensidad) ? null : (int) $emocion->intensidad;
                    $intensityClass = 'bg-surface-container-high text-on-surface-variant';
                    $intensityLabel = 'Sin dato';
                    $intensityText = 'Sin dato';

                    if (! is_null($intensity)) {
                      if ($intensity <= 2) {
                        $intensityClass = 'bg-emerald-100 text-emerald-800';
                        $intensityLabel = 'Baja';
                      } elseif ($intensity <= 4) {
                        $intensityClass = 'bg-lime-100 text-lime-800';
                        $intensityLabel = 'Leve';
                      } elseif ($intensity <= 6) {
                        $intensityClass = 'bg-yellow-100 text-yellow-800';
                        $intensityLabel = 'Media';
                      } elseif ($intensity <= 8) {
                        $intensityClass = 'bg-orange-100 text-orange-800';
                        $intensityLabel = 'Alta';
                      } else {
                        $intensityClass = 'bg-red-100 text-red-800';
                        $intensityLabel = 'Muy alta';
                      }

                      $intensityText = $intensity . '/10 · ' . $intensityLabel;
                    }
                  @endphp
                  <tr class="emotion-summary-row hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-6 py-5 whitespace-nowrap">{{ $emocion->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-5 whitespace-nowrap">{{ $emocion->created_at->format('H:i') }}</td>
                    <td class="px-6 py-5">
                      <span class="inline-flex items-center gap-2 rounded-full border border-outline-variant bg-surface-container px-4 py-1.5 font-bold">
                        <span class="h-2.5 w-2.5 rounded-full bg-primary"></span>
                        {{ $emocion->emocion ?: 'Sin registro' }}
                      </span>
                    </td>
                    <td class="px-6 py-5">
                      <span class="inline-flex rounded-full px-4 py-1 text-sm font-bold {{ $intensityClass }}">{{ $intensityText }}</span>
                    </td>
                    <td class="px-6 py-5 text-right">
                      <button class="details-toggle inline-flex items-center gap-1 font-bold text-primary hover:underline" type="button" aria-expanded="false" aria-controls="emocion-detalle-{{ $emocion->id }}" data-target="emocion-detalle-{{ $emocion->id }}">
                        Ver más detalles
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                      </button>
                    </td>
                  </tr>
                  <tr class="emotion-detail-row bg-surface-container-low/40" id="emocion-detalle-{{ $emocion->id }}" hidden>
                    <td colspan="5" class="px-6 py-5">
                      <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-lg bg-white p-4 border border-outline-variant/30"><p class="text-xs font-bold uppercase text-on-surface-variant">Situación</p><p class="mt-1">{{ $emocion->situacion ?: 'Sin información' }}</p></div>
                        <div class="rounded-lg bg-white p-4 border border-outline-variant/30"><p class="text-xs font-bold uppercase text-on-surface-variant">Pensamiento automático</p><p class="mt-1">{{ $emocion->pensamiento ?: 'Sin información' }}</p></div>
                        <div class="rounded-lg bg-white p-4 border border-outline-variant/30"><p class="text-xs font-bold uppercase text-on-surface-variant">Conducta</p><p class="mt-1">{{ $emocion->conducta ?: 'Sin información' }}</p></div>
                        <div class="rounded-lg bg-white p-4 border border-outline-variant/30"><p class="text-xs font-bold uppercase text-on-surface-variant">Interpretación</p><p class="mt-1">{{ $emocion->interpretacion ?: 'Sin información' }}</p></div>
                        <div class="rounded-lg bg-white p-4 border border-outline-variant/30 md:col-span-2"><p class="text-xs font-bold uppercase text-on-surface-variant">Reestructuración</p><p class="mt-1">{{ $emocion->reestructuracion ?: 'Sin información' }}</p></div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td class="px-6 py-8 text-center text-on-surface-variant" colspan="5">Sin registros de emociones.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </article>
      </section>

      <section class="tab-panel" id="tab-sesiones" {{ $activeTab === 'citas' ? '' : 'hidden' }}>
        <article class="bg-surface-container-lowest rounded-lg atmospheric-shadow overflow-hidden border border-outline-variant/20">
          <div class="p-6 bg-surface-container-low border-b border-outline-variant">
            <h2 class="text-2xl font-bold text-primary">Citas / Sesiones</h2>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left" aria-label="Citas y sesiones">
              <thead class="bg-surface/80 border-b border-outline-variant">
                <tr>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Fecha</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Hora</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Motivo</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Estado</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase text-center">Notas</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/30">
                @forelse ($citas as $cita)
                  @php
                    $estado = strtolower($cita->estado ?: 'pendiente');
                    $estadoClass = match ($estado) {
                      'aceptada', 'aceptado', 'confirmada', 'confirmado' => 'bg-primary-fixed text-on-primary-fixed-variant',
                      'cancelada', 'cancelado', 'rechazada', 'rechazado' => 'bg-error-container text-error',
                      default => 'bg-secondary-container text-on-secondary-container',
                    };
                    $notasDeEstaCita = $notasSesion->where('cita_id', $cita->id);
                  @endphp
                  <tr class="session-summary-row hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-6 py-5 whitespace-nowrap">{{ $citaInicio($cita)->format('d/m/Y') }}</td>
                    <td class="px-6 py-5 whitespace-nowrap">{{ $citaInicio($cita)->format('H:i') }}</td>
                    <td class="px-6 py-5">{{ $cita->motivo ?: 'Sin registro' }}</td>
                    <td class="px-6 py-5"><span class="inline-flex rounded-full px-4 py-1 text-sm font-bold {{ $estadoClass }}">{{ ucfirst($cita->estado ?: 'pendiente') }}</span></td>
                    <td class="px-6 py-5 text-center">
                      <button class="session-note-toggle inline-flex items-center gap-1 rounded-full bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:opacity-90" type="button" aria-expanded="false" aria-controls="session-note-panel-{{ $cita->id }}" data-target="session-note-panel-{{ $cita->id }}">
                        Añadir / ver notas
                      </button>
                    </td>
                  </tr>
                  <tr class="session-note-row bg-surface-container-low/40" id="session-note-panel-{{ $cita->id }}" hidden>
                    <td colspan="5" class="px-6 py-5">
                      <div class="grid gap-6 lg:grid-cols-[minmax(280px,0.9fr)_minmax(320px,1.1fr)]">
                        <form class="space-y-4 rounded-lg bg-white p-5 border border-outline-variant/30" method="POST" action="/expediente/{{ $paciente->id }}/citas/{{ $cita->id }}/nota">
                          @csrf
                          <div>
                            <label class="mb-2 block text-sm font-bold text-on-surface-variant" for="nota-sesion-{{ $cita->id }}">Nueva nota</label>
                            <textarea class="w-full rounded-lg border border-outline-variant bg-white p-3 focus:border-primary focus:ring-primary/30" id="nota-sesion-{{ $cita->id }}" name="nota" rows="4" maxlength="3000" required></textarea>
                          </div>
                          <button class="inline-flex rounded-full bg-primary px-5 py-2 font-bold text-on-primary hover:opacity-90" type="submit">Guardar nota</button>
                        </form>

                        <div class="space-y-4">
                          <h3 class="font-bold text-primary">Notas anteriores</h3>
                          @forelse ($notasDeEstaCita as $notaSesion)
                            <article class="rounded-lg bg-white p-5 border border-outline-variant/30">
                              <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs font-bold text-on-surface-variant">{{ \Carbon\Carbon::parse($notaSesion->created_at)->format('d/m/Y H:i') }}</p>
                                <div class="flex flex-wrap gap-2">
                                  <button class="session-note-edit-toggle rounded-full border border-outline-variant px-4 py-2 text-sm font-bold text-primary hover:bg-surface-container-low" type="button" aria-expanded="false" aria-controls="session-note-edit-{{ $notaSesion->id }}" data-target="session-note-edit-{{ $notaSesion->id }}">Editar</button>
                                  <form method="POST" action="/expediente/{{ $paciente->id }}/citas/{{ $cita->id }}/nota/{{ $notaSesion->id }}" onsubmit="return confirm('¿Seguro que deseas eliminar esta nota?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-full bg-error-container px-4 py-2 text-sm font-bold text-error hover:opacity-90" type="submit">Eliminar</button>
                                  </form>
                                </div>
                              </div>
                              <p class="mt-4 whitespace-pre-line text-on-surface">{{ $notaSesion->nota }}</p>

                              <form class="session-note-edit-form mt-4 space-y-3" id="session-note-edit-{{ $notaSesion->id }}" method="POST" action="/expediente/{{ $paciente->id }}/citas/{{ $cita->id }}/nota/{{ $notaSesion->id }}" hidden>
                                @csrf
                                @method('PUT')
                                <label class="block text-sm font-bold text-on-surface-variant" for="nota-sesion-edit-{{ $notaSesion->id }}">Editar nota</label>
                                <textarea class="w-full rounded-lg border border-outline-variant bg-white p-3 focus:border-primary focus:ring-primary/30" id="nota-sesion-edit-{{ $notaSesion->id }}" name="nota" rows="4" maxlength="3000" required>{{ $notaSesion->nota }}</textarea>
                                <div class="flex flex-wrap gap-2">
                                  <button class="rounded-full bg-primary px-5 py-2 font-bold text-on-primary hover:opacity-90" type="submit">Guardar cambios</button>
                                  <button class="session-note-edit-cancel rounded-full border border-outline-variant px-5 py-2 font-bold text-primary hover:bg-surface-container-low" type="button">Cancelar</button>
                                </div>
                              </form>
                            </article>
                          @empty
                            <p class="rounded-lg bg-white p-5 text-on-surface-variant border border-outline-variant/30">Aún no hay notas para esta sesión.</p>
                          @endforelse
                        </div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td class="px-6 py-8 text-center text-on-surface-variant" colspan="5">Sin citas registradas.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </article>
      </section>

      <section class="tab-panel" id="tab-notas" {{ $activeTab === 'notas' ? '' : 'hidden' }}>
        <article class="grid gap-6 lg:grid-cols-[minmax(320px,1fr)_minmax(320px,0.9fr)]">
          <div class="rounded-lg bg-surface-container-lowest p-6 atmospheric-shadow border border-outline-variant/20">
            <h2 class="text-2xl font-bold text-primary">Notas registradas</h2>
            <div class="mt-5 space-y-4">
              @forelse ($notas as $nota)
                <div class="rounded-lg border border-outline-variant/30 bg-white p-5">
                  <p class="text-xs font-bold text-on-surface-variant">{{ $nota->created_at->format('d/m/Y H:i') }}</p>
                  <p class="mt-3 whitespace-pre-line">{{ $nota->nota ?: 'No se pudo mostrar esta nota.' }}</p>
                </div>
              @empty
                <p class="rounded-lg border border-outline-variant/30 bg-white p-5 text-on-surface-variant">Aún no hay notas registradas.</p>
              @endforelse
            </div>
          </div>

          <form class="rounded-lg bg-surface-container-lowest p-6 atmospheric-shadow border border-outline-variant/20 space-y-4" method="POST" action="/expediente/{{ $paciente->id }}/notas">
            @csrf
            <div>
              <label class="mb-2 block text-sm font-bold text-on-surface-variant" for="nota">Nueva nota</label>
              <textarea class="w-full rounded-lg border border-outline-variant bg-white p-3 focus:border-primary focus:ring-primary/30" id="nota" name="nota" rows="8" maxlength="3000" required>{{ old('nota') }}</textarea>
              @error('nota')
                <p class="mt-2 text-sm font-bold text-error">{{ $message }}</p>
              @enderror
            </div>
            <button class="inline-flex rounded-full bg-primary px-6 py-3 font-bold text-on-primary hover:opacity-90" type="submit">Guardar nota</button>
          </form>
        </article>
      </section>
    </div>
  </main>

  <nav class="fixed inset-x-0 bottom-0 z-50 grid grid-cols-4 border-t border-outline-variant bg-surface-container-lowest p-2 lg:hidden" aria-label="Navegación móvil">
    <a class="flex flex-col items-center gap-1 rounded-lg px-2 py-2 text-xs text-on-surface-variant" href="{{ url('/terapeuta') }}"><span class="material-symbols-outlined">dashboard</span>Inicio</a>
    <a class="flex flex-col items-center gap-1 rounded-lg bg-secondary-container px-2 py-2 text-xs font-bold text-on-secondary-container" href="{{ url('/pacientes') }}"><span class="material-symbols-outlined">group</span>Pacientes</a>
    <a class="flex flex-col items-center gap-1 rounded-lg px-2 py-2 text-xs text-on-surface-variant" href="{{ url('/confirmar') }}"><span class="material-symbols-outlined">event_available</span>Citas</a>
    <a class="flex flex-col items-center gap-1 rounded-lg px-2 py-2 text-xs text-error" href="{{ url('/logout') }}"><span class="material-symbols-outlined">logout</span>Salir</a>
  </nav>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const activeClasses = ['border-b-2', 'border-primary', 'text-primary'];
      const inactiveClasses = ['text-on-surface-variant'];
      const tabButtons = document.querySelectorAll('.tab');
      const panels = {
        emociones: document.getElementById('tab-emociones'),
        citas: document.getElementById('tab-sesiones'),
        notas: document.getElementById('tab-notas'),
      };

      tabButtons.forEach((button) => {
        button.addEventListener('click', () => {
          const key = button.dataset.tab;

          if (!panels[key]) {
            return;
          }

          tabButtons.forEach((item) => {
            item.classList.remove(...activeClasses);
            item.classList.add(...inactiveClasses);
          });

          button.classList.remove(...inactiveClasses);
          button.classList.add(...activeClasses);

          Object.values(panels).forEach((panel) => {
            panel.hidden = true;
          });

          panels[key].hidden = false;
          const url = new URL(window.location.href);
          url.searchParams.set('tab', key);
          window.history.replaceState({}, '', url.toString());
        });
      });

      document.querySelectorAll('.details-toggle').forEach((button) => {
        button.addEventListener('click', () => {
          const detailRow = document.getElementById(button.dataset.target);
          const summaryRow = button.closest('.emotion-summary-row');
          if (!detailRow || !summaryRow) {
            return;
          }

          const isExpanded = button.getAttribute('aria-expanded') === 'true';
          button.setAttribute('aria-expanded', String(!isExpanded));
          button.firstChild.textContent = isExpanded ? 'Ver más detalles' : 'Ocultar detalles';
          detailRow.hidden = isExpanded;
          summaryRow.classList.toggle('bg-surface-container-low', !isExpanded);
        });
      });

      document.querySelectorAll('.session-note-toggle').forEach((button) => {
        button.addEventListener('click', () => {
          const panelRow = document.getElementById(button.dataset.target);
          const summaryRow = button.closest('.session-summary-row');
          if (!panelRow || !summaryRow) {
            return;
          }

          const isExpanded = button.getAttribute('aria-expanded') === 'true';
          button.setAttribute('aria-expanded', String(!isExpanded));
          button.textContent = isExpanded ? 'Añadir / ver notas' : 'Ocultar';
          panelRow.hidden = isExpanded;
          summaryRow.classList.toggle('bg-surface-container-low', !isExpanded);
        });
      });

      document.querySelectorAll('.session-note-edit-toggle').forEach((button) => {
        button.addEventListener('click', () => {
          const editForm = document.getElementById(button.dataset.target);
          if (!editForm) {
            return;
          }

          const isExpanded = button.getAttribute('aria-expanded') === 'true';
          button.setAttribute('aria-expanded', String(!isExpanded));
          editForm.hidden = isExpanded;
        });
      });

      document.querySelectorAll('.session-note-edit-cancel').forEach((button) => {
        button.addEventListener('click', () => {
          const editForm = button.closest('.session-note-edit-form');
          if (!editForm) {
            return;
          }

          const toggle = document.querySelector(`.session-note-edit-toggle[data-target="${editForm.id}"]`);
          editForm.hidden = true;

          if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
          }
        });
      });
    });
  </script>

  @include('partials.marker-widget')
</body>
</html>
