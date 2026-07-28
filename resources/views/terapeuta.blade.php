@php
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $usuarioId = session('usuario_id');
    $terapeuta = $usuarioId
        ? DB::table('users')->where('id', $usuarioId)->first()
        : null;

    $nombreTerapeuta = trim(($terapeuta->nombre ?? session('usuario_nombre') ?? '') . ' ' . ($terapeuta->apellido ?? session('usuario_apellido') ?? ''));
    $nombreTerapeuta = $nombreTerapeuta !== '' ? $nombreTerapeuta : 'Terapeuta';
    $primerNombre = trim($terapeuta->nombre ?? session('usuario_nombre') ?? 'Terapeuta');
    $iniciales = strtoupper(substr($terapeuta->nombre ?? 'T', 0, 1) . substr($terapeuta->apellido ?? '', 0, 1));
    $iniciales = trim($iniciales) !== '' ? $iniciales : 'T';

    $avatarUrl = null;
    if (!empty($terapeuta->profile_photo_path)) {
        $avatarUrl = asset('storage/' . ltrim($terapeuta->profile_photo_path, '/'));
    } elseif (!empty($terapeuta->avatar_url)) {
        $avatarUrl = $terapeuta->avatar_url;
    }

    $pacientes = $usuarioId
        ? DB::table('users')
            ->where(function ($query) use ($usuarioId) {
                $query->where('terapeuta_id', (int) $usuarioId)
                    ->orWhere('terapeuta_id', (string) $usuarioId);
            })
            ->where(function ($query) {
                $query->where('terapeuta', 0)
                    ->orWhere('terapeuta', '0');
            })
            ->orderBy('nombre')
            ->get()
        : collect();

    $citasPendientes = $usuarioId
        ? DB::table('citas')
            ->join('users', 'citas.paciente_id', '=', 'users.id')
            ->where(function ($query) use ($usuarioId) {
                $query->where('citas.terapeuta_id', (int) $usuarioId)
                    ->orWhere('citas.terapeuta_id', (string) $usuarioId);
            })
            ->where('citas.estado', 'pendiente')
            ->select(
                'citas.id',
                'citas.fecha',
                'citas.hora',
                'citas.starts_at',
                'citas.ends_at',
                'citas.timezone',
                'citas.modalidad',
                'citas.motivo_encrypted',
                'citas.estado',
                'users.id as paciente_id',
                'users.nombre',
                'users.apellido'
            )
            ->orderBy('citas.created_at', 'desc')
            ->get()
        : collect();

    $pendientesTotal = isset($pendientesCount) ? $pendientesCount : $citasPendientes->count();
    $pinExpira = $terapeuta && $terapeuta->codigo_expira_en ? Carbon::parse($terapeuta->codigo_expira_en) : null;
    $pinActivo = $terapeuta && $terapeuta->codigo_vinculacion && (!$pinExpira || now()->lte($pinExpira));

    $citaInicio = function ($cita): Carbon {
        return $cita->starts_at
            ? Carbon::parse($cita->starts_at)->setTimezone($cita->timezone ?: 'America/Mexico_City')
            : Carbon::parse(($cita->fecha ?? now()->toDateString()) . ' ' . ($cita->hora ?: '00:00'));
    };

    $descifrar = function ($valor) {
        if (!$valor) {
            return 'Sin registro';
        }

        try {
            return Crypt::decryptString($valor);
        } catch (\Throwable $e) {
            return 'No se pudo mostrar este dato.';
        }
    };
@endphp

