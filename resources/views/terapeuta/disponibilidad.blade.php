@php
    use Carbon\Carbon;
    use Illuminate\Support\ViewErrorBag;

    $nombreCompleto = trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? ''));
    $nombreCompleto = $nombreCompleto !== '' ? $nombreCompleto : 'Terapeuta';
    $primerNombre = trim($usuario->nombre ?? 'Terapeuta');
    $iniciales = collect(explode(' ', $nombreCompleto))
        ->filter()
        ->take(2)
        ->map(fn ($parte) => mb_substr($parte, 0, 1))
        ->implode('');
    $iniciales = $iniciales !== '' ? strtoupper($iniciales) : 'T';

    $avatarUrl = null;
    if (!empty($usuario->profile_photo_path)) {
        $avatarUrl = asset('storage/' . ltrim($usuario->profile_photo_path, '/'));
    } elseif (!empty($usuario->avatar_url)) {
        $avatarUrl = $usuario->avatar_url;
    }

    $settingValue = fn ($key, $default = null) => old($key, $settings->{$key} ?? $default);
    $exceptionTypeLabels = [
        'blocked' => 'Bloqueo',
        'available' => 'Disponibilidad especial',
        'vacation' => 'Vacaciones',
    ];
    $viewErrors = $errors ?? new ViewErrorBag;
    $settingsErrors = $viewErrors->getBag('settings');
    $rulesErrors = $viewErrors->getBag('rules');
    $exceptionsErrors = $viewErrors->getBag('exceptions');
    $groupedPreviewSlots = collect($previewSlots)
        ->groupBy(fn ($slot) => Carbon::parse($slot['start'])->locale('es')->isoFormat('dddd D [de] MMMM'));
@endphp

