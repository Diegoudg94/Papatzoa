@php
  use Carbon\Carbon;

  $therapistInitials = collect(explode(' ', $therapistName ?? 'T'))
    ->filter()
    ->take(2)
    ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
    ->implode('');
  $therapistModalities = $therapistAvailableModalities ?? [];
  $therapistModalidadLabel = match ($terapeuta?->modalidad_atencion ?? null) {
    'online' => 'Online',
    'presencial' => 'Presencial',
    'hibrida', 'híbrida' => 'Híbrida',
    default => 'Modalidad por definir',
  };

  $nombrePaciente = trim((string) ($usuario->nombre ?? session('usuario_nombre') ?? ''));
  $nombrePaciente = $nombrePaciente !== '' ? $nombrePaciente : 'Paciente';
  $apellidoPaciente = trim((string) ($usuario->apellido ?? ''));
  $inicialesPaciente = strtoupper(substr($nombrePaciente, 0, 1) . substr($apellidoPaciente, 0, 1));
  $inicialesPaciente = trim($inicialesPaciente) !== '' ? $inicialesPaciente : 'P';
  $fotoPaciente = null;

  if (!empty($usuario->profile_photo_path)) {
      $fotoPaciente = asset('storage/' . ltrim($usuario->profile_photo_path, '/'));
  } elseif (!empty($usuario->avatar_url)) {
      $fotoPaciente = $usuario->avatar_url;
  }

  $nombreCompletoTerapeuta = null;
  $inicialesTerapeuta = $therapistInitials ?: 'T';
  $fotoTerapeuta = $therapistAvatarUrl ?? null;
  $modalidadAtencion = $terapeuta->modalidad_atencion ?? null;
  $telefonoCompleto = '';
  $direccionCompleta = '';
  $mostrarLugarAtencion = false;
  $badgeLabel = 'No enviada';
  $badgeClass = 'bg-surface-container text-outline';

  if ($terapeuta) {
      $nombreCompletoTerapeuta = trim(($terapeuta->nombre ?? '') . ' ' . ($terapeuta->apellido ?? ''));
      $nombreCompletoTerapeuta = $nombreCompletoTerapeuta !== '' ? $nombreCompletoTerapeuta : 'Terapeuta';
      $inicialesTerapeuta = strtoupper(substr($terapeuta->nombre ?? 'T', 0, 1) . substr($terapeuta->apellido ?? '', 0, 1));

      if (!empty($terapeuta->profile_photo_path)) {
          $fotoTerapeuta = asset('storage/' . ltrim($terapeuta->profile_photo_path, '/'));
      } elseif (!empty($terapeuta->avatar_url)) {
          $fotoTerapeuta = $terapeuta->avatar_url;
      }

      $estadoVerificacion = strtolower((string) ($terapeuta->estado_verificacion ?? ''));

      if ((bool) ($terapeuta->terapeuta_verificado ?? false) || in_array($estadoVerificacion, ['aprobado', 'aprobada', 'verificado', 'verificada'], true)) {
          $badgeLabel = 'Aprobado / Verificado';
          $badgeClass = 'bg-primary-fixed text-on-primary-fixed';
      } elseif (in_array($estadoVerificacion, ['rechazado', 'rechazada'], true)) {
          $badgeLabel = 'Rechazado';
          $badgeClass = 'bg-error-container text-on-error-container';
      } elseif (in_array($estadoVerificacion, ['pendiente', 'en_revision', 'en revisión'], true)) {
          $badgeLabel = 'Pendiente';
          $badgeClass = 'bg-secondary-fixed text-on-secondary-fixed';
      }

      $telefonoCompleto = trim(($terapeuta->telefono_lada ?? '') . ' ' . ($terapeuta->telefono ?? ''));
      $modalidadNormalizada = strtolower((string) $modalidadAtencion);
      $mostrarLugarAtencion = in_array($modalidadNormalizada, ['presencial', 'hibrida', 'híbrida'], true);
      $direccionCompleta = implode(', ', array_filter([
          $terapeuta->direccion_atencion ?? null,
          $terapeuta->ciudad_atencion ?? null,
          $terapeuta->estado_atencion ?? null,
          $terapeuta->pais_atencion ?? null,
          $terapeuta->codigo_postal_atencion ?? null,
      ]));
  }
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Papatzoa | Mis citas</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "primary-container": "#507d6f",
            "on-surface-variant": "#404945",
            "on-surface": "#121d26",
            "surface-variant": "#d9e3f1",
            "tertiary-fixed-dim": "#f8b7ad",
            "primary": "#376457",
            "on-background": "#121d26",
            "outline": "#717975",
            "on-error": "#ffffff",
            "surface-container-high": "#dfe9f7",
            "tertiary-container": "#9c675f",
            "on-secondary-fixed-variant": "#56442e",
            "surface": "#f7f9ff",
            "on-secondary": "#ffffff",
            "on-primary-container": "#f5fff9",
            "surface-bright": "#f7f9ff",
            "inverse-surface": "#27313c",
            "outline-variant": "#c0c8c4",
            "on-tertiary-fixed": "#34100c",
            "on-primary-fixed-variant": "#204f42",
            "secondary-fixed": "#fadec1",
            "surface-dim": "#d1dbe8",
            "surface-container-lowest": "#ffffff",
            "tertiary": "#804f48",
            "surface-container-low": "#edf4ff",
            "tertiary-fixed": "#ffdad5",
            "secondary-fixed-dim": "#dcc2a6",
            "on-tertiary": "#ffffff",
            "primary-fixed": "#bceddb",
            "surface-container-highest": "#d9e3f1",
            "error-container": "#ffdad6",
            "error": "#ba1a1a",
            "background": "#f7f9ff",
            "inverse-on-surface": "#e8f2ff",
            "on-error-container": "#93000a",
            "on-secondary-container": "#735f48",
            "on-primary-fixed": "#002018",
            "on-primary": "#ffffff",
            "secondary-container": "#f7dbbe",
            "primary-fixed-dim": "#a0d1c0",
            "on-tertiary-container": "#fffbff",
            "inverse-primary": "#a0d1c0",
            "surface-tint": "#396759",
            "secondary": "#6f5b44",
            "on-tertiary-fixed-variant": "#683a34",
            "surface-container": "#e4effd",
            "on-secondary-fixed": "#271907"
          },
          borderRadius: {
            DEFAULT: "1rem",
            lg: "2rem",
            xl: "3rem",
            full: "9999px"
          },
          fontFamily: {
            manrope: ["Manrope", "sans-serif"]
          }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Manrope', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .atmospheric-shadow { box-shadow: 0 12px 48px -12px rgba(55, 100, 87, 0.12); }
    .active-tab-glow { position: relative; }
    .active-tab-glow::after { content: ''; position: absolute; left: 0; top: 25%; height: 50%; width: 4px; background: #376457; border-radius: 0 4px 4px 0; }
    body.modal-open { position: fixed; width: 100%; overflow: hidden; }
    .therapist-modal { position: fixed; inset: 0; z-index: 80; display: none; }
    .therapist-modal.is-open { display: block; }
    .therapist-modal__overlay { position: absolute; inset: 0; background: rgba(18, 29, 38, 0.5); backdrop-filter: blur(4px); }
    .therapist-modal__content { position: relative; max-height: calc(100vh - 48px); overflow-y: auto; width: min(720px, calc(100% - 32px)); margin: 24px auto; border-radius: 28px; background: #ffffff; padding: 28px; box-shadow: 0 24px 80px rgba(18, 29, 38, 0.22); }
    .therapist-modal__close { position: absolute; top: 18px; right: 18px; width: 40px; height: 40px; border-radius: 999px; background: #edf4ff; color: #376457; font-size: 26px; line-height: 1; }
    .slot-days {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 12px;
      margin-top: 14px;
    }
    .slot-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 12px;
    }
    .slot-button {
      border: 1px solid #c0c8c4;
      background: #fff;
      border-radius: 18px;
      padding: 14px 16px;
      font-weight: 800;
      color: #404945;
      cursor: pointer;
      text-align: left;
      transition: border-color .2s, background-color .2s, color .2s;
    }
    .slot-button:hover,
    .slot-button.is-selected {
      border-color: #376457;
      background: #e4effd;
      color: #204f42;
    }
    .slot-day-button small {
      display: block;
      margin-top: 4px;
      font-size: 0.85rem;
      font-weight: 700;
      color: #717975;
    }
    .slot-empty,
    .slot-loading,
    .slot-error {
      padding: 18px;
      border-radius: 18px;
      background: #f7f9ff;
      border: 1px dashed #c0c8c4;
      color: #717975;
      font-weight: 700;
    }
    .slot-error {
      background: #fff7ed;
      border-color: #fdba74;
      color: #9a3412;
    }
    .selected-slot-summary { display: none; }
    @media (max-width: 640px) {
      .slot-days {
        display: flex;
        overflow-x: auto;
        padding-bottom: 4px;
      }
      .slot-days .slot-button {
        min-width: 148px;
        flex: 0 0 auto;
      }
    }
  </style>
</head>

<body class="bg-background text-on-background min-h-screen">
  <aside class="hidden lg:flex fixed left-0 top-0 h-full w-[280px] bg-surface border-r border-outline-variant flex-col py-6 px-3 z-50 overflow-y-auto">
    <div class="mb-10 px-6">
      <a class="font-bold text-2xl text-primary" href="{{ url('/dashboard') }}">Papatzoa</a>
      <div class="mt-6 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-secondary-container overflow-hidden flex items-center justify-center text-on-secondary-container font-bold">
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
      <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="{{ url('/diario') }}">
        <span class="material-symbols-outlined">edit_note</span>
        <span>Diario emocional</span>
      </a>
      <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-primary font-bold bg-primary-container/10 active-tab-glow" href="{{ url('/citas') }}">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">calendar_today</span>
        <span>Mis citas</span>
      </a>
      <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="{{ url('/diario') }}">
        <span class="material-symbols-outlined">insights</span>
        <span>Seguimiento</span>
      </a>
      <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="{{ url('/ayuda') }}">
        <span class="material-symbols-outlined">menu_book</span>
        <span>Recursos</span>
      </a>
      @if ($terapeuta)
        <button class="w-full flex items-center gap-3 px-6 py-3 rounded-2xl text-left text-on-surface-variant hover:bg-surface-container-high" type="button" data-open-therapist-modal>
          <span class="material-symbols-outlined">psychology</span>
          <span>Mi terapeuta</span>
        </button>
      @else
        <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="#solicitar-cita">
          <span class="material-symbols-outlined">psychology</span>
          <span>Mi terapeuta</span>
        </a>
      @endif
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

  <main class="min-h-screen lg:ml-[280px]">
    <header class="sticky top-0 z-40 bg-surface/85 backdrop-blur-md border-b border-outline-variant/60">
      <div class="flex justify-between items-center w-full px-4 sm:px-6 lg:px-12 py-4 gap-4">
        <div>
          <p class="text-xs uppercase tracking-[0.18em] text-outline font-bold lg:hidden">Papatzoa</p>
          <h1 class="text-xl sm:text-2xl text-primary font-bold">Hola, {{ $nombrePaciente }} 👋</h1>
        </div>
        <div class="flex items-center gap-2 sm:gap-4">
          <a class="hidden sm:inline-flex bg-primary text-on-primary px-5 py-3 rounded-full text-sm font-bold hover:opacity-90" href="#solicitar-cita">
            Solicitar cita
          </a>
          <a class="p-3 rounded-full hover:bg-surface-container text-on-surface-variant" href="{{ url('/logout') }}" aria-label="Cerrar sesión">
            <span class="material-symbols-outlined">logout</span>
          </a>
        </div>
      </div>
      <nav class="lg:hidden overflow-x-auto px-4 pb-3 flex gap-2" aria-label="Navegación móvil">
        <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/dashboard') }}">Inicio</a>
        <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/diario') }}">Diario emocional</a>
        <a class="shrink-0 px-4 py-2 rounded-full bg-primary text-on-primary text-sm font-bold" href="{{ url('/citas') }}">Mis citas</a>
        <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/ayuda') }}">Recursos</a>
      </nav>
    </header>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-12 py-8 space-y-8">
      @foreach (['success_cita' => 'bg-green-50 text-green-800 border-green-200', 'error_cita' => 'bg-red-50 text-red-800 border-red-200'] as $flashKey => $flashClass)
        @if (session($flashKey))
          <div class="{{ $flashClass }} border px-5 py-4 rounded-2xl font-semibold">
            {{ session($flashKey) }}
          </div>
        @endif
      @endforeach

      @if ($errors->any())
        <div class="bg-red-50 text-red-800 border border-red-200 px-5 py-4 rounded-2xl">
          <p class="font-bold">Revisa la información ingresada.</p>
          <ul class="mt-2 list-disc pl-5">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <section class="bg-surface-container-lowest atmospheric-shadow rounded-[2rem] p-6 lg:p-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
          <div>
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-outline">Mis citas</p>
            <h2 class="text-3xl font-bold text-on-surface mt-2">Solicitudes y citas</h2>
            <p class="text-on-surface-variant mt-2 max-w-3xl">
              Aquí puedes consultar tus citas solicitadas, confirmadas o rechazadas por tu terapeuta.
            </p>
          </div>
          <a class="inline-flex px-5 py-3 rounded-full bg-primary text-on-primary font-bold hover:opacity-90" href="#solicitar-cita">
            Nueva cita
          </a>
        </div>

        @if ($citas->isEmpty())
          <div class="border border-dashed border-outline-variant rounded-2xl p-8 text-center bg-surface">
            <h3 class="text-lg font-bold">No tienes citas solicitadas o agendadas.</h3>
            <p class="text-on-surface-variant mt-2">
              Cuando envíes una solicitud, aparecerá aquí con el estatus correspondiente.
            </p>
          </div>
        @else
          <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            @foreach ($citas as $cita)
              @php
                $estadoClase = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú'], ['-', 'a', 'e', 'i', 'o', 'u'], $cita->estado));
                $inicioCita = $cita->starts_at
                  ? Carbon::parse($cita->starts_at)->setTimezone($cita->timezone ?: 'America/Mexico_City')
                  : Carbon::parse(($cita->fecha ?? now()->toDateString()) . ' ' . ($cita->hora ?: '00:00'));
                $estado = strtolower($cita->estado);

                if (in_array($estado, ['aceptada', 'aceptado', 'confirmada', 'confirmado'], true)) {
                    $statusStyle = 'background:#dcfce7; color:#166534; border:1px solid #86efac;';
                } elseif ($estado === 'rechazada' || $estado === 'rechazado') {
                    $statusStyle = 'background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;';
                } elseif ($estado === 'cancelada' || $estado === 'cancelado') {
                    $statusStyle = 'background:#e5e7eb; color:#374151; border:1px solid #cbd5e1;';
                } elseif ($estado === 'completada' || $estado === 'completado') {
                    $statusStyle = 'background:#dbeafe; color:#1d4ed8; border:1px solid #93c5fd;';
                } else {
                    $statusStyle = 'background:#fff7ed; color:#c2410c; border:1px solid #fed7aa;';
                }
              @endphp
              <article class="appointment-item appointment-item--{{ $estadoClase ?? strtolower($cita->estado) }} rounded-[1.75rem] border border-outline-variant/40 bg-surface p-6">
                <div class="appointment-info grid gap-4">
                  <div class="flex items-start justify-between gap-4 pb-4 border-b border-outline-variant/40">
                    <div>
                      <h3 class="text-xl font-bold text-on-surface">Cita con {{ $therapistName ?? 'tu terapeuta' }}</h3>
                      <p class="text-sm text-outline mt-1">{{ $inicioCita->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full px-4 py-2 text-sm font-extrabold" style="{{ $statusStyle }}">
                      {{ ucfirst($cita->estado) }}
                    </span>
                  </div>

                  <div class="grid sm:grid-cols-2 gap-3 text-sm text-on-surface-variant">
                    <div class="rounded-2xl bg-surface-container-low p-4">
                      <p class="text-outline font-bold uppercase tracking-[0.14em] text-[11px]">Fecha</p>
                      <p class="mt-2 text-on-surface font-semibold">{{ $inicioCita->format('d/m/Y') }}</p>
                    </div>
                    <div class="rounded-2xl bg-surface-container-low p-4">
                      <p class="text-outline font-bold uppercase tracking-[0.14em] text-[11px]">Hora</p>
                      <p class="mt-2 text-on-surface font-semibold">{{ $inicioCita->format('H:i') }}</p>
                    </div>
                    @if (!empty($cita->modalidad))
                      <div class="rounded-2xl bg-surface-container-low p-4">
                        <p class="text-outline font-bold uppercase tracking-[0.14em] text-[11px]">Modalidad</p>
                        <p class="mt-2 text-on-surface font-semibold">{{ ucfirst($cita->modalidad) }}</p>
                      </div>
                    @endif
                    <div class="rounded-2xl bg-surface-container-low p-4">
                      <p class="text-outline font-bold uppercase tracking-[0.14em] text-[11px]">Terapeuta</p>
                      <p class="mt-2 text-on-surface font-semibold">{{ $therapistName ?? 'tu terapeuta' }}</p>
                    </div>
                  </div>

                  <div class="rounded-2xl bg-surface-container-low p-4">
                    <p class="text-outline font-bold uppercase tracking-[0.14em] text-[11px]">Tema a tratar</p>
                    <p class="mt-2 text-on-surface leading-7">
                      @php
                        $motivoCifrado = $cita->motivo_encrypted ?? $cita->motivo ?? null;
                        try {
                            echo $motivoCifrado ? \Illuminate\Support\Facades\Crypt::decryptString($motivoCifrado) : 'Sin registro';
                        } catch (\Exception $e) {
                            echo 'No se pudo mostrar este dato.';
                        }
                      @endphp
                    </p>
                  </div>
                </div>
              </article>
            @endforeach
          </div>
        @endif
      </section>

      <section class="bg-surface-container-lowest atmospheric-shadow rounded-[2rem] p-6 lg:p-8" id="solicitar-cita">
        <div class="mb-6">
          <p class="text-sm font-bold uppercase tracking-[0.18em] text-outline">Nueva solicitud</p>
          <h2 class="text-3xl font-bold text-on-surface mt-2">Solicitar nueva cita</h2>
        </div>

        <form action="/citas/solicitar" method="POST" id="appointment-request-form">
          @csrf

          <fieldset class="grid gap-6">
            <legend class="sr-only">Solicitar nueva cita</legend>

            @if (!$terapeuta)
              <div class="border border-dashed border-outline-variant rounded-2xl p-8 text-center bg-surface">
                <h3 class="text-lg font-bold">Aún no tienes terapeuta vinculado.</h3>
                <p class="text-on-surface-variant mt-2">Necesitas vincularte con un terapeuta antes de solicitar una cita.</p>
              </div>
            @else
              <div class="grid gap-4 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
                <div class="rounded-[1.75rem] border border-primary-fixed/50 bg-primary-fixed/30 p-5">
                  <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-secondary-container overflow-hidden flex items-center justify-center text-on-secondary-container text-lg font-extrabold shrink-0">
                      @if ($therapistAvatarUrl)
                        <img src="{{ $therapistAvatarUrl }}" alt="Foto de {{ $therapistName }}" class="w-full h-full object-cover">
                      @else
                        {{ $therapistInitials ?: 'T' }}
                      @endif
                    </div>
                    <div class="min-w-0">
                      <p class="text-sm text-outline font-bold">Terapeuta vinculado</p>
                      <h3 class="text-xl font-bold text-on-surface mt-1">{{ $therapistName }}</h3>
                      <p class="text-on-surface-variant mt-1">Zona horaria: {{ $availabilityTimezone }}</p>
                    </div>
                    <strong class="sm:ml-auto inline-flex items-center justify-center rounded-full bg-surface px-4 py-2 text-sm font-extrabold text-primary">
                      {{ $therapistModalidadLabel }}
                    </strong>
                  </div>
                </div>

                <div class="grid sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3 gap-3">
                  <div class="rounded-2xl border border-outline-variant/40 bg-surface p-4">
                    <span class="block text-outline text-[11px] font-bold uppercase tracking-[0.14em]">Duración</span>
                    <p class="mt-2 font-semibold text-on-surface">{{ $availabilityDurationMinutes }} minutos</p>
                  </div>
                  <div class="rounded-2xl border border-outline-variant/40 bg-surface p-4">
                    <span class="block text-outline text-[11px] font-bold uppercase tracking-[0.14em]">Zona horaria</span>
                    <p class="mt-2 font-semibold text-on-surface">{{ $availabilityTimezone }}</p>
                  </div>
                  <div class="rounded-2xl border border-outline-variant/40 bg-surface p-4">
                    <span class="block text-outline text-[11px] font-bold uppercase tracking-[0.14em]">Modalidad</span>
                    <p class="mt-2 font-semibold text-on-surface">{{ $therapistModalidadLabel }}</p>
                  </div>
                </div>
              </div>

              <input type="hidden" name="start" id="selected-start" value="{{ old('start') }}">
              <input type="hidden" name="end" id="selected-end" value="{{ old('end') }}">

              <div class="grid gap-6 xl:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)]">
                <div class="grid gap-6">
                  <div class="rounded-[1.5rem] border border-outline-variant/40 bg-surface p-5">
                    <label class="block text-base font-bold text-on-surface">Elige un día disponible</label>
                    <p class="mt-2 text-sm text-on-surface-variant">Mostramos los próximos 14 días con horarios reales disponibles de tu terapeuta.</p>
                    <div id="slot-days" class="slot-days">
                      <div class="slot-loading">Cargando fechas disponibles...</div>
                    </div>
                  </div>

                  <div class="rounded-[1.5rem] border border-outline-variant/40 bg-surface p-5">
                    <label class="block text-base font-bold text-on-surface">Elige un horario</label>
                    <div id="slot-grid" class="slot-grid mt-4">
                      <div class="slot-empty">Selecciona una fecha para ver horarios.</div>
                    </div>
                  </div>

                  <div id="selected-slot-summary" class="selected-slot-summary rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-extrabold text-green-700"></div>
                </div>

                <div class="rounded-[1.5rem] border border-outline-variant/40 bg-surface p-5 space-y-5">
                  <div>
                    <label class="block text-sm font-bold text-on-surface mb-2" for="modalidad">Modalidad</label>
                    @if (count($therapistModalities) > 1)
                      <select class="w-full rounded-2xl border-outline-variant bg-surface-container-low px-4 py-3 focus:border-primary focus:ring-primary" id="modalidad" name="modalidad" required>
                        <option value="">Selecciona una modalidad</option>
                        @foreach ($therapistModalities as $modalidad)
                          <option value="{{ $modalidad }}" @selected(old('modalidad') === $modalidad)>{{ ucfirst($modalidad) }}</option>
                        @endforeach
                      </select>
                    @elseif (count($therapistModalities) === 1)
                      <input type="hidden" name="modalidad" value="{{ $therapistModalities[0] }}">
                      <input class="w-full rounded-2xl border-outline-variant bg-surface-container-low px-4 py-3 text-on-surface-variant" type="text" value="{{ ucfirst($therapistModalities[0]) }}" disabled>
                    @else
                      <input class="w-full rounded-2xl border-outline-variant bg-surface-container-low px-4 py-3 text-on-surface-variant" type="text" value="Por definir" disabled>
                    @endif
                  </div>

                  <div>
                    <label class="block text-sm font-bold text-on-surface mb-2" for="motivo">Motivo / temas a tratar</label>
                    <textarea
                      class="w-full min-h-[180px] rounded-2xl border-outline-variant bg-surface-container-low px-4 py-3 focus:border-primary focus:ring-primary"
                      id="motivo"
                      name="motivo"
                      rows="5"
                      placeholder="Ej: ansiedad, estrés, conflictos personales..."
                      required
                    >{{ old('motivo') }}</textarea>
                  </div>

                  <button class="w-full rounded-full bg-primary px-5 py-3 text-base font-bold text-on-primary hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50" type="submit" id="submit-appointment" disabled>
                    Solicitar cita
                  </button>
                </div>
              </div>
            @endif
          </fieldset>
        </form>
      </section>
    </div>
  </main>

  @if ($terapeuta)
    <div class="therapist-modal" id="therapistModal" role="dialog" aria-modal="true" aria-labelledby="therapistModalTitle" aria-hidden="true">
      <div class="therapist-modal__overlay" data-close-therapist-modal></div>
      <div class="therapist-modal__content" tabindex="-1">
        <button type="button" class="therapist-modal__close" id="closeTherapistModal" aria-label="Cerrar perfil del terapeuta">&times;</button>
        <h2 class="text-3xl font-bold text-on-surface pr-12" id="therapistModalTitle">Perfil del terapeuta</h2>

        <div class="mt-6 flex flex-col sm:flex-row gap-5 sm:items-center">
          <div class="w-24 h-24 rounded-full bg-primary-fixed text-primary overflow-hidden flex items-center justify-center text-2xl font-bold">
            @if ($fotoTerapeuta)
              <img class="w-full h-full object-cover" src="{{ $fotoTerapeuta }}" alt="Foto de {{ $nombreCompletoTerapeuta }}">
            @else
              {{ $inicialesTerapeuta }}
            @endif
          </div>
          <div>
            <h3 class="text-2xl font-bold">{{ $nombreCompletoTerapeuta }}</h3>
            @if (!empty($terapeuta->correo))
              <p class="text-on-surface-variant">{{ $terapeuta->correo }}</p>
            @endif
            <span class="inline-flex mt-3 px-4 py-2 rounded-full text-sm font-bold {{ $badgeClass }}">{{ $badgeLabel }}</span>
          </div>
        </div>

        <div class="mt-8 grid sm:grid-cols-2 gap-4">
          @if (!empty($terapeuta->especialidad))
            <div class="p-4 rounded-2xl bg-surface-container">
              <span class="text-xs uppercase text-outline font-bold">Especialidad</span>
              <strong class="block mt-1">{{ $terapeuta->especialidad }}</strong>
            </div>
          @endif
          @if (!empty($terapeuta->experiencia_anios))
            <div class="p-4 rounded-2xl bg-surface-container">
              <span class="text-xs uppercase text-outline font-bold">Años de experiencia</span>
              <strong class="block mt-1">{{ $terapeuta->experiencia_anios }}</strong>
            </div>
          @endif
          @if (!empty($modalidadAtencion))
            <div class="p-4 rounded-2xl bg-surface-container">
              <span class="text-xs uppercase text-outline font-bold">Modalidad de terapia</span>
              <strong class="block mt-1">{{ $modalidadAtencion }}</strong>
            </div>
          @endif
          @if (!empty($terapeuta->nacionalidad))
            <div class="p-4 rounded-2xl bg-surface-container">
              <span class="text-xs uppercase text-outline font-bold">Nacionalidad</span>
              <strong class="block mt-1">{{ $terapeuta->nacionalidad }}</strong>
            </div>
          @endif
          @if ($telefonoCompleto !== '')
            <div class="p-4 rounded-2xl bg-surface-container">
              <span class="text-xs uppercase text-outline font-bold">Teléfono de contacto</span>
              <strong class="block mt-1">{{ $telefonoCompleto }}</strong>
            </div>
          @endif
          @if (!empty($terapeuta->cedula_profesional))
            <div class="p-4 rounded-2xl bg-surface-container">
              <span class="text-xs uppercase text-outline font-bold">Cédula profesional</span>
              <strong class="block mt-1">{{ $terapeuta->cedula_profesional }}</strong>
            </div>
          @endif
          @if (!empty($terapeuta->institucion_formacion))
            <div class="p-4 rounded-2xl bg-surface-container">
              <span class="text-xs uppercase text-outline font-bold">Institución de formación</span>
              <strong class="block mt-1">{{ $terapeuta->institucion_formacion }}</strong>
            </div>
          @endif
          @if (!empty($terapeuta->enfoque_terapeutico))
            <div class="p-4 rounded-2xl bg-surface-container">
              <span class="text-xs uppercase text-outline font-bold">Enfoque terapéutico</span>
              <strong class="block mt-1">{{ $terapeuta->enfoque_terapeutico }}</strong>
            </div>
          @endif
        </div>

        @if (!empty($terapeuta->biografia))
          <div class="mt-4 p-4 rounded-2xl bg-surface-container">
            <span class="text-xs uppercase text-outline font-bold">Biografía</span>
            <p class="mt-2 text-on-surface-variant">{{ $terapeuta->biografia }}</p>
          </div>
        @endif

        <div class="mt-4 p-4 rounded-2xl bg-surface-container">
          @if ($mostrarLugarAtencion)
            <span class="text-xs uppercase text-outline font-bold">Lugar de atención</span>
            <p class="mt-2 text-on-surface-variant">{{ $direccionCompleta !== '' ? $direccionCompleta : 'No especificado' }}</p>
          @else
            <span class="text-xs uppercase text-outline font-bold">Atención en línea</span>
            <p class="mt-2 text-on-surface-variant">Sesiones disponibles en modalidad online.</p>
          @endif
        </div>
      </div>
    </div>
  @endif

@if ($terapeuta)
<script>
  const availabilityEndpoint = @json(route('patient.appointments.availability'));
  const appointmentForm = document.getElementById('appointment-request-form');
  const slotDaysEl = document.getElementById('slot-days');
  const slotGridEl = document.getElementById('slot-grid');
  const selectedStartInput = document.getElementById('selected-start');
  const selectedEndInput = document.getElementById('selected-end');
  const selectedSummary = document.getElementById('selected-slot-summary');
  const submitButton = document.getElementById('submit-appointment');
  const slotsByDate = new Map();
  let selectedDateKey = null;

  function toYmd(date) {
    return new Intl.DateTimeFormat('en-CA', {
      timeZone: 'America/Mexico_City',
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
    }).format(date);
  }

  function formatDateLabel(dateKey) {
    const date = new Date(`${dateKey}T12:00:00`);
    return date.toLocaleDateString('es-MX', { weekday: 'short', day: 'numeric', month: 'short' });
  }

  function updateSubmitState() {
    submitButton.disabled = !(selectedStartInput.value && selectedEndInput.value);
  }

  function clearSelectedSlot() {
    selectedStartInput.value = '';
    selectedEndInput.value = '';
    selectedSummary.style.display = 'none';
    selectedSummary.textContent = '';
    slotGridEl.querySelectorAll('.slot-button').forEach((button) => button.classList.remove('is-selected'));
    updateSubmitState();
  }

  function renderDayState(message, className = 'slot-empty') {
    slotDaysEl.innerHTML = `<div class="${className}">${message}</div>`;
    slotGridEl.innerHTML = '<div class="slot-empty">Sin horarios para mostrar.</div>';
    clearSelectedSlot();
  }

  function renderSlots(dateKey) {
    selectedDateKey = dateKey;
    clearSelectedSlot();

    const slots = slotsByDate.get(dateKey) || [];

    if (!slots.length) {
      slotGridEl.innerHTML = '<div class="slot-empty">No hay horarios disponibles para este día.</div>';
      return;
    }

    slotGridEl.innerHTML = '';

    slots.forEach((slot) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'slot-button';
      button.textContent = slot.label;
      button.setAttribute('role', 'radio');
      button.setAttribute('aria-checked', 'false');

      button.addEventListener('click', () => {
        slotGridEl.querySelectorAll('.slot-button').forEach((item) => {
          item.classList.remove('is-selected');
          item.setAttribute('aria-checked', 'false');
        });

        button.classList.add('is-selected');
        button.setAttribute('aria-checked', 'true');
        selectedStartInput.value = slot.start;
        selectedEndInput.value = slot.end;
        selectedSummary.textContent = `Horario seleccionado: ${formatDateLabel(dateKey)} · ${slot.label}`;
        selectedSummary.style.display = 'block';
        updateSubmitState();
      });

      slotGridEl.appendChild(button);
    });
  }

  function renderDays(slots) {
    slotsByDate.clear();
    clearSelectedSlot();

    slots.forEach((slot) => {
      const key = slot.date || String(slot.start).slice(0, 10);
      if (!slotsByDate.has(key)) {
        slotsByDate.set(key, []);
      }
      slotsByDate.get(key).push(slot);
    });

    if (!slotsByDate.size) {
      renderDayState('No hay horarios disponibles en los próximos 14 días.');
      return;
    }

    slotDaysEl.innerHTML = '';

    Array.from(slotsByDate.entries()).forEach(([dateKey, daySlots], index) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'slot-button slot-day-button';
      button.innerHTML = `${formatDateLabel(dateKey)}<small>${daySlots.length} horario${daySlots.length === 1 ? '' : 's'}</small>`;

      button.addEventListener('click', () => {
        slotDaysEl.querySelectorAll('.slot-button').forEach((item) => item.classList.remove('is-selected'));
        button.classList.add('is-selected');
        renderSlots(dateKey);
      });

      slotDaysEl.appendChild(button);

      if (index === 0) {
        button.classList.add('is-selected');
        renderSlots(dateKey);
      }
    });
  }

  async function loadAvailability() {
    slotDaysEl.innerHTML = '<div class="slot-loading">Cargando fechas disponibles...</div>';
    slotGridEl.innerHTML = '<div class="slot-empty">Selecciona una fecha para ver horarios.</div>';

    const from = new Date();
    const to = new Date();
    to.setDate(to.getDate() + 14);

    const params = new URLSearchParams({
      from: toYmd(from),
      to: toYmd(to),
    });

    try {
      const response = await fetch(`${availabilityEndpoint}?${params.toString()}`, {
        headers: { Accept: 'application/json' },
      });

      const data = await response.json().catch(() => ({}));

      if (!response.ok) {
        if (response.status === 422 && data.message) {
          renderDayState(data.message);
          return;
        }

        if (response.status === 404 && data.message) {
          renderDayState(data.message);
          return;
        }

        throw new Error('availability_failed');
      }

      if (data.availability_active === false) {
        renderDayState(data.message || 'Tu terapeuta todavía no ha habilitado horarios para reservar.');
        return;
      }

      if (!Array.isArray(data.slots) || !data.slots.length) {
        renderDayState(data.message || 'No hay horarios disponibles en los próximos 14 días.');
        return;
      }

      renderDays(data.slots);
    } catch (error) {
      renderDayState('No pudimos cargar la disponibilidad. Intenta nuevamente.', 'slot-error');
    }
  }

  appointmentForm.addEventListener('submit', (event) => {
    if (!selectedStartInput.value || !selectedEndInput.value) {
      event.preventDefault();
      selectedSummary.textContent = 'Selecciona un horario disponible antes de enviar la solicitud.';
      selectedSummary.style.display = 'block';
      updateSubmitState();
    }
  });

  updateSubmitState();
  loadAvailability();

  const modal = document.getElementById('therapistModal');
  const openButtons = document.querySelectorAll('[data-open-therapist-modal]');
  const closeBtn = document.getElementById('closeTherapistModal');
  const overlay = modal?.querySelector('.therapist-modal__overlay');
  let scrollY = 0;

  function openModal() {
    if (!modal) return;
    scrollY = window.scrollY || document.documentElement.scrollTop;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
    document.body.style.top = `-${scrollY}px`;
    modal.querySelector('.therapist-modal__content')?.focus();
  }

  function closeModal() {
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    document.body.style.top = '';
    window.scrollTo(0, scrollY);
  }

  openButtons.forEach((button) => {
    button.addEventListener('click', openModal);
  });

  closeBtn?.addEventListener('click', closeModal);
  overlay?.addEventListener('click', closeModal);
  overlay?.addEventListener('wheel', function (event) {
    event.preventDefault();
  }, { passive: false });
  overlay?.addEventListener('touchmove', function (event) {
    event.preventDefault();
  }, { passive: false });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && modal?.classList.contains('is-open')) {
      closeModal();
    }
  });
</script>
@endif

@include('partials.marker-widget')
</body>
</html>