<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Papatzoa | Panel del Terapeuta</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-tertiary-container": "#fffbff",
                        "on-secondary": "#ffffff",
                        "on-tertiary": "#ffffff",
                        "on-tertiary-fixed-variant": "#683a34",
                        "on-error-container": "#93000a",
                        "error-container": "#ffdad6",
                        "tertiary-fixed-dim": "#f8b7ad",
                        "on-secondary-fixed": "#271907",
                        "tertiary-fixed": "#ffdad5",
                        "surface-dim": "#d1dbe8",
                        "surface-bright": "#f7f9ff",
                        "on-secondary-fixed-variant": "#56442e",
                        "inverse-surface": "#27313c",
                        "on-primary-fixed-variant": "#204f42",
                        "primary-container": "#507d6f",
                        "on-surface": "#121d26",
                        "tertiary-container": "#9c675f",
                        "surface": "#f7f9ff",
                        "secondary-fixed-dim": "#dcc2a6",
                        "on-primary": "#ffffff",
                        "inverse-primary": "#a0d1c0",
                        "on-error": "#ffffff",
                        "error": "#ba1a1a",
                        "secondary-container": "#f7dbbe",
                        "on-primary-fixed": "#002018",
                        "surface-container-low": "#edf4ff",
                        "surface-container": "#e4effd",
                        "secondary-fixed": "#fadec1",
                        "on-primary-container": "#f5fff9",
                        "background": "#f7f9ff",
                        "tertiary": "#804f48",
                        "on-secondary-container": "#735f48",
                        "primary-fixed": "#bceddb",
                        "outline-variant": "#c0c8c4",
                        "outline": "#717975",
                        "primary": "#376457",
                        "on-background": "#121d26",
                        "on-surface-variant": "#404945",
                        "surface-variant": "#d9e3f1",
                        "surface-tint": "#396759",
                        "surface-container-high": "#dfe9f7",
                        "inverse-on-surface": "#e8f2ff",
                        "on-tertiary-fixed": "#34100c",
                        "surface-container-highest": "#d9e3f1",
                        "secondary": "#6f5b44",
                        "primary-fixed-dim": "#a0d1c0",
                        "surface-container-lowest": "#ffffff"
                    },
                    borderRadius: {
                        DEFAULT: "1rem",
                        lg: "2rem",
                        xl: "3rem",
                        full: "9999px"
                    },
                    spacing: {
                        xl: "80px",
                        lg: "48px",
                        md: "24px",
                        margin: "32px",
                        gutter: "24px",
                        base: "8px",
                        sm: "12px",
                        xs: "4px"
                    },
                    fontFamily: {
                        "label-sm": ["Manrope"],
                        "label-md": ["Manrope"],
                        "body-md": ["Manrope"],
                        "body-lg": ["Manrope"],
                        "headline-lg": ["Manrope"],
                        "display-lg": ["Manrope"],
                        "headline-md": ["Manrope"],
                        "headline-lg-mobile": ["Manrope"]
                    },
                    fontSize: {
                        "label-sm": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "label-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "fontWeight": "600"}],
                        "display-lg": ["48px", {"lineHeight": "56px", "fontWeight": "700"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "600"}]
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Manrope', sans-serif; background-color: #f7f9ff; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .atmospheric-shadow { box-shadow: 0 10px 40px -15px rgba(55, 100, 87, 0.18); }
        .glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); }
    </style>
