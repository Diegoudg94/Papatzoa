@php
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

  $pacienteIds = $pacientes->pluck('id')->all();
  $proximasPorPaciente = collect();
  $proximasCitasTotal = 0;

  if ($terapeutaId && count($pacienteIds) > 0) {
    $proximasCitas = Illuminate\Support\Facades\DB::table('citas')
      ->whereIn('paciente_id', $pacienteIds)
      ->where(function ($query) use ($terapeutaId) {
        $query->where('terapeuta_id', (int) $terapeutaId)
          ->orWhere('terapeuta_id', (string) $terapeutaId);
      })
      ->whereIn('estado', ['aceptada', 'aceptado', 'confirmada', 'confirmado'])
      ->where(function ($query) {
        $query->where('starts_at', '>=', now())
          ->orWhere(function ($legacyQuery) {
            $legacyQuery->whereNull('starts_at')
              ->where('fecha', '>=', now()->toDateString());
          });
      })
      ->orderByRaw('COALESCE(starts_at, fecha::timestamp) ASC')
      ->orderBy('hora')
      ->get();

    $proximasCitasTotal = $proximasCitas->count();
    $proximasPorPaciente = $proximasCitas->groupBy('paciente_id')->map(fn ($items) => $items->first());
  }
@endphp

<!DOCTYPE html>
<html class="light" lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mis pacientes | Papatzoa</title>
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
            'on-primary': '#ffffff',
            'on-primary-fixed-variant': '#204f42',
            secondary: '#6f5b44',
            'secondary-container': '#f7dbbe',
            'on-secondary-container': '#735f48',
            tertiary: '#804f48',
            'tertiary-fixed': '#ffdad5',
            'surface-container-lowest': '#ffffff',
            'surface-container-low': '#edf4ff',
            'surface-container': '#e4effd',
            'surface-container-high': '#dfe9f7',
            'surface-container-highest': '#d9e3f1',
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
          <p class="text-sm text-on-surface-variant">Inicio / <span class="font-bold text-primary">Pacientes</span></p>
          <h2 class="mt-1 text-2xl font-bold text-primary sm:text-3xl">Hola, {{ $terapeutaNombre }}</h2>
        </div>
        <div class="flex items-center gap-4">
          <div class="text-right hidden sm:block">
            <p class="text-sm font-bold text-on-surface">{{ $terapeutaNombre }}</p>
            <p class="text-xs text-on-surface-variant">Terapeuta</p>
          </div>
          @if ($terapeutaFoto)
            <img class="h-12 w-12 rounded-full object-cover border-2 border-primary-fixed" src="{{ $terapeutaFoto }}" alt="Foto de perfil de {{ $terapeutaNombre }}">
          @else
            <div class="h-12 w-12 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-bold border-2 border-primary-fixed">
              {{ $terapeutaIniciales ?: 'T' }}
            </div>
          @endif
        </div>
      </div>
    </header>

    <div class="px-5 py-6 lg:px-20 lg:py-8 space-y-8">
      <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold text-on-surface">Mis pacientes</h1>
          <p class="mt-2 text-on-surface-variant">Pacientes vinculados actualmente a tu panel clínico.</p>
        </div>
        <a class="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-6 py-3 font-bold text-on-primary shadow-md hover:opacity-90" href="{{ url('/terapeuta') }}">
          <span class="material-symbols-outlined">vpn_key</span>
          Ver PIN
        </a>
      </section>

      <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <article class="bg-surface-container-lowest p-6 rounded-lg atmospheric-shadow border border-outline-variant/30 flex items-center gap-6">
          <div class="w-14 h-14 rounded-2xl bg-primary-fixed flex items-center justify-center">
            <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">group</span>
          </div>
          <div>
            <p class="text-sm text-on-surface-variant font-medium">Pacientes activos</p>
            <p class="text-2xl font-bold text-primary">{{ $pacientes->count() }}</p>
          </div>
        </article>
        <article class="bg-surface-container-lowest p-6 rounded-lg atmospheric-shadow border border-outline-variant/30 flex items-center gap-6">
          <div class="w-14 h-14 rounded-2xl bg-secondary-container flex items-center justify-center">
            <span class="material-symbols-outlined text-secondary text-3xl" style="font-variation-settings: 'FILL' 1;">calendar_month</span>
          </div>
          <div>
            <p class="text-sm text-on-surface-variant font-medium">Próximas citas</p>
            <p class="text-2xl font-bold text-secondary">{{ $proximasCitasTotal }}</p>
          </div>
        </article>
        <article class="bg-surface-container-lowest p-6 rounded-lg atmospheric-shadow border border-outline-variant/30 flex items-center gap-6">
          <div class="w-14 h-14 rounded-2xl bg-tertiary-fixed flex items-center justify-center">
            <span class="material-symbols-outlined text-tertiary text-3xl" style="font-variation-settings: 'FILL' 1;">fiber_new</span>
          </div>
          <div>
            <p class="text-sm text-on-surface-variant font-medium">Pacientes nuevos</p>
            <p class="text-2xl font-bold text-tertiary">—</p>
          </div>
        </article>
      </section>

      <section class="bg-white/70 backdrop-blur-sm p-6 rounded-lg border border-outline-variant/20">
        <label class="sr-only" for="buscador">Buscar paciente</label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
          <input class="w-full rounded-full border border-outline-variant bg-surface-container-lowest py-3 pl-14 pr-5 text-base outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/30" id="buscador" type="text" placeholder="Buscar por nombre, motivo o estado..." />
        </div>
      </section>

      <section class="bg-surface-container-lowest rounded-lg atmospheric-shadow border border-outline-variant/30 overflow-hidden">
        @if ($pacientes->isEmpty())
          <div class="p-10 text-center">
            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-primary-fixed text-primary">
              <span class="material-symbols-outlined text-3xl">group_add</span>
            </div>
            <h2 class="text-xl font-bold text-primary">Aún no tienes pacientes vinculados.</h2>
            <p class="mt-2 text-on-surface-variant">Comparte tu PIN de vinculación para agregar pacientes a tu panel clínico.</p>
            <a class="mt-6 inline-flex items-center justify-center gap-2 rounded-full bg-primary px-6 py-3 font-bold text-on-primary hover:opacity-90" href="{{ url('/terapeuta') }}">
              <span class="material-symbols-outlined">vpn_key</span>
              Ver PIN
            </a>
          </div>
        @else
          <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] text-left border-collapse" aria-label="Tabla de pacientes">
              <thead>
                <tr class="bg-surface-container-low/70">
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Paciente</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Fecha de inicio</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Próxima cita</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Motivo de consulta</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase">Estado</th>
                  <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase text-center">Acciones</th>
                </tr>
              </thead>
              <tbody id="tablaPacientes" class="divide-y divide-outline-variant/30">
                @forelse ($pacientes as $paciente)
                  @php
                    $nombrePaciente = trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? ''));
                    $inicialesPaciente = collect(explode(' ', $nombrePaciente))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('');
                    $motivo = $paciente->motivo_terapia;
                    if ($motivo) {
                      try {
                        $motivo = Illuminate\Support\Facades\Crypt::decryptString($motivo);
                      } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                      }
                    }
                    $proximaCita = $proximasPorPaciente->get($paciente->id);
                  @endphp
                  <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-5">
                      <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-bold">{{ $inicialesPaciente ?: 'P' }}</div>
                        <div>
                          <a class="font-bold text-primary hover:underline" href="{{ url('/expediente/' . $paciente->id) }}">{{ $nombrePaciente ?: 'Paciente sin nombre' }}</a>
                          <p class="text-xs text-on-surface-variant">Exp: #{{ $paciente->id }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-on-surface">{{ $paciente->updated_at ? \Carbon\Carbon::parse($paciente->updated_at)->format('d/m/Y') : '—' }}</td>
                    <td class="px-6 py-5 whitespace-nowrap">
                      @if ($proximaCita)
                        @php($proximaCitaInicio = $proximaCita->starts_at ? \Carbon\Carbon::parse($proximaCita->starts_at)->setTimezone($proximaCita->timezone ?: 'America/Mexico_City') : \Carbon\Carbon::parse(($proximaCita->fecha ?? now()->toDateString()) . ' ' . ($proximaCita->hora ?: '00:00')))
                        <span class="inline-flex items-center gap-1 font-bold text-primary">
                          <span class="material-symbols-outlined text-[18px]">event</span>
                          {{ $proximaCitaInicio->format('d/m/Y') }} {{ $proximaCitaInicio->format('H:i') }}
                        </span>
                      @else
                        <span class="text-on-surface-variant">—</span>
                      @endif
                    </td>
                    <td class="px-6 py-5">
                      <span class="inline-flex max-w-xs rounded-full bg-secondary-container/60 px-3 py-1 text-sm text-on-secondary-container">{{ $motivo ?: 'Sin especificar' }}</span>
                    </td>
                    <td class="px-6 py-5">
                      <span class="inline-flex items-center gap-2 rounded-full bg-primary-fixed px-4 py-1 text-sm font-bold text-on-primary-fixed-variant">
                        <span class="h-2 w-2 rounded-full bg-primary"></span>
                        Activo
                      </span>
                    </td>
                    <td class="px-6 py-5">
                      <div class="flex items-center justify-center">
                        <a class="inline-flex h-10 w-10 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container-high hover:text-primary" href="{{ url('/expediente/' . $paciente->id) }}" aria-label="Ver expediente de {{ $nombrePaciente }}">
                          <span class="material-symbols-outlined">visibility</span>
                        </a>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td class="px-6 py-8 text-center text-on-surface-variant" colspan="6">Aún no tienes pacientes vinculados.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="border-t border-outline-variant/30 px-6 py-4">
            <p class="text-sm text-on-surface-variant">Mostrando {{ $pacientes->count() }} {{ $pacientes->count() === 1 ? 'paciente' : 'pacientes' }} vinculados.</p>
          </div>
        @endif
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
      const buscador = document.getElementById('buscador');
      const tabla = document.getElementById('tablaPacientes');

      if (!buscador || !tabla) {
        return;
      }

      buscador.addEventListener('input', () => {
        const query = buscador.value.toLowerCase().trim();
        tabla.querySelectorAll('tr').forEach((row) => {
          row.hidden = query !== '' && !row.innerText.toLowerCase().includes(query);
        });
      });
    });
  </script>

  @include('partials.marker-widget')
</body>
</html>