<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Disponibilidad | Papatzoa</title>
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
                        "on-error-container": "#93000a",
                        "error-container": "#ffdad6",
                        "on-secondary-fixed": "#271907",
                        "on-primary-fixed-variant": "#204f42",
                        "primary-container": "#507d6f",
                        "on-surface": "#121d26",
                        "surface": "#f7f9ff",
                        "on-primary": "#ffffff",
                        "error": "#ba1a1a",
                        "secondary-container": "#f7dbbe",
                        "on-primary-container": "#f5fff9",
                        "background": "#f7f9ff",
                        "on-secondary-container": "#735f48",
                        "primary-fixed": "#bceddb",
                        "outline-variant": "#c0c8c4",
                        "outline": "#717975",
                        "primary": "#376457",
                        "on-background": "#121d26",
                        "on-surface-variant": "#404945",
                        "surface-variant": "#d9e3f1",
                        "surface-container-low": "#edf4ff",
                        "surface-container": "#e4effd",
                        "surface-container-high": "#dfe9f7",
                        "surface-container-highest": "#d9e3f1",
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
                        sans: ["Manrope", "sans-serif"]
                    },
                    fontSize: {
                        "label-sm": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "label-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "700"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "fontWeight": "700"}]
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Manrope', sans-serif; background-color: #f7f9ff; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .atmospheric-shadow { box-shadow: 0 10px 40px -15px rgba(55, 100, 87, 0.18); }
        input[type="time"]::-webkit-calendar-picker-indicator { opacity: .65; }
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
            <a class="flex items-center gap-md px-md py-md text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/terapeuta') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-label-md font-semibold">Inicio</span>
            </a>
            <a class="flex items-center gap-md px-md py-md text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/pacientes') }}">
                <span class="material-symbols-outlined">group</span>
                <span class="text-label-md font-semibold">Pacientes</span>
            </a>
            <a class="flex items-center gap-md px-md py-md bg-secondary-container text-on-secondary-container rounded-lg font-bold" href="{{ route('therapist.availability.index') }}">
                <span class="material-symbols-outlined">event_repeat</span>
                <span class="text-label-md font-bold">Disponibilidad</span>
            </a>
            <a class="flex items-center gap-md px-md py-md text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/terapeuta#proximas-citas') }}">
                <span class="material-symbols-outlined">calendar_today</span>
                <span class="text-label-md font-semibold">Próximas citas</span>
            </a>
            <a class="flex items-center gap-md px-md py-md text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/confirmar') }}">
                <span class="material-symbols-outlined">event_available</span>
                <span class="text-label-md font-semibold">Confirmar citas</span>
            </a>
            <a class="flex items-center gap-md px-md py-md text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/pacientes') }}">
                <span class="material-symbols-outlined">folder_shared</span>
                <span class="text-label-md font-semibold">Expedientes</span>
            </a>
            <a class="flex items-center gap-md px-md py-md text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/pacientes') }}">
                <span class="material-symbols-outlined">description</span>
                <span class="text-label-md font-semibold">Notas clínicas</span>
            </a>
        </nav>

        <div class="mt-auto space-y-xs border-t border-outline-variant pt-lg">
            <a class="flex items-center gap-md px-md py-sm text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/terapeuta/mis-datos') }}">
                <span class="material-symbols-outlined">badge</span>
                <span class="text-label-md font-semibold">Mi perfil profesional</span>
            </a>
            <a class="flex items-center gap-md px-md py-sm text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" href="{{ url('/terapeuta/mis-datos') }}">
                <span class="material-symbols-outlined">account_circle</span>
                <span class="text-label-md font-semibold">Mi cuenta</span>
            </a>
            <a class="flex items-center gap-md px-md py-sm text-error hover:bg-error-container/20 rounded-lg transition-colors" href="{{ url('/logout') }}">
                <span class="material-symbols-outlined">logout</span>
                <span class="text-label-md font-semibold">Cerrar sesión</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 lg:ml-80 min-h-screen pb-xl">
        <header class="w-full sticky top-0 z-40 bg-surface/95 backdrop-blur border-b border-outline-variant/40">
            <div class="w-full max-w-[1500px] mx-auto px-6 lg:px-10 xl:px-12 py-md md:py-lg flex justify-between items-center">
                <div>
                    <p class="lg:hidden text-primary font-bold text-xl">Papatzoa</p>
                    <h2 class="text-headline-lg text-primary font-bold">Disponibilidad</h2>
                    <p class="text-sm text-on-surface-variant">Hola, {{ $primerNombre }}. Configura horarios, excepciones y revisa slots reales.</p>
                </div>
                <div class="flex items-center gap-sm md:gap-md">
                    <a class="hidden sm:flex p-base text-on-surface-variant hover:text-primary transition-all" href="{{ url('/terapeuta/mis-datos') }}" aria-label="Mi cuenta">
                        <span class="material-symbols-outlined">account_circle</span>
                    </a>
                    <div class="flex items-center gap-sm border-l border-outline-variant pl-sm md:pl-md">
                        @if ($avatarUrl)
                            <img class="w-10 h-10 rounded-full object-cover border-2 border-primary-fixed" src="{{ $avatarUrl }}" alt="Foto de perfil de {{ $nombreCompleto }}">
                        @else
                            <div class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold border-2 border-primary-fixed">
                                {{ $iniciales }}
                            </div>
                        @endif
                        <div class="hidden lg:block">
                            <p class="text-label-md font-semibold text-on-surface">{{ $nombreCompleto }}</p>
                            <p class="text-[10px] text-on-surface-variant uppercase tracking-widest font-bold">{{ $usuario->especialidad ?? 'Salud mental' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <nav class="lg:hidden bg-surface border-b border-outline-variant/40" aria-label="Navegación móvil">
            <div class="w-full max-w-[1500px] mx-auto px-6 py-sm flex gap-sm overflow-x-auto">
                <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/terapeuta') }}">Inicio</a>
                <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/pacientes') }}">Pacientes</a>
                <a class="shrink-0 px-4 py-2 rounded-full bg-primary text-on-primary text-sm font-bold" href="{{ route('therapist.availability.index') }}">Disponibilidad</a>
                <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-on-surface-variant text-sm font-bold" href="{{ url('/confirmar') }}">Confirmar</a>
                <a class="shrink-0 px-4 py-2 rounded-full bg-surface-container text-error text-sm font-bold" href="{{ url('/logout') }}">Salir</a>
            </div>
        </nav>

        <div class="w-full max-w-[1500px] mx-auto px-6 lg:px-10 xl:px-12 mt-md space-y-lg">
            @if (session('success'))
                <div class="rounded-lg border border-primary/20 bg-primary-fixed px-md py-sm text-primary font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg border border-error/20 bg-error-container px-md py-sm text-error font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            <section class="grid grid-cols-1 xl:grid-cols-[minmax(0,1.1fr)_minmax(360px,0.9fr)] gap-md">
                <article class="rounded-lg bg-surface-container-lowest border border-outline-variant/40 atmospheric-shadow p-md md:p-lg">
                    <div class="flex items-start justify-between gap-md mb-md">
                        <div>
                            <h3 class="text-headline-md text-on-surface">Configuración general</h3>
                            <p class="text-sm text-on-surface-variant mt-xs">Estos valores controlan la generación de slots.</p>
                        </div>
                        <span class="material-symbols-outlined text-primary">tune</span>
                    </div>

                    @if ($settingsErrors->any())
                        <div class="mb-md rounded-lg border border-error/20 bg-error-container px-md py-sm text-sm text-error">
                            <ul class="list-disc pl-md">
                                @foreach ($settingsErrors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('therapist.availability.settings.update') }}" class="grid grid-cols-1 md:grid-cols-2 gap-md">
                        @csrf
                        <label class="space-y-xs md:col-span-2">
                            <span class="text-sm font-bold text-on-surface">Zona horaria</span>
                            <select name="timezone" class="w-full rounded-lg border-outline-variant bg-white">
                                @foreach ($timezones as $timezone => $label)
                                    <option value="{{ $timezone }}" @selected($settingValue('timezone', 'America/Mexico_City') === $timezone)>
                                        {{ $label }} ({{ $timezone }})
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="space-y-xs">
                            <span class="text-sm font-bold text-on-surface">Duración de sesión</span>
                            <input type="number" min="15" max="240" name="default_duration_minutes" value="{{ $settingValue('default_duration_minutes', 60) }}" class="w-full rounded-lg border-outline-variant bg-white">
                        </label>

                        <label class="space-y-xs">
                            <span class="text-sm font-bold text-on-surface">Anticipación mínima</span>
                            <input type="number" min="0" max="720" name="minimum_notice_hours" value="{{ $settingValue('minimum_notice_hours', 24) }}" class="w-full rounded-lg border-outline-variant bg-white">
                        </label>

                        <label class="space-y-xs">
                            <span class="text-sm font-bold text-on-surface">Descanso antes</span>
                            <input type="number" min="0" max="120" name="buffer_before_minutes" value="{{ $settingValue('buffer_before_minutes', 0) }}" class="w-full rounded-lg border-outline-variant bg-white">
                        </label>

                        <label class="space-y-xs">
                            <span class="text-sm font-bold text-on-surface">Descanso después</span>
                            <input type="number" min="0" max="120" name="buffer_after_minutes" value="{{ $settingValue('buffer_after_minutes', 0) }}" class="w-full rounded-lg border-outline-variant bg-white">
                        </label>

                        <label class="space-y-xs md:col-span-2">
                            <span class="text-sm font-bold text-on-surface">Máximo de días para reservar</span>
                            <input type="number" min="1" max="365" name="maximum_booking_days" value="{{ $settingValue('maximum_booking_days', 60) }}" class="w-full rounded-lg border-outline-variant bg-white">
                        </label>

                        <label class="flex items-center gap-sm rounded-lg border border-outline-variant/50 bg-surface-container-low px-md py-sm">
                            <input type="checkbox" name="requires_confirmation" value="1" class="rounded border-outline text-primary" @checked((bool) $settingValue('requires_confirmation', true))>
                            <span class="font-semibold text-on-surface">Requiere confirmación</span>
                        </label>

                        <label class="flex items-center gap-sm rounded-lg border border-outline-variant/50 bg-surface-container-low px-md py-sm">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-outline text-primary" @checked((bool) $settingValue('is_active', true))>
                            <span class="font-semibold text-on-surface">Disponibilidad activa</span>
                        </label>

                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-xs rounded-full bg-primary px-md py-sm font-bold text-on-primary hover:opacity-90">
                                <span class="material-symbols-outlined text-[20px]">save</span>
                                Guardar configuración
                            </button>
                        </div>
                    </form>
                </article>

                <article class="rounded-lg bg-surface-container-lowest border border-outline-variant/40 atmospheric-shadow p-md md:p-lg">
                    <div class="flex items-start justify-between gap-md mb-md">
                        <div>
                            <h3 class="text-headline-md text-on-surface">Vista previa</h3>
                            <p class="text-sm text-on-surface-variant mt-xs">Slots reales para los próximos 14 días.</p>
                        </div>
                        <a href="{{ route('therapist.availability.preview') }}" class="inline-flex items-center justify-center rounded-full bg-surface-container px-sm py-sm text-primary" aria-label="Abrir endpoint de vista previa">
                            <span class="material-symbols-outlined">visibility</span>
                        </a>
                    </div>

                    @if ($rules->flatten()->isEmpty())
                        <div class="rounded-lg border border-outline-variant/50 bg-surface-container-low px-md py-md text-on-surface-variant">
                            Aún no has configurado horarios disponibles.
                        </div>
                    @elseif ($groupedPreviewSlots->isEmpty())
                        <div class="rounded-lg border border-outline-variant/50 bg-surface-container-low px-md py-md text-on-surface-variant">
                            No hay slots disponibles en la ventana actual.
                        </div>
                    @else
                        <div class="space-y-md max-h-[620px] overflow-y-auto pr-xs">
                            @foreach ($groupedPreviewSlots as $dateLabel => $slots)
                                <div>
                                    <h4 class="mb-sm text-sm font-extrabold capitalize text-primary">{{ $dateLabel }}</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-sm">
                                        @foreach ($slots as $slot)
                                            <div class="rounded-lg border border-primary/20 bg-primary-fixed/60 px-sm py-sm">
                                                <p class="font-bold text-primary">{{ $slot['label'] }}</p>
                                                <p class="text-xs text-on-surface-variant">{{ Carbon::parse($slot['start'])->format('d/m/Y') }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>
            </section>

            <section class="rounded-lg bg-surface-container-lowest border border-outline-variant/40 atmospheric-shadow p-md md:p-lg">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-md mb-md">
                    <div>
                        <h3 class="text-headline-md text-on-surface">Horario semanal</h3>
                        <p class="text-sm text-on-surface-variant mt-xs">Puedes tener varios intervalos por día, sin traslapes.</p>
                    </div>
                    <span class="material-symbols-outlined text-primary">calendar_month</span>
                </div>

                @if ($rulesErrors->any())
                    <div class="mb-md rounded-lg border border-error/20 bg-error-container px-md py-sm text-sm text-error">
                        <ul class="list-disc pl-md">
                            @foreach ($rulesErrors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-md">
                    @foreach ($dayNames as $dayNumber => $dayName)
                        @php($dayRules = $rules->get($dayNumber, collect()))
                        <div class="rounded-lg border border-outline-variant/50 bg-surface-container-low p-md">
                            <div class="grid grid-cols-1 xl:grid-cols-[140px_minmax(0,1fr)_minmax(300px,420px)] gap-md">
                                <h4 class="font-extrabold text-primary">{{ $dayName }}</h4>

                                <div class="space-y-sm">
                                    @forelse ($dayRules as $rule)
                                        <form method="POST" action="{{ route('therapist.availability.rules.update', $rule->id) }}" class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto_auto] gap-sm rounded-lg bg-white p-sm border border-outline-variant/40">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="day_of_week" value="{{ $dayNumber }}">
                                            <label class="space-y-xs">
                                                <span class="text-xs font-bold text-on-surface-variant">Inicio</span>
                                                <input type="time" name="start_time" value="{{ substr($rule->start_time, 0, 5) }}" class="w-full rounded-lg border-outline-variant">
                                            </label>
                                            <label class="space-y-xs">
                                                <span class="text-xs font-bold text-on-surface-variant">Fin</span>
                                                <input type="time" name="end_time" value="{{ substr($rule->end_time, 0, 5) }}" class="w-full rounded-lg border-outline-variant">
                                            </label>
                                            <label class="flex items-center gap-xs self-end rounded-lg border border-outline-variant px-sm py-[11px] text-sm font-semibold">
                                                <input type="checkbox" name="is_active" value="1" class="rounded border-outline text-primary" @checked($rule->is_active)>
                                                Activo
                                            </label>
                                            <div class="flex items-end gap-xs">
                                                <button type="submit" class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-primary text-on-primary" aria-label="Editar horario">
                                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                                </button>
                                            </div>
                                        </form>
                                        <form method="POST" action="{{ route('therapist.availability.rules.destroy', $rule->id) }}" onsubmit="return confirm('¿Eliminar este horario?')" class="flex justify-end">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-xs rounded-full bg-error-container px-sm py-xs text-sm font-bold text-error">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                                Eliminar
                                            </button>
                                        </form>
                                    @empty
                                        <p class="rounded-lg border border-dashed border-outline-variant px-md py-sm text-sm text-on-surface-variant">Sin intervalos configurados.</p>
                                    @endforelse
                                </div>

                                <form method="POST" action="{{ route('therapist.availability.rules.store') }}" class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] xl:grid-cols-1 gap-sm rounded-lg bg-white p-sm border border-outline-variant/40">
                                    @csrf
                                    <input type="hidden" name="day_of_week" value="{{ $dayNumber }}">
                                    <label class="space-y-xs">
                                        <span class="text-xs font-bold text-on-surface-variant">Hora inicio</span>
                                        <input type="time" name="start_time" class="w-full rounded-lg border-outline-variant">
                                    </label>
                                    <label class="space-y-xs">
                                        <span class="text-xs font-bold text-on-surface-variant">Hora fin</span>
                                        <input type="time" name="end_time" class="w-full rounded-lg border-outline-variant">
                                    </label>
                                    <button type="submit" class="inline-flex items-center justify-center gap-xs rounded-full bg-primary px-sm py-sm font-bold text-on-primary">
                                        <span class="material-symbols-outlined text-[20px]">add</span>
                                        Agregar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-lg bg-surface-container-lowest border border-outline-variant/40 atmospheric-shadow p-md md:p-lg">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-md mb-md">
                    <div>
                        <h3 class="text-headline-md text-on-surface">Excepciones</h3>
                        <p class="text-sm text-on-surface-variant mt-xs">Bloquea horarios, marca vacaciones o agrega disponibilidad especial.</p>
                    </div>
                    <span class="material-symbols-outlined text-primary">event_busy</span>
                </div>

                @if ($exceptionsErrors->any())
                    <div class="mb-md rounded-lg border border-error/20 bg-error-container px-md py-sm text-sm text-error">
                        <ul class="list-disc pl-md">
                            @foreach ($exceptionsErrors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('therapist.availability.exceptions.store') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-[180px_220px_150px_150px_minmax(180px,1fr)_auto] gap-sm rounded-lg border border-outline-variant/50 bg-surface-container-low p-md">
                    @csrf
                    <label class="space-y-xs">
                        <span class="text-xs font-bold text-on-surface-variant">Fecha</span>
                        <input type="date" name="exception_date" value="{{ old('exception_date') }}" class="w-full rounded-lg border-outline-variant bg-white">
                    </label>
                    <label class="space-y-xs">
                        <span class="text-xs font-bold text-on-surface-variant">Tipo</span>
                        <select name="type" id="exception-type" class="w-full rounded-lg border-outline-variant bg-white">
                            @foreach ($exceptionTypeLabels as $type => $label)
                                <option value="{{ $type }}" @selected(old('type') === $type)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="exception-time-field space-y-xs">
                        <span class="text-xs font-bold text-on-surface-variant">Inicio</span>
                        <input type="time" name="start_time" value="{{ old('start_time') }}" class="w-full rounded-lg border-outline-variant bg-white">
                    </label>
                    <label class="exception-time-field space-y-xs">
                        <span class="text-xs font-bold text-on-surface-variant">Fin</span>
                        <input type="time" name="end_time" value="{{ old('end_time') }}" class="w-full rounded-lg border-outline-variant bg-white">
                    </label>
                    <label class="space-y-xs">
                        <span class="text-xs font-bold text-on-surface-variant">Motivo</span>
                        <input type="text" name="reason" value="{{ old('reason') }}" class="w-full rounded-lg border-outline-variant bg-white" maxlength="255">
                    </label>
                    <button type="submit" class="inline-flex items-center justify-center gap-xs self-end rounded-full bg-primary px-md py-sm font-bold text-on-primary">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        Agregar
                    </button>
                </form>

                <div class="mt-md overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left">
                        <thead>
                            <tr class="border-b border-outline-variant text-xs uppercase tracking-wide text-on-surface-variant">
                                <th class="py-sm pr-sm">Fecha</th>
                                <th class="py-sm pr-sm">Tipo</th>
                                <th class="py-sm pr-sm">Horario</th>
                                <th class="py-sm pr-sm">Motivo</th>
                                <th class="py-sm text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/50">
                            @forelse ($exceptions as $exception)
                                <tr>
                                    <td class="py-sm pr-sm font-semibold">{{ $exception->exception_date->format('d/m/Y') }}</td>
                                    <td class="py-sm pr-sm">{{ $exceptionTypeLabels[$exception->type] ?? $exception->type }}</td>
                                    <td class="py-sm pr-sm">
                                        @if ($exception->type === 'vacation')
                                            Todo el día
                                        @else
                                            {{ substr($exception->start_time, 0, 5) }} - {{ substr($exception->end_time, 0, 5) }}
                                        @endif
                                    </td>
                                    <td class="py-sm pr-sm text-on-surface-variant">{{ $exception->reason ?: 'Sin motivo' }}</td>
                                    <td class="py-sm text-right">
                                        <form method="POST" action="{{ route('therapist.availability.exceptions.destroy', $exception->id) }}" onsubmit="return confirm('¿Eliminar esta excepción?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-xs rounded-full bg-error-container px-sm py-xs text-sm font-bold text-error">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-md text-center text-on-surface-variant">Sin excepciones configuradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <script>
        const exceptionType = document.getElementById('exception-type');
        const exceptionTimeFields = document.querySelectorAll('.exception-time-field');

        function syncExceptionFields() {
            const isVacation = exceptionType && exceptionType.value === 'vacation';
            exceptionTimeFields.forEach((field) => {
                field.classList.toggle('hidden', isVacation);
                field.querySelectorAll('input').forEach((input) => {
                    input.disabled = isVacation;
                    if (isVacation) {
                        input.value = '';
                    }
                });
            });
        }

        if (exceptionType) {
            exceptionType.addEventListener('change', syncExceptionFields);
            syncExceptionFields();
        }
    </script>
</body>
</html>