</head>
<body class="flex bg-background text-on-surface">
    <aside class="hidden lg:flex h-screen w-80 fixed left-0 top-0 border-r border-outline-variant bg-surface-container-lowest flex-col p-margin z-50">
        <div class="mb-lg flex items-center gap-sm">
            <div class="w-10 h-10 bg-primary flex items-center justify-center rounded-lg">
                <span class="material-symbols-outlined text-on-primary">spa</span>
            </div>
            <h1 class="text-headline-md font-bold text-primary">Papatzoa</h1>
        </div>

        <nav class="flex-1 overflow-y-auto space-y-sm" aria-label="Navegación principal">
            <a class="flex items-center gap-md px-md py-md bg-secondary-container text-on-secondary-container rounded-lg font-bold" href="{{ url('/terapeuta') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-label-md text-label-md">Inicio</span>
            </a>
            <a class="flex items-center gap-md px-md py-md text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/pacientes') }}">
                <span class="material-symbols-outlined">group</span>
                <span class="font-label-md text-label-md">Pacientes</span>
            </a>
            <a class="flex items-center gap-md px-md py-md text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="#proximas-citas">
                <span class="material-symbols-outlined">calendar_today</span>
                <span class="font-label-md text-label-md">Próximas citas</span>
            </a>
            <a class="flex items-center gap-md px-md py-md text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="#citas-pendientes">
                <span class="material-symbols-outlined">event_available</span>
                <span class="font-label-md text-label-md">Confirmar citas</span>
            </a>
            <a class="flex items-center gap-md px-md py-md text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/pacientes') }}">
                <span class="material-symbols-outlined">folder_shared</span>
                <span class="font-label-md text-label-md">Expedientes</span>
            </a>
            <a class="flex items-center gap-md px-md py-md text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/pacientes') }}">
                <span class="material-symbols-outlined">description</span>
                <span class="font-label-md text-label-md">Notas clínicas</span>
            </a>
        </nav>

        <div class="mt-auto space-y-xs border-t border-outline-variant pt-lg">
            <a class="flex items-center justify-center gap-sm w-full mb-md bg-primary text-on-primary py-sm rounded-full font-bold shadow-md hover:opacity-90 transition-all" href="#pin-vinculacion">
                <span class="material-symbols-outlined">vpn_key</span>
                Generar PIN
            </a>
            <a class="flex items-center gap-md px-md py-sm text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/terapeuta/mis-datos') }}">
                <span class="material-symbols-outlined">badge</span>
                <span class="font-label-md text-label-md">Mi perfil profesional</span>
            </a>
            <a class="flex items-center gap-md px-md py-sm text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/terapeuta/mis-datos') }}">
                <span class="material-symbols-outlined">account_circle</span>
                <span class="font-label-md text-label-md">Mi cuenta</span>
            </a>
            <a class="flex items-center gap-md px-md py-sm text-error hover:bg-error-container/20 rounded-lg transition-colors" href="{{ url('/logout') }}">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-label-md text-label-md">Cerrar sesión</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 lg:ml-80 min-h-screen pb-xl">
        <header class="w-full sticky top-0 z-40 bg-surface/95 backdrop-blur border-b border-outline-variant/40">
            <div class="w-full max-w-[1500px] mx-auto px-6 lg:px-10 xl:px-12 py-md md:py-lg flex justify-between items-center">
                <div>
                    <p class="lg:hidden text-primary font-bold text-xl">Papatzoa</p>
                    <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary font-bold">Hola, {{ $primerNombre }} 👋</h2>
                </div>
                <div class="flex items-center gap-sm md:gap-md">
                    <a class="hidden md:flex p-base text-on-surface-variant hover:text-primary transition-all" href="#citas-pendientes" aria-label="Citas pendientes">
                        <span class="material-symbols-outlined">notifications</span>
                    </a>
                    <a class="hidden sm:flex p-base text-on-surface-variant hover:text-primary transition-all" href="{{ url('/terapeuta/mis-datos') }}" aria-label="Mi cuenta">
                        <span class="material-symbols-outlined">account_circle</span>
                    </a>
                    <div class="flex items-center gap-sm border-l border-outline-variant pl-sm md:pl-md">
                        @if ($avatarUrl)
                            <img class="w-10 h-10 rounded-full object-cover border-2 border-primary-fixed" src="{{ $avatarUrl }}" alt="Foto de perfil de {{ $nombreTerapeuta }}">
                        @else
                            <div class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold border-2 border-primary-fixed">
                                {{ $iniciales }}
                            </div>
                        @endif
                        <div class="hidden lg:block">
                            <p class="font-label-md text-label-md text-on-surface">{{ $nombreTerapeuta }}</p>
                            <p class="text-[10px] text-on-surface-variant uppercase tracking-widest font-bold">{{ $terapeuta->especialidad ?? 'Salud mental' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <nav class="lg:hidden bg-surface border-b border-outline-variant/40" aria-label="Navegación móvil">
            <div class="w-full max-w-[1500px] mx-auto px-6 py-sm flex gap-sm overflow-x-auto">
                <a class="shrink-0 px-4 py-2 rounded-full bg-primary text-on-primary text-sm font-bold" href="{{ url('/terapeuta') }}">Inicio</a>
                <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/pacientes') }}">Pacientes</a>
                <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="#citas-pendientes">Pendientes</a>
                <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="#pin-vinculacion">PIN</a>
                <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-error text-sm font-bold" href="{{ url('/logout') }}">Salir</a>
            </div>
        </nav>

        <div class="w-full max-w-[1500px] mx-auto px-6 lg:px-10 xl:px-12 mt-md">
            @foreach (['success_pin' => 'bg-green-50 text-green-800 border-green-200', 'pin_existente' => 'bg-yellow-50 text-yellow-800 border-yellow-200', 'success_confirmar' => 'bg-green-50 text-green-800 border-green-200'] as $flashKey => $flashClass)
                @if (session($flashKey))
                    <div class="mb-md rounded-lg border px-md py-sm font-label-md {{ $flashClass }}">
                        {{ session($flashKey) }}
                    </div>
                @endif
            @endforeach

            <section class="w-full mb-lg relative overflow-hidden rounded-lg p-md md:p-lg bg-surface-container-low atmospheric-shadow">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-lg">
                    <div class="space-y-sm w-full max-w-5xl">
                        <p class="text-primary font-bold tracking-widest text-[12px] uppercase">Bienvenido al panel clínico de Papatzoa</p>
                        <h3 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Tu espacio de acompañamiento hoy</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant">
                            Tienes {{ $proximasCitas->count() }} {{ $proximasCitas->count() === 1 ? 'sesión programada' : 'sesiones programadas' }} y {{ $pendientesTotal }} {{ $pendientesTotal === 1 ? 'solicitud pendiente' : 'solicitudes pendientes' }} de confirmación.
                            {{ $pinActivo ? 'Tu código de vinculación está activo.' : 'No tienes un PIN activo en este momento.' }}
                        </p>
                        <div class="flex flex-wrap gap-sm pt-md">
                            <a class="bg-primary text-on-primary px-md py-sm rounded-full font-bold flex items-center gap-xs hover:opacity-90 transition-all" href="{{ url('/pacientes') }}">
                                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">group</span>
                                Ver pacientes
                            </a>
                            <a class="bg-secondary-container text-on-secondary-container px-md py-sm rounded-full font-bold flex items-center gap-xs hover:bg-secondary-fixed-dim transition-all" href="#citas-pendientes">
                                <span class="material-symbols-outlined">event_available</span>
                                Confirmar citas
                            </a>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
            </section>

            <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-6 mb-lg" aria-label="Métricas del panel">
                <div class="bg-surface-container-lowest p-md rounded-lg atmospheric-shadow border border-white/50 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-primary-fixed-dim/20 rounded-full flex items-center justify-center text-primary mb-sm">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                    <span class="font-display-lg text-[28px] text-on-surface font-extrabold">{{ $pacientes->count() }}</span>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">Pacientes vinculados</p>
                </div>
                <div class="bg-surface-container-lowest p-md rounded-lg atmospheric-shadow border border-white/50 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-secondary-container/20 rounded-full flex items-center justify-center text-secondary mb-sm">
                        <span class="material-symbols-outlined">calendar_month</span>
                    </div>
                    <span class="font-display-lg text-[28px] text-on-surface font-extrabold">{{ $proximasCitas->count() }}</span>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">Próximas citas</p>
                </div>
                <div class="bg-surface-container-lowest p-md rounded-lg atmospheric-shadow border border-white/50 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-error-container/20 rounded-full flex items-center justify-center text-error mb-sm">
                        <span class="material-symbols-outlined">pending_actions</span>
                    </div>
                    <span class="font-display-lg text-[28px] text-on-surface font-extrabold">{{ $pendientesTotal }}</span>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">Pendientes</p>
                </div>
                <div class="bg-surface-container-lowest p-md rounded-lg atmospheric-shadow border border-white/50 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-tertiary-container/20 rounded-full flex items-center justify-center text-tertiary mb-sm">
                        <span class="material-symbols-outlined">sticky_note_2</span>
                    </div>
                    <span class="font-display-lg text-[28px] text-on-surface font-extrabold">—</span>
                    <p class="font-label-sm text-label-sm text-on-surface-variant">Notas recientes</p>
                </div>
                <div class="bg-primary-container p-md rounded-lg atmospheric-shadow flex flex-col items-center text-center text-on-primary-container">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-sm">
                        <span class="material-symbols-outlined">key</span>
                    </div>
                    <span class="font-display-lg text-[28px] font-extrabold">{{ $pinActivo ? 1 : 0 }}</span>
                    <p class="font-label-sm text-label-sm">PIN activo</p>
                </div>
            </section>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                <div class="xl:col-span-8 space-y-lg">
                    <section id="proximas-citas" class="bg-surface-container-lowest rounded-lg atmospheric-shadow overflow-hidden scroll-mt-32">
                        <div class="p-md border-b border-outline-variant flex justify-between items-center">
                            <div class="flex items-center gap-sm">
                                <span class="material-symbols-outlined text-on-surface-variant">calendar_today</span>
                                <h4 class="font-headline-md text-headline-md text-on-surface">Próximas citas</h4>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left" aria-label="Tabla de próximas citas">
                                <thead class="bg-surface-container-low/50">
                                    <tr>
                                        <th class="px-md py-sm font-label-sm text-label-sm text-on-surface-variant uppercase">Paciente</th>
                                        <th class="px-md py-sm font-label-sm text-label-sm text-on-surface-variant uppercase">Fecha</th>
                                        <th class="px-md py-sm font-label-sm text-label-sm text-on-surface-variant uppercase">Hora</th>
                                        <th class="px-md py-sm font-label-sm text-label-sm text-on-surface-variant uppercase">Motivo / descripción</th>
                                        <th class="px-md py-sm font-label-sm text-label-sm text-on-surface-variant uppercase">Estado</th>
                                        <th class="px-md py-sm font-label-sm text-label-sm text-on-surface-variant uppercase">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/30">
                                    @forelse ($proximasCitas as $cita)
                                        @php
                                            $nombrePaciente = trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? ''));
                                            $inicialesPaciente = strtoupper(substr($cita->nombre ?? 'P', 0, 1) . substr($cita->apellido ?? '', 0, 1));
                                            $inicio = $citaInicio($cita);
                                        @endphp
                                        <tr class="hover:bg-surface-container-low/30 transition-colors">
                                            <td class="px-md py-md">
                                                <div class="flex items-center gap-sm">
                                                    <div class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-bold text-xs">{{ $inicialesPaciente }}</div>
                                                    <a class="font-bold text-primary hover:underline" href="{{ url('/expediente/' . $cita->paciente_id) }}">{{ $nombrePaciente }}</a>
                                                </div>
                                            </td>
                                            <td class="px-md py-md font-body-md text-body-md whitespace-nowrap">{{ $inicio->format('d/m/Y') }}</td>
                                            <td class="px-md py-md font-body-md text-body-md font-bold whitespace-nowrap">{{ $inicio->format('H:i') }}</td>
                                            <td class="px-md py-md font-body-md text-body-md min-w-56">{{ $descifrar($cita->motivo_encrypted ?? $cita->motivo ?? null) }}</td>
                                            <td class="px-md py-md"><span class="bg-primary/10 text-primary px-sm py-xs rounded-full text-xs font-bold">Confirmada</span></td>
                                            <td class="px-md py-md">
                                                <a class="text-primary font-bold text-sm hover:underline whitespace-nowrap" href="{{ url('/expediente/' . $cita->paciente_id) }}">Ver expediente</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="px-md py-lg text-center text-on-surface-variant" colspan="6">No tienes próximas citas programadas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="bg-surface-container-lowest p-md rounded-lg atmospheric-shadow border border-white/50 w-full">
                        <div class="flex items-center justify-between mb-md">
                            <h4 class="font-headline-md text-headline-md font-bold text-on-surface">Actividad &amp; Alertas</h4>
                            <span class="material-symbols-outlined text-on-surface-variant">monitor_heart</span>
                        </div>
                        <div class="rounded-lg border border-outline-variant/30 bg-surface-container-low/40 p-md text-on-surface-variant">
                            No hay alertas clínicas recientes.
                        </div>
                    </section>

                    <section id="citas-pendientes" class="space-y-md scroll-mt-32">
                        <div class="flex items-center gap-sm">
                            <span class="material-symbols-outlined text-on-surface-variant">event_available</span>
                            <h4 class="font-headline-md text-headline-md text-on-surface">Solicitudes pendientes</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                            @forelse ($citasPendientes as $solicitud)
                                @php
                                    $nombrePaciente = trim(($solicitud->nombre ?? '') . ' ' . ($solicitud->apellido ?? ''));
                                    $inicialesPaciente = strtoupper(substr($solicitud->nombre ?? 'P', 0, 1) . substr($solicitud->apellido ?? '', 0, 1));
                                    $inicioSolicitud = $citaInicio($solicitud);
                                @endphp
                                <article class="bg-secondary-container/30 p-md rounded-lg border border-secondary-container flex flex-col gap-md">
                                    <div class="flex items-start justify-between gap-sm">
                                        <div class="flex items-center gap-sm min-w-0">
                                            <div class="w-12 h-12 rounded-full bg-secondary text-white flex items-center justify-center font-bold shrink-0">{{ $inicialesPaciente }}</div>
                                            <div class="min-w-0">
                                                <a class="font-bold text-on-surface hover:text-primary hover:underline" href="{{ url('/expediente/' . $solicitud->paciente_id) }}">{{ $nombrePaciente }}</a>
                                                <p class="text-xs text-on-surface-variant">Solicitud pendiente</p>
                                            </div>
                                        </div>
                                        <div class="bg-white px-sm py-xs rounded-lg text-xs font-bold text-secondary shadow-sm whitespace-nowrap">
                                            {{ $inicioSolicitud->format('d/m') }} · {{ $inicioSolicitud->format('H:i') }}
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-sm text-sm">
                                        <div>
                                            <p class="text-xs uppercase tracking-widest font-bold text-on-surface-variant">Fecha solicitada</p>
                                            <p class="font-bold">{{ $inicioSolicitud->format('d/m/Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs uppercase tracking-widest font-bold text-on-surface-variant">Hora</p>
                                            <p class="font-bold">{{ $inicioSolicitud->format('H:i') }}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm text-on-surface-variant">{{ $descifrar($solicitud->motivo_encrypted ?? $solicitud->motivo ?? null) }}</p>
                                    <div class="grid gap-sm">
                                        <form action="{{ url('/citas/' . $solicitud->id . '/aceptar') }}" method="POST">
                                            @csrf
                                            <button class="w-full bg-secondary text-white py-sm rounded-lg font-bold text-sm hover:opacity-90" type="submit">Aceptar</button>
                                        </form>
                                        <form action="{{ url('/citas/' . $solicitud->id . '/rechazar') }}" method="POST">
                                            @csrf
                                            <label class="sr-only" for="comentario-{{ $solicitud->id }}">Comentario opcional</label>
                                            <div class="flex gap-sm">
                                                <input id="comentario-{{ $solicitud->id }}" class="flex-1 rounded-lg border-secondary/30 text-sm focus:border-secondary focus:ring-secondary/20" type="text" name="comentario" placeholder="Comentario opcional">
                                                <button class="px-md border border-secondary/30 text-secondary py-sm rounded-lg font-bold text-sm hover:bg-white" type="submit">Rechazar</button>
                                            </div>
                                        </form>
                                    </div>
                                </article>
                            @empty
                                <article class="md:col-span-2 bg-surface-container-lowest p-md rounded-lg border border-outline-variant/30 text-on-surface-variant">
                                    No tienes citas pendientes por confirmar.
                                </article>
                            @endforelse
                        </div>
                    </section>
                </div>

                <div class="xl:col-span-4 space-y-lg">
                    <section id="pin-vinculacion" class="bg-surface-container-lowest p-md rounded-lg border border-outline-variant/30 atmospheric-shadow scroll-mt-32">
                        <div class="flex items-center justify-between mb-sm">
                            <div class="flex items-center gap-xs text-primary">
                                <span class="material-symbols-outlined text-[20px]">vpn_key</span>
                                <h5 class="font-label-md text-label-md font-bold">Vincular paciente</h5>
                            </div>
                            <span class="text-[10px] text-on-surface-variant uppercase tracking-widest font-bold">{{ $pinActivo ? 'PIN activo' : 'Sin PIN activo' }}</span>
                        </div>

                        @if ($pinActivo)
                            <div class="flex items-center justify-between bg-surface-container-low p-sm rounded-lg gap-sm">
                                <div>
                                    <div id="pinValue" class="font-headline-md text-headline-md text-on-surface tracking-widest">{{ $terapeuta->codigo_vinculacion }}</div>
                                    @if ($pinExpira)
                                        <p class="text-[10px] text-on-surface-variant">Expira: {{ $pinExpira->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                                <button id="copyPinButton" class="p-sm bg-primary/10 text-primary rounded-full hover:bg-primary/20 transition-colors" type="button" title="Copiar código">
                                    <span class="material-symbols-outlined text-[20px]">content_copy</span>
                                </button>
                            </div>
                            <div class="mt-sm rounded-lg bg-primary/5 p-sm text-sm text-on-surface-variant">
                                No puedes generar otro PIN hasta que expire el actual.
                            </div>
                        @else
                            <div class="rounded-lg bg-surface-container-low p-sm text-on-surface-variant mb-sm">
                                No tienes un PIN activo.
                            </div>
                            <form action="{{ url('/generar-pin') }}" method="POST">
                                @csrf
                                <button class="w-full bg-primary text-on-primary py-sm rounded-full font-bold hover:opacity-90" type="submit">Generar PIN</button>
                            </form>
                        @endif
                    </section>

                    <section class="bg-surface-container-lowest p-md rounded-lg border border-outline-variant/30 atmospheric-shadow">
                        <div class="flex items-center justify-between mb-md">
                            <h5 class="font-headline-md text-headline-md font-bold">Pacientes recientes</h5>
                            <a class="text-primary font-bold text-sm hover:underline" href="{{ url('/pacientes') }}">Ver pacientes</a>
                        </div>
                        <div class="space-y-sm">
                            @forelse ($pacientes->take(4) as $paciente)
                                @php
                                    $nombrePaciente = trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? ''));
                                    $inicialesPaciente = strtoupper(substr($paciente->nombre ?? 'P', 0, 1) . substr($paciente->apellido ?? '', 0, 1));
                                @endphp
                                <a class="flex items-center justify-between p-sm hover:bg-surface-container-low rounded-lg transition-colors border border-outline-variant/30" href="{{ url('/expediente/' . $paciente->id) }}">
                                    <div class="flex items-center gap-sm min-w-0">
                                        <div class="w-10 h-10 rounded-full bg-tertiary-container/20 text-tertiary flex items-center justify-center font-bold shrink-0">{{ $inicialesPaciente }}</div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-on-surface truncate">{{ $nombrePaciente }}</p>
                                            <p class="text-xs text-on-surface-variant">Paciente vinculado</p>
                                        </div>
                                    </div>
                                    <span class="material-symbols-outlined text-on-surface-variant text-[20px]">chevron_right</span>
                                </a>
                            @empty
                                <div class="rounded-lg border border-outline-variant/30 p-sm text-on-surface-variant">
                                    Aún no tienes pacientes vinculados.
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <section class="bg-surface-container-lowest p-md rounded-lg border border-outline-variant/30 atmospheric-shadow">
                        <div class="flex items-center justify-between mb-md">
                            <h5 class="font-headline-md text-headline-md font-bold">Acciones rápidas</h5>
                            <span class="material-symbols-outlined text-on-surface-variant">bolt</span>
                        </div>
                        <div class="grid gap-sm">
                            <a class="flex items-center justify-between p-sm rounded-lg bg-primary text-on-primary font-bold hover:opacity-90 transition-all" href="{{ url('/pacientes') }}">
                                <span class="flex items-center gap-sm">
                                    <span class="material-symbols-outlined text-[20px]">group</span>
                                    Ver pacientes
                                </span>
                                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                            </a>
                            <a class="flex items-center justify-between p-sm rounded-lg bg-secondary-container text-on-secondary-container font-bold hover:bg-secondary-fixed-dim transition-all" href="#citas-pendientes">
                                <span class="flex items-center gap-sm">
                                    <span class="material-symbols-outlined text-[20px]">event_available</span>
                                    Confirmar citas
                                </span>
                                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                            </a>
                            <a class="flex items-center justify-between p-sm rounded-lg border border-outline-variant/40 text-on-surface-variant font-bold hover:bg-surface-container-low transition-all" href="{{ url('/terapeuta/mis-datos') }}">
                                <span class="flex items-center gap-sm">
                                    <span class="material-symbols-outlined text-[20px]">badge</span>
                                    Mi perfil profesional
                                </span>
                                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                            </a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <a class="fixed bottom-6 right-6 w-14 h-14 bg-primary text-on-primary rounded-full shadow-lg flex items-center justify-center z-50 hover:opacity-90 md:hidden" href="#pin-vinculacion" aria-label="Generar PIN">
        <span class="material-symbols-outlined text-[28px]">vpn_key</span>
    </a>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const copyBtn = document.getElementById('copyPinButton');
            const pinValue = document.getElementById('pinValue');

            if (copyBtn && pinValue) {
                copyBtn.addEventListener('click', async function () {
                    const originalHtml = copyBtn.innerHTML;

                    try {
                        await navigator.clipboard.writeText(pinValue.textContent.trim().replace(/\s/g, ''));
                        copyBtn.innerHTML = '<span class="material-symbols-outlined text-[20px]">check</span>';
                        setTimeout(function () {
                            copyBtn.innerHTML = originalHtml;
                        }, 1500);
                    } catch (e) {
                        console.warn('No se pudo copiar el PIN');
                    }
                });
            }
        });
    </script>

    @include('partials.marker-widget')
</body>
</html>
