@php
  use App\Models\DiarioEmocion;
  use Carbon\Carbon;
  use Illuminate\Support\Facades\Crypt;
  use Illuminate\Support\Facades\DB;

  $descifrar = function (?string $valor): ?string {
      if (!$valor) {
          return null;
      }

      try {
          return Crypt::decryptString($valor);
      } catch (\Throwable) {
          return 'No se pudo mostrar este dato.';
      }
  };

  $formatearFecha = function ($valor, string $formato, string $fallback = 'Sin fecha') {
      if (!$valor) {
          return $fallback;
      }

      try {
          $fecha = $valor instanceof \Carbon\CarbonInterface ? $valor : Carbon::parse($valor);

          return $fecha->translatedFormat($formato);
      } catch (\Throwable) {
          return $fallback;
      }
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

  $citas = DB::table('citas')
      ->where('paciente_id', $usuario->id)
      ->orderByRaw('COALESCE(starts_at, fecha::timestamp) ASC')
      ->orderBy('hora', 'asc')
      ->get();

  $ahora = Carbon::now();
  $citaInicio = function ($cita): ?Carbon {
      try {
          return $cita->starts_at
              ? Carbon::parse($cita->starts_at)->setTimezone($cita->timezone ?: 'America/Mexico_City')
              : Carbon::parse(($cita->fecha ?? '') . ' ' . ($cita->hora ?? '00:00'));
      } catch (\Throwable) {
          return null;
      }
  };

  $proximaCita = $citas->first(function ($cita) use ($ahora, $citaInicio) {
      $estado = strtolower((string) ($cita->estado ?? ''));

      if (in_array($estado, ['rechazada', 'rechazado', 'cancelada', 'cancelado'], true)) {
          return false;
      }

      return $citaInicio($cita)?->greaterThanOrEqualTo($ahora) ?? false;
  });

  $proximasCitas = $citas->filter(function ($cita) use ($ahora, $citaInicio) {
      $estado = strtolower((string) ($cita->estado ?? ''));

      if (in_array($estado, ['rechazada', 'rechazado', 'cancelada', 'cancelado'], true)) {
          return false;
      }

      return $citaInicio($cita)?->greaterThanOrEqualTo($ahora) ?? false;
  })->take(4);

  $emociones = DiarioEmocion::with(['seguimientos' => function ($query) {
          $query->orderBy('created_at', 'desc');
      }])
      ->where('user_id', $usuario->id)
      ->orderBy('created_at', 'desc')
      ->take(7)
      ->get()
      ->map(function (DiarioEmocion $emocion) use ($descifrar) {
          $emocion->situacion = $descifrar($emocion->situacion_encrypted);
          $emocion->pensamiento = $descifrar($emocion->pensamiento_encrypted);
          $emocion->conducta = $descifrar($emocion->conducta_encrypted);
          $emocion->interpretacion = $descifrar($emocion->interpretacion_encrypted);
          $emocion->reestructuracion = $descifrar($emocion->reestructuracion_encrypted);

          return $emocion;
      });

  $ultimoRegistro = $emociones->first();
  $emocionesGrafica = $emociones->reverse()->values();
  $ultimasEmociones = $emociones->take(3)->values();

  $nombreCompletoTerapeuta = null;
  $inicialesTerapeuta = 'T';
  $fotoTerapeuta = null;
  $modalidadAtencion = null;
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
      $modalidadAtencion = $terapeuta->modalidad_atencion ?? null;
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
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>Papatzoa | Dashboard de Bienestar</title>
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
          <h3 class="text-sm font-bold text-on-surface">{{ $nombrePaciente }} 👋</h3>
          <p class="text-xs text-outline">Paciente</p>
        </div>
      </div>
    </div>

    <nav class="flex-1 space-y-1 px-1" aria-label="Navegación principal">
      <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-primary font-bold bg-primary-container/10 active-tab-glow" href="{{ url('/dashboard') }}">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">home</span>
        <span>Inicio</span>
      </a>
      <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="{{ url('/diario') }}">
        <span class="material-symbols-outlined">edit_note</span>
        <span>Diario emocional</span>
      </a>
      <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="{{ url('/citas') }}">
        <span class="material-symbols-outlined">calendar_today</span>
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
        <a class="flex items-center gap-3 px-6 py-3 rounded-2xl text-on-surface-variant hover:bg-surface-container-high" href="#vincular-terapeuta">
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
      <div class="flex justify-between items-center w-full px-4 sm:px-6 lg:px-12 py-4">
        <div>
          <p class="text-xs uppercase tracking-[0.18em] text-outline font-bold lg:hidden">Papatzoa</p>
          <h1 class="text-xl sm:text-2xl text-primary font-bold">Hola, {{ $nombrePaciente }} 👋</h1>
        </div>
        <div class="flex items-center gap-2 sm:gap-4">
          <a class="hidden sm:inline-flex bg-primary text-on-primary px-5 py-3 rounded-full text-sm font-bold hover:opacity-90" href="{{ url('/diario') }}">
            Registrar emoción
          </a>
          <a class="p-3 rounded-full hover:bg-surface-container text-on-surface-variant" href="{{ url('/logout') }}" aria-label="Cerrar sesión">
            <span class="material-symbols-outlined">logout</span>
          </a>
        </div>
      </div>
      <nav class="lg:hidden overflow-x-auto px-4 pb-3 flex gap-2" aria-label="Navegación móvil">
        <a class="shrink-0 px-4 py-2 rounded-full bg-primary text-on-primary text-sm font-bold" href="{{ url('/dashboard') }}">Inicio</a>
        <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/diario') }}">Diario</a>
        <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/citas') }}">Citas</a>
        <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/ayuda') }}">Recursos</a>
      </nav>
    </header>

    <div class="px-4 sm:px-6 lg:px-12 py-8 lg:py-12 max-w-[1400px] mx-auto space-y-8 lg:space-y-12">
      @foreach (['success_vinculacion' => 'bg-green-50 text-green-800 border-green-200', 'error_vinculacion' => 'bg-red-50 text-red-800 border-red-200', 'success_cita' => 'bg-green-50 text-green-800 border-green-200', 'error_cita' => 'bg-red-50 text-red-800 border-red-200', 'success_diario' => 'bg-green-50 text-green-800 border-green-200'] as $flashKey => $flashClass)
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

      <section class="relative overflow-hidden bg-primary-container text-on-primary-container rounded-[2rem] p-6 sm:p-8 lg:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="z-10 relative space-y-6 md:w-3/5">
          <h2 class="text-3xl sm:text-4xl lg:text-5xl leading-tight font-bold">Bienvenido nuevamente a tu espacio de bienestar emocional</h2>
          <p class="text-base sm:text-lg opacity-90 max-w-2xl">Tómate un momento para conectar contigo mismo hoy. Cada pequeño paso cuenta en tu camino de sanación.</p>
          <a class="w-fit bg-secondary-fixed text-on-secondary-fixed font-bold px-7 py-4 rounded-full flex items-center gap-3 hover:scale-[1.02] active:scale-95 transition-transform" href="{{ url('/diario') }}">
            <span class="material-symbols-outlined">mood</span>
            Registrar emoción de hoy
          </a>
        </div>
        <div class="z-10 w-40 h-40 sm:w-56 sm:h-56 rounded-full bg-white/10 flex items-center justify-center">
          <span class="material-symbols-outlined text-[88px] text-white/50">spa</span>
        </div>
        <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -top-20 -left-20 w-64 h-64 bg-primary/20 rounded-full blur-3xl"></div>
      </section>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="bg-surface-container-lowest atmospheric-shadow p-6 rounded-[2rem] flex flex-col gap-6">
          <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-primary-container/10 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined">event_available</span>
              </div>
              <h3 class="text-2xl font-bold">Próxima sesión</h3>
            </div>
            @if ($proximaCita)
              <span class="px-4 py-1 bg-primary-fixed text-on-primary-fixed rounded-full text-xs font-bold">{{ ucfirst($proximaCita->estado ?? 'Pendiente') }}</span>
            @endif
          </div>

          @if ($proximaCita)
            @php
              $proximaCitaInicio = $citaInicio($proximaCita);
            @endphp
            <div class="bg-surface p-5 rounded-2xl border border-outline-variant/40">
              <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                  <p class="text-primary font-bold">{{ $proximaCitaInicio?->translatedFormat('l, d F Y') }}</p>
                  <p class="text-on-surface">{{ $proximaCitaInicio?->format('H:i') }}</p>
                  <div class="flex items-center gap-1 mt-2 text-outline text-sm">
                    <span class="material-symbols-outlined text-base">videocam</span>
                    <span>{{ $modalidadAtencion ? 'Modalidad ' . $modalidadAtencion : 'Modalidad por confirmar' }}</span>
                  </div>
                </div>
                <div class="sm:text-right">
                  <p class="text-xs uppercase tracking-wider text-outline font-bold">Terapeuta</p>
                  <p class="font-semibold">{{ $nombreCompletoTerapeuta ?? 'Por vincular' }}</p>
                </div>
              </div>
            </div>
          @else
            <div class="bg-surface p-5 rounded-2xl border border-dashed border-outline-variant">
              <p class="text-lg font-bold">No tienes citas próximas</p>
              <p class="text-on-surface-variant mt-1">Puedes solicitar o agendar una nueva sesión.</p>
            </div>
          @endif

          <div class="flex flex-col sm:flex-row gap-3 mt-auto">
            <a class="flex-1 py-3 border border-primary text-primary rounded-full font-bold text-center hover:bg-primary/5" href="{{ url('/citas') }}">Ver detalles</a>
            <a class="flex-1 py-3 bg-primary text-on-primary rounded-full font-bold text-center hover:opacity-90" href="{{ url('/citas') }}">Agendar otra</a>
          </div>
        </section>

        <section class="bg-surface-container-lowest atmospheric-shadow p-6 rounded-[2rem] flex flex-col gap-6">
          <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-secondary-container/40 text-secondary flex items-center justify-center">
                <span class="material-symbols-outlined">timeline</span>
              </div>
              <h3 class="text-2xl font-bold">Seguimiento emocional</h3>
            </div>
            <span class="text-outline text-sm font-bold">Últimos registros</span>
          </div>

          @if (isset($emociones) && $emociones->isNotEmpty())
            <div class="bg-surface-container rounded-2xl p-5">
              <div class="w-full h-36 flex items-end justify-between gap-3">
                @foreach ($ultimasEmociones as $emocion)
                  @php
                    $intensidad = (int) ($emocion->intensidad ?? 0);
                    $altura = max(15, min(100, $intensidad * 10));
                    $fechaEmocion = $formatearFecha($emocion->created_at ?? null, 'd M');
                  @endphp

                  <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-primary-container/40 rounded-t-full flex items-end" style="height: 120px;">
                      <div class="w-full bg-primary rounded-t-full" style="height: {{ $altura }}%;"></div>
                    </div>
                    <span class="text-[10px] text-outline font-bold">{{ $fechaEmocion }}</span>
                  </div>
                @endforeach
              </div>

              <div class="mt-4 grid gap-2">
                @foreach ($ultimasEmociones as $emocion)
                  <div class="flex items-center justify-between gap-3 text-sm">
                    <span class="font-semibold text-on-surface">{{ $emocion->emocion ?? 'Emoción sin nombre' }}</span>
                    <span class="text-outline">{{ (int) ($emocion->intensidad ?? 0) }}/10</span>
                  </div>
                @endforeach
              </div>
            </div>
          @else
            <div class="bg-surface-container rounded-2xl p-6 text-center">
              <p class="font-bold text-on-surface">Aún no hay registros emocionales para mostrar una tendencia.</p>
              <a class="inline-flex mt-4 px-5 py-3 bg-primary text-on-primary rounded-full font-bold" href="{{ url('/diario') }}">Registrar emoción</a>
            </div>
          @endif
        </section>
      </div>

      <section class="bg-surface-container-lowest atmospheric-shadow p-6 lg:p-8 rounded-[2rem]">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
          <div>
            <h3 class="text-3xl font-bold">Mis sesiones</h3>
            <p class="text-on-surface-variant mt-1">Agenda de citas reales registradas en tu cuenta.</p>
          </div>
          <a class="px-5 py-3 bg-primary text-on-primary rounded-full font-bold" href="{{ url('/citas') }}">Solicitar cita</a>
        </div>

        @if ($proximasCitas->isEmpty())
          <div class="border border-dashed border-outline-variant rounded-2xl p-8 text-center">
            <p class="text-lg font-bold">No tienes sesiones próximas.</p>
            <p class="text-on-surface-variant mt-1">Cuando tu terapeuta confirme una cita, aparecerá en esta agenda.</p>
          </div>
        @else
          <div class="grid gap-4">
            @foreach ($proximasCitas as $cita)
              @php
                $citaInicioItem = $citaInicio($cita);
              @endphp
              <article class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 rounded-2xl border border-outline-variant/40 bg-surface">
                <div class="flex items-start gap-4">
                  <div class="w-14 h-14 rounded-2xl bg-primary-fixed text-on-primary-fixed flex flex-col items-center justify-center font-bold">
                    <span class="text-xs">{{ $citaInicioItem?->translatedFormat('M') }}</span>
                    <span class="text-lg leading-none">{{ $citaInicioItem?->format('d') }}</span>
                  </div>
                  <div>
                    <h4 class="font-bold text-lg">Sesión con {{ $nombreCompletoTerapeuta ?? 'tu terapeuta' }}</h4>
                    <p class="text-on-surface-variant">{{ $citaInicioItem?->translatedFormat('l, d F Y') }} · {{ $citaInicioItem?->format('H:i') }}</p>
                  </div>
                </div>
                <span class="w-fit px-4 py-2 rounded-full bg-surface-container text-primary font-bold text-sm">{{ ucfirst($cita->estado ?? 'Pendiente') }}</span>
              </article>
            @endforeach
          </div>
        @endif
      </section>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <article class="bg-surface-container-lowest atmospheric-shadow p-6 rounded-[2rem] flex flex-col gap-4">
          <div class="h-28 rounded-2xl bg-primary-fixed text-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-[56px]">air</span>
          </div>
          <h4 class="text-2xl font-bold">Ejercicios de respiración</h4>
          <p class="text-on-surface-variant">Técnicas breves para regular ansiedad y tensión durante el día.</p>
          <a class="mt-auto text-primary font-bold" href="{{ url('/ayuda') }}">Ver recursos</a>
        </article>

        <article class="bg-surface-container-lowest atmospheric-shadow p-6 rounded-[2rem] flex flex-col gap-4">
          <div class="h-28 rounded-2xl bg-secondary-container text-on-secondary-container flex items-center justify-center">
            <span class="material-symbols-outlined text-[56px]">edit_note</span>
          </div>
          <h4 class="text-2xl font-bold">Guía de journaling</h4>
          <p class="text-on-surface-variant">Preguntas simples para observar pensamientos, emociones y conductas.</p>
          <a class="mt-auto text-primary font-bold" href="{{ url('/diario') }}">Ir al diario</a>
        </article>

        <section class="bg-surface-container-lowest atmospheric-shadow p-6 rounded-[2rem] flex flex-col items-center text-center gap-5 relative" id="vincular-terapeuta">
          @if ($terapeuta)
            <span class="absolute top-5 right-5 px-4 py-1 bg-primary-fixed text-on-primary-fixed rounded-full text-xs font-bold flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-primary"></span>
              Vinculado
            </span>
            <div class="w-24 h-24 rounded-full border-4 border-surface bg-primary-fixed overflow-hidden flex items-center justify-center text-primary font-bold text-2xl mt-5">
              @if ($fotoTerapeuta)
                <img class="w-full h-full object-cover" src="{{ $fotoTerapeuta }}" alt="Foto de {{ $nombreCompletoTerapeuta }}">
              @else
                {{ $inicialesTerapeuta }}
              @endif
            </div>
            <div>
              <h3 class="text-2xl font-bold">{{ $nombreCompletoTerapeuta }}</h3>
              @if (!empty($terapeuta->especialidad))
                <p class="text-primary font-bold text-sm">{{ $terapeuta->especialidad }}</p>
              @endif
              @if (!empty($modalidadAtencion))
                <p class="text-outline text-sm mt-1">Modalidad {{ $modalidadAtencion }}</p>
              @endif
            </div>
            <button class="w-full py-3 text-primary font-bold hover:bg-primary/5 rounded-full" type="button" data-open-therapist-modal>Ver perfil completo</button>
          @else
            <div class="w-20 h-20 rounded-full bg-primary-fixed text-primary flex items-center justify-center mt-2">
              <span class="material-symbols-outlined text-[42px]">psychology</span>
            </div>
            <div>
              <h3 class="text-2xl font-bold">Aún no tienes terapeuta vinculado</h3>
              <p class="text-on-surface-variant mt-2">Ingresa el código de vinculación que te compartió tu terapeuta.</p>
            </div>
            <form class="w-full text-left grid gap-4" action="{{ url('/vincular-terapeuta') }}" method="POST">
              @csrf
              <div>
                <label class="text-sm font-bold text-on-surface" for="codigo">PIN de vinculación</label>
                <input class="mt-2 w-full rounded-2xl border-outline-variant bg-surface" type="text" id="codigo" name="codigo" placeholder="Ejemplo: 483921" maxlength="6" required>
              </div>
              <div>
                <label class="text-sm font-bold text-on-surface" for="motivo">¿Qué te gustaría trabajar en terapia?</label>
                <textarea class="mt-2 w-full rounded-2xl border-outline-variant bg-surface" id="motivo" name="motivo" rows="4" placeholder="Ejemplo: ansiedad, problemas familiares, estrés..." required></textarea>
              </div>
              <button class="w-full py-3 bg-primary text-on-primary rounded-full font-bold" type="submit">Vincular terapeuta</button>
            </form>
          @endif
        </section>
      </div>

      <footer class="bg-surface-container-high/40 border border-outline-variant/40 rounded-[2rem] p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-secondary-container rounded-full flex items-center justify-center text-on-secondary-container">
            <span class="material-symbols-outlined text-3xl">sentiment_satisfied</span>
          </div>
          <div>
            <p class="text-xs uppercase text-outline font-bold">Último registro emocional</p>
            @if ($ultimoRegistro)
              <div class="flex flex-wrap items-center gap-3">
                <h5 class="text-2xl font-bold">{{ $ultimoRegistro->emocion }}</h5>
                @if ($ultimoRegistro->intensidad)
                  <span class="text-on-surface-variant">{{ $ultimoRegistro->intensidad }}/10</span>
                @endif
                <span class="text-outline">{{ $formatearFecha($ultimoRegistro->created_at ?? null, 'd M Y, H:i') }}</span>
              </div>
            @else
              <h5 class="text-2xl font-bold">Aún no has registrado emociones</h5>
            @endif
          </div>
        </div>

        @if ($ultimoRegistro)
          @php
            $resumenRegistro = $ultimoRegistro->reestructuracion ?: ($ultimoRegistro->pensamiento ?: $ultimoRegistro->situacion);
          @endphp
          @if ($resumenRegistro)
            <div class="flex-1 md:max-w-md bg-white/70 p-4 rounded-2xl border border-white">
              <p class="text-on-surface-variant text-sm">{{ $resumenRegistro }}</p>
            </div>
          @endif
        @endif

        <a class="p-4 rounded-full bg-primary text-on-primary hover:opacity-90" href="{{ url('/diario') }}" aria-label="{{ $ultimoRegistro ? 'Editar diario emocional' : 'Crear primer registro' }}">
          <span class="material-symbols-outlined">{{ $ultimoRegistro ? 'edit' : 'add' }}</span>
        </a>
      </footer>
    </div>
  </main>

  <a class="fixed bottom-6 right-6 lg:bottom-8 lg:right-8 w-16 h-16 bg-primary text-on-primary rounded-full shadow-lg flex items-center justify-center z-50 hover:scale-105 active:scale-95 transition-all" href="{{ url('/diario') }}" aria-label="Registrar emoción de hoy">
    <span class="material-symbols-outlined text-3xl">add</span>
  </a>

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

  <script>
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
  @include('partials.marker-widget')
</body>
</html>
