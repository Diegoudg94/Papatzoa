<!DOCTYPE html>
<html class="light" lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Papatzoa - Diario de emociones</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            background: '#f7f9ff',
            primary: '#376457',
            'primary-container': '#507d6f',
            secondary: '#6f5b44',
            tertiary: '#804f48',
            'on-tertiary': '#ffffff',
            'on-primary': '#ffffff',
            'on-primary-container': '#f5fff9',
            ivory: '#fcfbf7',
            surface: '#f7f9ff',
            'surface-low': '#edf4ff',
            'surface-high': '#dfe9f7',
            'surface-bright': '#ffffff',
            'surface-container': '#e4effd',
            'surface-container-high': '#dfe9f7',
            'surface-container-lowest': '#ffffff',
            'on-surface': '#121d26',
            'on-surface-variant': '#404945',
            outline: '#717975',
            'outline-variant': '#c0c8c4',
            error: '#ba1a1a',
            'error-container': '#ffdad6',
          },
          fontFamily: {
            sans: ['Manrope', 'sans-serif'],
          },
          boxShadow: {
            atmospheric: '0 10px 40px -10px rgba(55, 100, 87, 0.12)',
          },
        },
      },
    };
  </script>
  <style>
    body {
      background: #fcfbf7;
      color: #121d26;
      font-family: 'Manrope', sans-serif;
    }

    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    .active-tab {
      font-variation-settings: 'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 24;
    }

    .active-tab-glow { position: relative; }
    .active-tab-glow::after { content: ''; position: absolute; left: 0; top: 25%; height: 50%; width: 4px; background: #376457; border-radius: 0 4px 4px 0; }

    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    .diario-step[hidden],
    .historial-details[hidden] {
      display: none;
    }

    .progress__step.is-active {
      background: #376457;
      color: #ffffff;
      opacity: 1;
    }

    .progress__step.is-done {
      background: rgba(55, 100, 87, 0.12);
      color: #376457;
      opacity: 1;
    }

    .chip.is-selected {
      border-color: #376457;
      background: rgba(55, 100, 87, 0.10);
      color: #376457;
    }

    .diario-range {
      accent-color: #376457;
    }

    .intensity-empty { background: #eef2f7; color: #404945; }
    .intensity-low { background: #e8f5e9; color: #2e7d32; }
    .intensity-mild { background: #f1f8e9; color: #558b2f; }
    .intensity-medium { background: #fff9c4; color: #8a6d00; }
    .intensity-high { background: #fff3e0; color: #b85c00; }
    .intensity-critical { background: #ffebee; color: #b71c1c; }
  </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-ivory">
@php
  $usuario = $usuario ?? \Illuminate\Support\Facades\DB::table('users')
    ->where('id', session('usuario_id'))
    ->first();

  $nombrePaciente = trim((string) ($usuario->nombre ?? session('usuario_nombre') ?? ''));
  $nombrePaciente = $nombrePaciente !== '' ? $nombrePaciente : 'Paciente';
  $apellidoPaciente = trim((string) ($usuario->apellido ?? session('usuario_apellido') ?? ''));
  $inicialesPaciente = strtoupper(substr($nombrePaciente, 0, 1) . substr($apellidoPaciente, 0, 1));
  $inicialesPaciente = trim($inicialesPaciente) !== '' ? $inicialesPaciente : 'P';
  $fotoPaciente = null;

  if (!empty($usuario->profile_photo_path)) {
    $fotoPaciente = asset('storage/' . ltrim($usuario->profile_photo_path, '/'));
  } elseif (!empty($usuario->avatar_url)) {
    $fotoPaciente = $usuario->avatar_url;
  }

  $totalRegistros = $emociones->count();
  $ultimaEmocion = $emociones->first();
  $registrosSemana = $emociones->filter(fn ($item) => $item->created_at && $item->created_at->gte(now()->startOfWeek()))->count();
  $intensidades = $emociones->pluck('intensidad')->filter(fn ($valor) => ! is_null($valor));
  $promedioIntensidad = $intensidades->count() ? round($intensidades->avg(), 1) : null;
  $ultimoSeguimiento = $emociones
    ->flatMap(fn ($item) => $item->seguimientos)
    ->sortByDesc('created_at')
    ->first();

  $summaryIntensityLabel = '—';
  if (! is_null($promedioIntensidad)) {
    if ($promedioIntensidad <= 2) {
      $summaryIntensityLabel = 'Baja';
    } elseif ($promedioIntensidad <= 4) {
      $summaryIntensityLabel = 'Leve';
    } elseif ($promedioIntensidad <= 6) {
      $summaryIntensityLabel = 'Media';
    } elseif ($promedioIntensidad <= 8) {
      $summaryIntensityLabel = 'Alta';
    } else {
      $summaryIntensityLabel = 'Muy alta';
    }
  }
@endphp

<aside class="hidden lg:flex fixed left-0 top-0 h-full w-[280px] bg-surface border-r border-outline-variant flex-col py-6 px-3 z-50 overflow-y-auto">
  <div class="mb-10 px-6">
    <a class="font-bold text-2xl text-primary" href="{{ url('/dashboard') }}">Papatzoa</a>
    <div class="mt-6 flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-secondary/20 overflow-hidden flex items-center justify-center text-secondary font-bold">
        @if ($fotoPaciente)
          <img class="w-full h-full object-cover" src="{{ $fotoPaciente }}" alt="Foto de {{ $nombrePaciente }}">
        @else
          {{ $inicialesPaciente }}
        @endif
      </div>
      <div>
        <h3 class="text-sm font-bold text-on-surface">{{ $nombrePaciente }}</h3>
        <p class="text-xs text-outline">Paciente</p>
      </div>
    </div>
  </div>

  <nav class="flex-1 space-y-1 px-1" aria-label="Navegación principal">
    <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="{{ url('/dashboard') }}">
      <span class="material-symbols-outlined">home</span>
      <span>Inicio</span>
    </a>
    <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-primary font-bold bg-primary-container/10 active-tab-glow" href="{{ url('/diario') }}" aria-current="page">
      <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">edit_note</span>
      <span>Diario emocional</span>
    </a>
    <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="{{ url('/citas') }}">
      <span class="material-symbols-outlined">calendar_today</span>
      <span>Mis citas</span>
    </a>
    <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="{{ url('/diario#historial') }}">
      <span class="material-symbols-outlined">insights</span>
      <span>Seguimiento</span>
    </a>
    <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="{{ url('/ayuda') }}">
      <span class="material-symbols-outlined">menu_book</span>
      <span>Recursos</span>
    </a>
    <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="{{ url('/dashboard#vincular-terapeuta') }}">
      <span class="material-symbols-outlined">psychology</span>
      <span>Mi terapeuta</span>
    </a>
    <button class="w-full flex items-center gap-3 px-6 py-3 rounded-2xl text-left text-outline cursor-not-allowed" type="button" disabled>
      <span class="material-symbols-outlined">person</span>
      <span>Mi cuenta</span>
    </button>
  </nav>

  <div class="mt-auto px-1 space-y-4">
    <a class="w-full py-3 bg-tertiary text-on-tertiary rounded-full font-bold flex items-center justify-center gap-2 hover:opacity-90" href="{{ url('/ayuda') }}">
      <span class="material-symbols-outlined text-lg">support_agent</span>
      Apoyo en crisis
    </a>
    <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="{{ url('/logout') }}">
      <span class="material-symbols-outlined">logout</span>
      <span>Cerrar sesión</span>
    </a>
  </div>
</aside>

<main class="min-h-screen w-full bg-ivory lg:ml-[280px] lg:w-[calc(100%_-_280px)]">
  <div class="mx-auto w-full max-w-[1400px] px-4 py-6 sm:px-6 lg:px-8 xl:px-10 lg:py-10">
    <header class="mb-8 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
      <div class="max-w-3xl">
        <div class="mb-4 flex items-center justify-between gap-4 lg:hidden">
          <a class="text-2xl font-bold text-primary" href="{{ url('/dashboard') }}">Papatzoa</a>
          <a class="inline-flex items-center gap-2 rounded-full bg-error-container/80 px-4 py-2 text-sm font-bold text-error" href="{{ url('/logout') }}">
            <span class="material-symbols-outlined text-base">logout</span>
            Salir
          </a>
        </div>
        <p class="mb-2 text-sm font-bold text-on-surface-variant">Hola, {{ $nombrePaciente }} 👋</p>
        <h2 class="text-3xl font-bold tracking-tight text-primary sm:text-4xl">Diario de emociones</h2>
        <p class="mt-3 text-base leading-7 text-on-surface-variant sm:text-lg">
          Registra, comprende y da seguimiento a lo que sientes.
          <span class="block font-semibold text-primary/75">Cada registro te ayuda a identificar patrones y avanzar en tu proceso terapéutico.</span>
        </p>
      </div>

      <button
        class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-primary px-6 py-3 text-sm font-bold text-white shadow-atmospheric transition hover:bg-primary/90 sm:w-auto"
        type="button"
        onclick="document.getElementById('emotionFormCard')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
      >
        <span class="material-symbols-outlined">add</span>
        Añadir nueva emoción
      </button>
    </header>

    <nav class="-mt-4 mb-8 flex gap-2 overflow-x-auto pb-2 lg:hidden no-scrollbar" aria-label="Navegación móvil">
      <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/dashboard') }}">Inicio</a>
      <a class="shrink-0 px-4 py-2 rounded-full bg-primary text-on-primary text-sm font-bold" href="{{ url('/diario') }}" aria-current="page">Diario</a>
      <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/citas') }}">Citas</a>
      <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/ayuda') }}">Recursos</a>
    </nav>

    <section class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Resumen del diario">
      <article class="rounded-3xl border border-outline-variant/20 bg-white p-5 shadow-atmospheric">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-on-surface-variant/70">Última emoción</p>
        <div class="mt-4 flex items-center gap-3">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
            <span class="material-symbols-outlined">sentiment_satisfied</span>
          </span>
          <div>
            <p class="text-xl font-bold text-primary">{{ $ultimaEmocion?->emocion ?: 'Sin registros' }}</p>
            <p class="text-xs text-on-surface-variant/70">
              {{ $ultimaEmocion?->created_at ? $ultimaEmocion->created_at->diffForHumans() : '—' }}
            </p>
          </div>
        </div>
      </article>

      <article class="rounded-3xl border border-outline-variant/20 bg-white p-5 shadow-atmospheric">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-on-surface-variant/70">Intensidad promedio</p>
        <div class="mt-4 flex items-center gap-3">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-secondary/10 text-secondary">
            <span class="material-symbols-outlined">insights</span>
          </span>
          <div>
            <p class="text-xl font-bold text-secondary">{{ $summaryIntensityLabel }}</p>
            <p class="text-xs text-on-surface-variant/70">{{ is_null($promedioIntensidad) ? '—' : $promedioIntensidad . '/10' }}</p>
          </div>
        </div>
      </article>

      <article class="rounded-3xl border border-outline-variant/20 bg-white p-5 shadow-atmospheric">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-on-surface-variant/70">Registros esta semana</p>
        <div class="mt-4 flex items-center gap-3">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-tertiary/10 text-tertiary">
            <span class="material-symbols-outlined">calendar_today</span>
          </span>
          <div>
            <p class="text-xl font-bold text-tertiary">{{ $registrosSemana }} registros</p>
            <p class="text-xs text-on-surface-variant/70">Semana actual</p>
          </div>
        </div>
      </article>

      <article class="rounded-3xl border border-outline-variant/20 bg-white p-5 shadow-atmospheric">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-on-surface-variant/70">Último seguimiento</p>
        <div class="mt-4 flex items-center gap-3">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
            <span class="material-symbols-outlined">verified</span>
          </span>
          <div>
            <p class="text-xl font-bold text-primary">{{ $ultimoSeguimiento?->created_at ? $ultimoSeguimiento->created_at->diffForHumans() : 'Sin registros' }}</p>
            <p class="text-xs text-on-surface-variant/70">{{ $totalRegistros }} registros totales</p>
          </div>
        </div>
      </article>
    </section>

    @if (session('success_diario'))
      <div class="mb-6 rounded-2xl border border-primary/20 bg-primary/10 px-5 py-4 text-sm font-semibold text-primary">
        {{ session('success_diario') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-6 rounded-2xl border border-error/20 bg-error-container/70 px-5 py-4 text-sm font-semibold text-error">
        Revisa los campos marcados antes de guardar.
      </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12 xl:gap-8 2xl:gap-10">
      <section class="space-y-8 xl:col-span-8 2xl:col-span-9">
        <article class="rounded-[2rem] border border-outline-variant/20 bg-white p-5 shadow-atmospheric sm:p-8" id="emotionFormCard">
          <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h3 class="text-2xl font-bold text-primary">Nuevo Registro Cognitivo</h3>
              <p class="mt-1 text-sm text-on-surface-variant">Completa los pasos para registrar una emoción.</p>
            </div>
            <div class="flex gap-1.5" aria-hidden="true">
              @for ($i = 1; $i <= 6; $i++)
                <span class="h-3 w-3 rounded-full {{ $i === 1 ? 'bg-primary' : 'bg-outline-variant/60' }}"></span>
              @endfor
            </div>
          </div>

          <form class="diario-form" method="POST" action="/diario">
            @csrf

            <div class="progress mb-7 flex gap-2 overflow-x-auto pb-2 no-scrollbar" aria-label="Pasos del registro emocional">
              <div class="progress__step is-active shrink-0 rounded-full bg-surface-low px-4 py-2 text-xs font-bold text-on-surface-variant opacity-70" data-step="1">1. Situación</div>
              <div class="progress__step shrink-0 rounded-full bg-surface-low px-4 py-2 text-xs font-bold text-on-surface-variant opacity-70" data-step="2">2. Pensamiento</div>
              <div class="progress__step shrink-0 rounded-full bg-surface-low px-4 py-2 text-xs font-bold text-on-surface-variant opacity-70" data-step="3">3. Emoción</div>
              <div class="progress__step shrink-0 rounded-full bg-surface-low px-4 py-2 text-xs font-bold text-on-surface-variant opacity-70" data-step="4">4. Conducta</div>
              <div class="progress__step shrink-0 rounded-full bg-surface-low px-4 py-2 text-xs font-bold text-on-surface-variant opacity-70" data-step="5">5. Interpretación</div>
              <div class="progress__step shrink-0 rounded-full bg-surface-low px-4 py-2 text-xs font-bold text-on-surface-variant opacity-70" data-step="6">6. Reestructuración</div>
            </div>

            <section class="diario-step scroll-mt-8" id="step1">
              <div class="space-y-4">
                <div>
                  <h2 class="text-xl font-bold text-on-surface">1) Situación o antecedente</h2>
                  <p class="mt-1 text-sm text-on-surface-variant">Describe brevemente qué ocurrió.</p>
                </div>
                <textarea class="min-h-36 w-full rounded-3xl border-outline-variant/60 bg-surface/40 p-4 text-base text-on-surface shadow-sm transition focus:border-primary focus:ring-primary/20" id="situacion" name="situacion" rows="4" placeholder="Ej: discutí con alguien...">{{ old('situacion') }}</textarea>
                @error('situacion') <p class="text-sm font-semibold text-error">{{ $message }}</p> @enderror
                <div class="flex justify-end pt-2">
                  <button class="inline-flex items-center gap-2 rounded-full bg-primary px-7 py-3 text-sm font-bold text-white transition hover:bg-primary/90" data-next="2" type="button">
                    Siguiente
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                  </button>
                </div>
              </div>
            </section>

            <section class="diario-step scroll-mt-8" id="step2" hidden>
              <div class="space-y-4">
                <div>
                  <h2 class="text-xl font-bold text-on-surface">2) Pensamiento automático</h2>
                  <p class="mt-1 text-sm text-on-surface-variant">¿Qué pasó por tu mente?</p>
                </div>
                <textarea class="min-h-36 w-full rounded-3xl border-outline-variant/60 bg-surface/40 p-4 text-base text-on-surface shadow-sm transition focus:border-primary focus:ring-primary/20" id="pensamiento" name="pensamiento" rows="4" placeholder="Ej: voy a fracasar">{{ old('pensamiento') }}</textarea>
                @error('pensamiento') <p class="text-sm font-semibold text-error">{{ $message }}</p> @enderror
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-between">
                  <button class="rounded-full border border-outline-variant/70 px-7 py-3 text-sm font-bold text-on-surface-variant transition hover:bg-surface-low" data-previous="1" type="button">Volver</button>
                  <button class="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-7 py-3 text-sm font-bold text-white transition hover:bg-primary/90" data-next="3" type="button">
                    Siguiente
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                  </button>
                </div>
              </div>
            </section>

            <section class="diario-step scroll-mt-8" id="step3" hidden>
              <div class="space-y-5">
                <div>
                  <h2 class="text-xl font-bold text-on-surface">3) Emoción</h2>
                  <p class="mt-1 text-sm text-on-surface-variant">¿Qué emoción apareció?</p>
                </div>
                <select class="w-full rounded-3xl border-outline-variant/60 bg-surface/40 p-4 text-base text-on-surface shadow-sm transition focus:border-primary focus:ring-primary/20" id="emocion" name="emocion" required>
                  <option value="">Selecciona emoción</option>
                  @foreach (['Ansiedad', 'Tristeza', 'Enojo', 'Miedo', 'Vergüenza', 'Culpa', 'Alegría'] as $opcion)
                    <option value="{{ $opcion }}" @selected(old('emocion') === $opcion)>{{ $opcion }}</option>
                  @endforeach
                </select>
                @error('emocion') <p class="text-sm font-semibold text-error">{{ $message }}</p> @enderror

                <label class="block text-sm font-bold text-on-surface-variant" for="intensidad">Intensidad: <strong id="intensidadValor">{{ old('intensidad', 5) }}</strong> /10</label>
                <input class="diario-range w-full" type="range" id="intensidad" name="intensidad" min="1" max="10" value="{{ old('intensidad', 5) }}" />
                @error('intensidad') <p class="text-sm font-semibold text-error">{{ $message }}</p> @enderror
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-between">
                  <button class="rounded-full border border-outline-variant/70 px-7 py-3 text-sm font-bold text-on-surface-variant transition hover:bg-surface-low" data-previous="2" type="button">Volver</button>
                  <button class="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-7 py-3 text-sm font-bold text-white transition hover:bg-primary/90" data-next="4" type="button">
                    Siguiente
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                  </button>
                </div>
              </div>
            </section>

            <section class="diario-step scroll-mt-8" id="step4" hidden>
              <div class="space-y-4">
                <div>
                  <h2 class="text-xl font-bold text-on-surface">4) Conducta o reacción</h2>
                  <p class="mt-1 text-sm text-on-surface-variant">¿Qué hiciste después?</p>
                </div>
                <textarea class="min-h-36 w-full rounded-3xl border-outline-variant/60 bg-surface/40 p-4 text-base text-on-surface shadow-sm transition focus:border-primary focus:ring-primary/20" id="conducta" name="conducta" rows="4" placeholder="Ej: me aislé, lloré...">{{ old('conducta') }}</textarea>
                @error('conducta') <p class="text-sm font-semibold text-error">{{ $message }}</p> @enderror
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-between">
                  <button class="rounded-full border border-outline-variant/70 px-7 py-3 text-sm font-bold text-on-surface-variant transition hover:bg-surface-low" data-previous="3" type="button">Volver</button>
                  <button class="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-7 py-3 text-sm font-bold text-white transition hover:bg-primary/90" data-next="5" type="button">
                    Siguiente
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                  </button>
                </div>
              </div>
            </section>

            <section class="diario-step scroll-mt-8" id="step5" hidden>
              <div class="space-y-5">
                <div>
                  <h2 class="text-xl font-bold text-on-surface">5) ¿Cómo estás interpretando esto?</h2>
                  <p class="mt-1 text-sm text-on-surface-variant">Selecciona las opciones que más se parezcan a lo que pensaste o sentiste.</p>
                </div>
                <input type="hidden" id="interpretacion" name="interpretacion" value="{{ old('interpretacion') }}">

                <div class="grid gap-3 sm:grid-cols-2">
                  <button class="chip rounded-3xl border border-outline-variant/60 bg-white p-4 text-left text-sm font-bold text-on-surface transition hover:border-primary/60" data-label="Catastrofización" type="button">Catastrofización <small class="mt-1 block font-medium text-on-surface-variant">Pensar que todo saldrá muy mal</small></button>
                  <button class="chip rounded-3xl border border-outline-variant/60 bg-white p-4 text-left text-sm font-bold text-on-surface transition hover:border-primary/60" data-label="Lectura de mente" type="button">Lectura de mente <small class="mt-1 block font-medium text-on-surface-variant">Asumir lo que otros piensan</small></button>
                  <button class="chip rounded-3xl border border-outline-variant/60 bg-white p-4 text-left text-sm font-bold text-on-surface transition hover:border-primary/60" data-label="Generalización" type="button">Generalización <small class="mt-1 block font-medium text-on-surface-variant">Creer que siempre pasa lo mismo</small></button>
                  <button class="chip rounded-3xl border border-outline-variant/60 bg-white p-4 text-left text-sm font-bold text-on-surface transition hover:border-primary/60" data-label="Blanco / negro" type="button">Blanco / negro <small class="mt-1 block font-medium text-on-surface-variant">Ver todo como éxito o fracaso</small></button>
                  <button class="chip rounded-3xl border border-outline-variant/60 bg-white p-4 text-left text-sm font-bold text-on-surface transition hover:border-primary/60 sm:col-span-2" data-label="Personalización" type="button">Personalización <small class="mt-1 block font-medium text-on-surface-variant">Culparte automáticamente</small></button>
                </div>
                @error('interpretacion') <p class="text-sm font-semibold text-error">{{ $message }}</p> @enderror
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-between">
                  <button class="rounded-full border border-outline-variant/70 px-7 py-3 text-sm font-bold text-on-surface-variant transition hover:bg-surface-low" data-previous="4" type="button">Volver</button>
                  <button class="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-7 py-3 text-sm font-bold text-white transition hover:bg-primary/90" data-next="6" type="button">
                    Siguiente
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                  </button>
                </div>
              </div>
            </section>

            <section class="diario-step scroll-mt-8" id="step6" hidden>
              <div class="space-y-4">
                <div>
                  <h2 class="text-xl font-bold text-on-surface">6) Reestructuración cognitiva</h2>
                  <p class="mt-1 text-sm text-on-surface-variant">Escribe una interpretación más equilibrada.</p>
                </div>
                <textarea class="min-h-40 w-full rounded-3xl border-outline-variant/60 bg-surface/40 p-4 text-base text-on-surface shadow-sm transition focus:border-primary focus:ring-primary/20" id="reestructuracion" name="reestructuracion" rows="5" placeholder="Ej: tal vez no fue personal...">{{ old('reestructuracion') }}</textarea>
                @error('reestructuracion') <p class="text-sm font-semibold text-error">{{ $message }}</p> @enderror
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-between">
                  <button class="rounded-full border border-outline-variant/70 px-7 py-3 text-sm font-bold text-on-surface-variant transition hover:bg-surface-low" data-previous="5" type="button">Volver</button>
                  <button class="rounded-full bg-primary px-7 py-3 text-sm font-bold text-white transition hover:bg-primary/90" type="submit">Guardar emoción</button>
                </div>
              </div>
            </section>
          </form>
        </article>

        <section class="space-y-4 scroll-mt-8" id="historial">
          <div class="flex flex-col gap-1 px-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <h3 class="text-2xl font-bold text-primary">Historial reciente</h3>
              <p class="text-sm text-on-surface-variant">Registros ordenados del más reciente al más antiguo.</p>
            </div>
          </div>

          <div class="space-y-4">
            @forelse ($emociones as $emocion)
              @php
                $intensity = is_null($emocion->intensidad) ? null : (int) $emocion->intensidad;
                $intensityClass = 'intensity-empty';
                $intensityLabel = 'Sin información';
                $intensityText = 'Sin información';

                if (! is_null($intensity)) {
                  if ($intensity <= 2) {
                    $intensityClass = 'intensity-low';
                    $intensityLabel = 'Baja';
                  } elseif ($intensity <= 4) {
                    $intensityClass = 'intensity-mild';
                    $intensityLabel = 'Leve';
                  } elseif ($intensity <= 6) {
                    $intensityClass = 'intensity-medium';
                    $intensityLabel = 'Media';
                  } elseif ($intensity <= 8) {
                    $intensityClass = 'intensity-high';
                    $intensityLabel = 'Alta';
                  } else {
                    $intensityClass = 'intensity-critical';
                    $intensityLabel = 'Muy alta';
                  }

                  $intensityText = $intensity . '/10 · ' . $intensityLabel;
                }

                $situacion = $emocion->situacion ?: 'Sin información';
              @endphp
              <article class="rounded-3xl border border-transparent bg-white p-5 shadow-atmospheric transition hover:border-primary/30">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                  <div class="flex gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                      <span class="material-symbols-outlined">mood</span>
                    </div>
                    <div>
                      <h4 class="text-base font-extrabold text-on-surface">{{ $emocion->emocion ?: 'Sin información' }}</h4>
                      <p class="mt-1 text-xs font-semibold text-on-surface-variant/70">{{ $emocion->created_at->format('d/m/Y') }} · {{ $emocion->created_at->format('H:i') }}</p>
                    </div>
                  </div>
                  <span class="{{ $intensityClass }} inline-flex w-fit rounded-full px-4 py-1.5 text-xs font-extrabold uppercase tracking-[0.14em]">{{ $intensityText }}</span>
                </div>

                <div class="mt-5 rounded-2xl bg-surface/50 p-4">
                  <strong class="text-sm text-on-surface">Situación</strong>
                  <p class="mt-1 text-sm leading-6 text-on-surface-variant">{{ \Illuminate\Support\Str::limit($situacion, 150) }}</p>
                </div>

                <button
                  class="details-toggle mt-4 inline-flex items-center gap-2 rounded-full px-1 py-2 text-sm font-bold text-primary transition hover:text-primary/80"
                  type="button"
                  aria-expanded="false"
                  aria-controls="emocion-detalle-{{ $emocion->id }}"
                  data-target="emocion-detalle-{{ $emocion->id }}"
                >
                  Ver más detalles
                </button>

                <div class="historial-details mt-4 border-t border-outline-variant/30 pt-5" id="emocion-detalle-{{ $emocion->id }}" hidden>
                  <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-surface/50 p-4"><strong class="text-sm text-on-surface">Pensamiento automático</strong><p class="mt-1 text-sm leading-6 text-on-surface-variant">{{ $emocion->pensamiento ?: 'Sin información' }}</p></div>
                    <div class="rounded-2xl bg-surface/50 p-4"><strong class="text-sm text-on-surface">Conducta</strong><p class="mt-1 text-sm leading-6 text-on-surface-variant">{{ $emocion->conducta ?: 'Sin información' }}</p></div>
                    <div class="rounded-2xl bg-surface/50 p-4"><strong class="text-sm text-on-surface">Interpretación</strong><p class="mt-1 text-sm leading-6 text-on-surface-variant">{{ $emocion->interpretacion ?: 'Sin información' }}</p></div>
                    <div class="rounded-2xl bg-surface/50 p-4"><strong class="text-sm text-on-surface">Reestructuración</strong><p class="mt-1 text-sm leading-6 text-on-surface-variant">{{ $emocion->reestructuracion ?: 'Sin información' }}</p></div>
                  </div>

                  <div class="mt-6 rounded-3xl border border-outline-variant/30 bg-white p-4">
                    <div class="mb-4 flex items-center gap-2 text-primary">
                      <span class="material-symbols-outlined">forum</span>
                      <h4 class="font-bold">Seguimientos anteriores</h4>
                    </div>

                    <div class="space-y-3">
                      @forelse ($emocion->seguimientos as $seguimiento)
                        <div class="rounded-2xl bg-primary/5 p-4">
                          <p class="text-sm leading-6 text-on-surface-variant">{{ $seguimiento->nota ?: 'Sin información' }}</p>
                          <span class="mt-2 block text-xs font-semibold text-on-surface-variant/70">{{ $seguimiento->created_at->format('d/m/Y') }} · {{ $seguimiento->created_at->format('H:i') }}</span>
                        </div>
                      @empty
                        <p class="rounded-2xl bg-surface/50 p-4 text-sm text-on-surface-variant">Sin información</p>
                      @endforelse
                    </div>

                    <form class="mt-5 space-y-3" id="seguimiento-{{ $emocion->id }}" method="POST" action="/diario/{{ $emocion->id }}/seguimiento">
                      @csrf
                      <label class="block text-sm font-bold text-on-surface-variant" for="nota-{{ $emocion->id }}">Nueva nota de seguimiento</label>
                      <textarea class="min-h-28 w-full rounded-3xl border-outline-variant/60 bg-surface/40 p-4 text-sm text-on-surface shadow-sm transition focus:border-primary focus:ring-primary/20" id="nota-{{ $emocion->id }}" name="nota" rows="3" maxlength="2000" required></textarea>
                      <button class="rounded-full bg-primary px-6 py-3 text-sm font-bold text-white transition hover:bg-primary/90" type="submit">Guardar seguimiento</button>
                    </form>
                  </div>
                </div>
              </article>
            @empty
              <div class="rounded-[2rem] border border-dashed border-primary/30 bg-white p-8 text-center shadow-atmospheric">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary/10 text-primary">
                  <span class="material-symbols-outlined">edit_note</span>
                </span>
                <h3 class="mt-4 text-xl font-bold text-primary">Aún no has registrado emociones.</h3>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-on-surface-variant">Tu primer registro puede ayudarte a reconocer patrones emocionales.</p>
                <button
                  class="mt-5 rounded-full bg-primary px-6 py-3 text-sm font-bold text-white transition hover:bg-primary/90"
                  type="button"
                  onclick="document.getElementById('emotionFormCard')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                >
                  Crear primer registro
                </button>
              </div>
            @endforelse
          </div>
        </section>
      </section>

      <aside class="space-y-6 xl:col-span-4 2xl:col-span-3">
        <section class="rounded-[2rem] border border-primary/10 bg-primary/5 p-6">
          <div class="mb-4 flex items-center gap-3 text-primary">
            <span class="material-symbols-outlined">lightbulb</span>
            <h4 class="text-xl font-bold">Tips de journaling</h4>
          </div>
          <ul class="space-y-4 text-sm leading-6 text-on-surface-variant">
            <li class="flex gap-3"><span class="font-bold text-primary">•</span><span>Escribe en el momento en que sientas la emoción.</span></li>
            <li class="flex gap-3"><span class="font-bold text-primary">•</span><span>No te preocupes por la gramática, deja fluir tus ideas.</span></li>
            <li class="flex gap-3"><span class="font-bold text-primary">•</span><span>Sé específico: ¿dónde lo sentiste en tu cuerpo?</span></li>
          </ul>
        </section>

        <section class="relative overflow-hidden rounded-[2rem] border border-outline-variant/20 bg-white p-6 shadow-atmospheric">
          <span class="mb-3 inline-block rounded-full bg-secondary/10 px-3 py-1 text-xs font-extrabold uppercase tracking-[0.16em] text-secondary">Recurso recomendado</span>
          <h4 class="text-xl font-bold text-on-surface">Respiración 4-7-8</h4>
          <p class="mt-2 text-sm leading-6 text-on-surface-variant">Una técnica rápida para calmar el sistema nervioso en momentos de ansiedad.</p>
          <a class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-full bg-secondary px-5 py-3 text-sm font-bold text-white transition hover:bg-secondary/90" href="{{ url('/ayuda') }}">
            <span class="material-symbols-outlined">play_circle</span>
            Comenzar ejercicio
          </a>
        </section>

        <section class="rounded-r-3xl border-l-4 border-primary/30 bg-surface-low/70 p-5">
          <div class="mb-2 flex items-center gap-2 text-on-surface-variant">
            <span class="material-symbols-outlined text-base">lock</span>
            <span class="text-xs font-extrabold uppercase tracking-[0.16em]">Privacidad</span>
          </div>
          <p class="text-sm italic leading-6 text-on-surface-variant">
            Tus registros sensibles se almacenan cifrados. Puedes compartirlos con tu terapeuta dentro de la plataforma.
          </p>
        </section>

        <section class="rounded-[2rem] border border-outline-variant/20 bg-white p-6 shadow-atmospheric">
          <h5 class="mb-4 text-sm font-extrabold text-on-surface">Sistema de intensidad</h5>
          <div class="space-y-3">
            <div class="flex items-center justify-between gap-4 text-xs font-semibold"><span>Muy baja</span><div class="h-2 w-24 rounded-full bg-[#e8f5e9]"></div></div>
            <div class="flex items-center justify-between gap-4 text-xs font-semibold"><span>Baja</span><div class="h-2 w-24 rounded-full bg-[#a5d6a7]"></div></div>
            <div class="flex items-center justify-between gap-4 text-xs font-semibold"><span>Media</span><div class="h-2 w-24 rounded-full bg-[#fff59d]"></div></div>
            <div class="flex items-center justify-between gap-4 text-xs font-semibold"><span>Alta</span><div class="h-2 w-24 rounded-full bg-[#ffcc80]"></div></div>
            <div class="flex items-center justify-between gap-4 text-xs font-semibold"><span>Muy alta</span><div class="h-2 w-24 rounded-full bg-[#ef9a9a]"></div></div>
          </div>
        </section>
      </aside>
    </div>

    <footer class="mt-14 flex flex-col gap-4 border-t border-outline-variant/20 pt-8 text-sm text-on-surface-variant sm:flex-row sm:items-center sm:justify-between">
      <p class="font-semibold opacity-70">© {{ date('Y') }} Papatzoa. Tu bienestar es nuestra prioridad.</p>
      <div class="flex gap-5">
        <a class="font-semibold transition hover:text-primary" href="{{ url('/ayuda') }}">Privacidad</a>
        <a class="font-semibold transition hover:text-primary" href="{{ url('/ayuda') }}">Términos</a>
        <a class="font-semibold transition hover:text-primary" href="{{ url('/ayuda') }}">Contacto</a>
      </div>
    </footer>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const steps = Array.from(document.querySelectorAll('.diario-step'));
  const progressSteps = Array.from(document.querySelectorAll('.progress__step'));
  const range = document.getElementById('intensidad');
  const rangeValue = document.getElementById('intensidadValor');
  const interpretacion = document.getElementById('interpretacion');

  function showStep(number) {
    steps.forEach((step, index) => {
      step.hidden = index + 1 !== number;
    });

    progressSteps.forEach((step, index) => {
      step.classList.toggle('is-active', index + 1 === number);
      step.classList.toggle('is-done', index + 1 < number);
    });

    document.getElementById(`step${number}`).scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  document.querySelectorAll('[data-next]').forEach((button) => {
    button.addEventListener('click', () => {
      const nextStep = Number(button.dataset.next);

      if (nextStep === 4 && !document.getElementById('emocion').value) {
        document.getElementById('emocion').reportValidity();
        return;
      }

      showStep(nextStep);
    });
  });

  document.querySelectorAll('[data-previous]').forEach((button) => {
    button.addEventListener('click', () => {
      showStep(Number(button.dataset.previous));
    });
  });

  if (range && rangeValue) {
    range.addEventListener('input', () => {
      rangeValue.textContent = range.value;
    });
  }

  document.querySelectorAll('.chip[data-label]').forEach((chip) => {
    chip.addEventListener('click', () => {
      chip.classList.toggle('is-selected');
      const selected = Array.from(document.querySelectorAll('.chip[data-label].is-selected'))
        .map((item) => item.dataset.label);
      interpretacion.value = selected.join(', ');
    });
  });

  document.querySelectorAll('.details-toggle').forEach((button) => {
    button.addEventListener('click', () => {
      const details = document.getElementById(button.dataset.target);
      const isOpening = details.hidden;

      details.hidden = !isOpening;
      button.setAttribute('aria-expanded', String(isOpening));
      button.textContent = isOpening ? 'Ocultar detalles' : 'Ver más detalles';

      if (isOpening) {
        details.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    });
  });
});
</script>

@include('partials.marker-widget')
</body>
</html>
