@php
    $estado = $usuario->estado_verificacion ?? 'no_enviada';
    $estadoLabels = [
        'no_enviada' => 'Verificación no enviada',
        'no_aplica' => 'Verificación no enviada',
        'pendiente' => 'Verificación pendiente',
        'aprobada' => 'Verificación aprobada',
        'rechazada' => 'Verificación rechazada',
    ];
    $estadoClass = in_array($estado, ['no_enviada', 'no_aplica', null], true) ? 'no_enviada' : $estado;
    $estadoIcons = [
        'no_enviada' => 'hourglass_empty',
        'pendiente' => 'pending',
        'aprobada' => 'verified',
        'rechazada' => 'error',
    ];
    $nombreCompleto = trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? ''));
    $iniciales = collect(explode(' ', $nombreCompleto ?: 'T'))
        ->filter()
        ->take(2)
        ->map(fn ($parte) => mb_substr($parte, 0, 1))
        ->implode('');
    $avatarUrl = null;

    if (!empty($usuario->profile_photo_path)) {
        $avatarUrl = asset('storage/' . ltrim($usuario->profile_photo_path, '/'));
    } elseif (!empty($usuario->avatar_url)) {
        $avatarUrl = $usuario->avatar_url;
    }

    $paises = [
        'México',
        'Estados Unidos',
        'Canadá',
        'España',
        'Argentina',
        'Chile',
        'Colombia',
        'Perú',
        'Brasil',
        'Uruguay',
        'Paraguay',
        'Bolivia',
        'Ecuador',
        'Venezuela',
        'Costa Rica',
        'Panamá',
        'Guatemala',
        'El Salvador',
        'Honduras',
        'Nicaragua',
        'República Dominicana',
        'Puerto Rico',
        'Otro',
    ];
    $ladas = [
        '+52' => 'México +52',
        '+1-us' => 'Estados Unidos +1',
        '+1-ca' => 'Canadá +1',
        '+34' => 'España +34',
        '+54' => 'Argentina +54',
        '+56' => 'Chile +56',
        '+57' => 'Colombia +57',
        '+51' => 'Perú +51',
        '+55' => 'Brasil +55',
        '+598' => 'Uruguay +598',
        '+595' => 'Paraguay +595',
        '+591' => 'Bolivia +591',
        '+593' => 'Ecuador +593',
        '+58' => 'Venezuela +58',
        '+506' => 'Costa Rica +506',
        '+507' => 'Panamá +507',
        '+502' => 'Guatemala +502',
        '+503' => 'El Salvador +503',
        '+504' => 'Honduras +504',
        '+505' => 'Nicaragua +505',
        '+1-do' => 'República Dominicana +1',
        '+1-pr' => 'Puerto Rico +1',
    ];
    $paisesAtencion = [
        'México',
        'Estados Unidos',
        'Canadá',
        'España',
        'Argentina',
        'Chile',
        'Colombia',
        'Perú',
        'Brasil',
        'Otro',
    ];
    $estadosAtencion = [
        'México' => [
            'Aguascalientes',
            'Baja California',
            'Baja California Sur',
            'Campeche',
            'Chiapas',
            'Chihuahua',
            'Ciudad de México',
            'Coahuila',
            'Colima',
            'Durango',
            'Estado de México',
            'Guanajuato',
            'Guerrero',
            'Hidalgo',
            'Jalisco',
            'Michoacán',
            'Morelos',
            'Nayarit',
            'Nuevo León',
            'Oaxaca',
            'Puebla',
            'Querétaro',
            'Quintana Roo',
            'San Luis Potosí',
            'Sinaloa',
            'Sonora',
            'Tabasco',
            'Tamaulipas',
            'Tlaxcala',
            'Veracruz',
            'Yucatán',
            'Zacatecas',
        ],
        'Estados Unidos' => [
            'California',
            'Texas',
            'Florida',
            'New York',
            'Illinois',
            'Arizona',
            'Otro',
        ],
        'Canadá' => [
            'Ontario',
            'Quebec',
            'British Columbia',
            'Alberta',
            'Otro',
        ],
        'España' => [
            'Madrid',
            'Cataluña',
            'Andalucía',
            'Valencia',
            'Otro',
        ],
        'Argentina' => ['Otro'],
        'Chile' => ['Otro'],
        'Colombia' => ['Otro'],
        'Perú' => ['Otro'],
        'Brasil' => ['Otro'],
        'Otro' => ['Otro'],
    ];

    $nacionalidad = old('nacionalidad', $usuario->nacionalidad);
    $telefonoLada = old('telefono_lada', $usuario->telefono_lada);
    $modalidad = old('modalidad_atencion', $usuario->modalidad_atencion);
    $paisAtencion = old('pais_atencion', $usuario->pais_atencion ?? '');
    $estadoAtencion = old('estado_atencion', $usuario->estado_atencion ?? '');
    $mostrarUbicacion = in_array($modalidad, ['presencial', 'hibrida'], true);
    $documentoEstadoClasses = [
        'aprobada' => 'border-primary/30 bg-primary/10 text-primary',
        'aprobado' => 'border-primary/30 bg-primary/10 text-primary',
        'pendiente' => 'border-secondary/30 bg-secondary-container text-on-secondary-container',
        'rechazada' => 'border-error/30 bg-error-container text-error',
        'rechazado' => 'border-error/30 bg-error-container text-error',
    ];
@endphp

<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil profesional | Papatzoa</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: '#f7f9ff',
                        surface: '#f7f9ff',
                        'surface-container-lowest': '#ffffff',
                        'surface-container-low': '#edf4ff',
                        'surface-container': '#e4effd',
                        'surface-container-high': '#dfe9f7',
                        'on-surface': '#121d26',
                        'on-surface-variant': '#404945',
                        primary: '#376457',
                        'on-primary': '#ffffff',
                        'primary-fixed': '#bceddb',
                        'primary-fixed-dim': '#a0d1c0',
                        secondary: '#6f5b44',
                        'secondary-container': '#f7dbbe',
                        'on-secondary-container': '#735f48',
                        outline: '#717975',
                        'outline-variant': '#c0c8c4',
                        error: '#ba1a1a',
                        'error-container': '#ffdad6',
                    },
                    fontFamily: {
                        sans: ['Manrope', 'sans-serif'],
                    },
                    borderRadius: {
                        DEFAULT: '0.5rem',
                        lg: '0.5rem',
                        xl: '0.75rem',
                    },
                    boxShadow: {
                        atmospheric: '0 10px 40px -10px rgba(55, 100, 87, 0.12)',
                    },
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        [hidden] {
            display: none !important;
        }

        .field-error {
            color: #ba1a1a;
            font-size: 0.8125rem;
            line-height: 1.25rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-background font-sans text-on-surface">
    <aside class="fixed left-0 top-0 z-50 hidden h-screen w-80 flex-col border-r border-outline-variant bg-surface-container-lowest p-8 lg:flex">
        <div class="mb-10 flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary text-on-primary">
                <span class="material-symbols-outlined">spa</span>
            </div>
            <div>
                <p class="text-xl font-extrabold text-primary">Panel del terapeuta</p>
                <p class="text-sm font-semibold text-on-surface-variant">Salud Mental</p>
            </div>
        </div>

        <nav class="flex flex-1 flex-col gap-2" aria-label="Navegación principal">
            <a class="flex items-center gap-4 rounded-lg px-6 py-3 font-semibold text-on-surface-variant transition-colors hover:bg-surface-container-high" href="{{ url('/terapeuta') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span>Inicio</span>
            </a>
            <a class="flex items-center gap-4 rounded-lg px-6 py-3 font-semibold text-on-surface-variant transition-colors hover:bg-surface-container-high" href="{{ url('/pacientes') }}">
                <span class="material-symbols-outlined">group</span>
                <span>Pacientes</span>
            </a>
            <a class="flex items-center gap-4 rounded-lg px-6 py-3 font-semibold text-on-surface-variant transition-colors hover:bg-surface-container-high" href="{{ url('/terapeuta') }}#proximas-citas">
                <span class="material-symbols-outlined">calendar_today</span>
                <span>Próximas citas</span>
            </a>
            <a class="flex items-center gap-4 rounded-lg px-6 py-3 font-semibold text-on-surface-variant transition-colors hover:bg-surface-container-high" href="{{ url('/terapeuta') }}#citas-pendientes">
                <span class="material-symbols-outlined">event_available</span>
                <span>Confirmar citas</span>
            </a>
            <a class="flex items-center gap-4 rounded-lg px-6 py-3 font-semibold text-on-surface-variant transition-colors hover:bg-surface-container-high" href="{{ url('/pacientes') }}">
                <span class="material-symbols-outlined">folder_shared</span>
                <span>Expedientes</span>
            </a>
            <a class="flex items-center gap-4 rounded-lg px-6 py-3 font-semibold text-on-surface-variant transition-colors hover:bg-surface-container-high" href="{{ url('/pacientes') }}">
                <span class="material-symbols-outlined">description</span>
                <span>Notas clínicas</span>
            </a>

            <div class="mt-8 border-t border-outline-variant pt-8">
                <a class="flex items-center gap-4 rounded-lg bg-secondary-container px-6 py-3 font-extrabold text-on-secondary-container" href="{{ url('/terapeuta/mis-datos') }}" aria-current="page">
                    <span class="material-symbols-outlined">badge</span>
                    <span>Mi perfil profesional</span>
                </a>
                <a class="mt-2 flex items-center gap-4 rounded-lg px-6 py-3 font-semibold text-on-surface-variant transition-colors hover:bg-surface-container-high" href="{{ url('/terapeuta/mis-datos') }}">
                    <span class="material-symbols-outlined">account_circle</span>
                    <span>Mi cuenta</span>
                </a>
                <a class="mt-2 flex items-center gap-4 rounded-lg px-6 py-3 font-semibold text-error transition-colors hover:bg-error-container/40" href="{{ url('/logout') }}">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="min-h-screen lg:ml-80">
        <header class="sticky top-0 z-40 border-b border-outline-variant/40 bg-surface/95 px-5 py-5 backdrop-blur lg:px-12 lg:py-7">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-normal text-primary md:text-4xl">Mi perfil profesional</h1>
                    <p class="mt-2 max-w-2xl text-sm font-medium text-on-surface-variant md:text-base">Actualiza tu perfil profesional y envía documentación para revisión.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a class="inline-flex items-center justify-center gap-2 rounded-full border border-primary px-5 py-3 text-sm font-extrabold text-primary transition-colors hover:bg-primary/5" href="{{ url('/terapeuta') }}">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                        Volver al panel
                    </a>
                    <a class="inline-flex items-center justify-center gap-2 rounded-full bg-error-container px-5 py-3 text-sm font-extrabold text-error lg:hidden" href="{{ url('/logout') }}">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        Salir
                    </a>
                </div>
            </div>
        </header>

        <div class="px-5 py-6 lg:px-12 lg:py-8">
            <nav class="mb-6 flex gap-3 overflow-x-auto pb-2 lg:hidden" aria-label="Navegación móvil">
                <a class="shrink-0 rounded-full bg-surface-container px-4 py-2 text-sm font-bold text-on-surface-variant" href="{{ url('/terapeuta') }}">Inicio</a>
                <a class="shrink-0 rounded-full bg-surface-container px-4 py-2 text-sm font-bold text-on-surface-variant" href="{{ url('/pacientes') }}">Pacientes</a>
                <a class="shrink-0 rounded-full bg-secondary-container px-4 py-2 text-sm font-bold text-on-secondary-container" href="{{ url('/terapeuta/mis-datos') }}">Mi perfil profesional</a>
                <a class="shrink-0 rounded-full bg-surface-container px-4 py-2 text-sm font-bold text-on-surface-variant" href="{{ url('/terapeuta/mis-datos') }}">Mi cuenta</a>
            </nav>

            @if (session('success'))
                <div class="mb-5 flex items-start gap-3 rounded-lg border border-primary/20 bg-primary/10 px-5 py-4 text-primary">
                    <span class="material-symbols-outlined mt-0.5">check_circle</span>
                    <p class="font-bold">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 flex items-start gap-3 rounded-lg border border-error/20 bg-error-container px-5 py-4 text-error">
                    <span class="material-symbols-outlined mt-0.5">error</span>
                    <p class="font-bold">{{ session('error') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-error/20 bg-error-container px-5 py-4 text-error">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined mt-0.5">error</span>
                        <div>
                            <p class="font-extrabold">Revisa los campos marcados antes de continuar.</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm font-semibold">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <section class="mb-6 rounded-lg border border-outline-variant/40 bg-surface-container-lowest p-5 shadow-atmospheric md:p-6">
                <div class="flex flex-col gap-6 xl:flex-row xl:items-center">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                        <div class="relative h-28 w-28 shrink-0 overflow-hidden rounded-full border-4 border-primary-fixed bg-primary-fixed-dim sm:h-32 sm:w-32">
                            @if ($avatarUrl)
                                <img class="h-full w-full object-cover" src="{{ $avatarUrl }}" alt="Foto de perfil de {{ $nombreCompleto ?: 'terapeuta' }}">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-primary-fixed text-4xl font-extrabold text-primary">
                                    {{ strtoupper($iniciales ?: 'T') }}
                                </div>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="break-words text-2xl font-extrabold text-on-surface md:text-3xl">{{ $nombreCompleto ?: 'Terapeuta' }}</h2>
                                <span class="{{ $estadoClass === 'aprobada' ? 'border-primary/30 bg-primary/10 text-primary' : ($estadoClass === 'pendiente' ? 'border-secondary/30 bg-secondary-container text-on-secondary-container' : ($estadoClass === 'rechazada' ? 'border-error/30 bg-error-container text-error' : 'border-outline-variant bg-surface-container text-on-surface-variant')) }} inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-extrabold">
                                    <span class="material-symbols-outlined text-[16px]" @if ($estadoClass === 'aprobada') style="font-variation-settings: 'FILL' 1;" @endif>{{ $estadoIcons[$estadoClass] ?? 'hourglass_empty' }}</span>
                                    {{ $estadoLabels[$estado] ?? ucfirst($estado) }}
                                </span>
                            </div>
                            <p class="mt-2 text-base font-bold text-on-surface-variant">{{ $usuario->especialidad ?: 'Especialidad no registrada' }}</p>
                            <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-on-surface-variant">
                                <span class="material-symbols-outlined text-[18px]">mail</span>
                                <span class="break-all">{{ $usuario->correo }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="w-full rounded-lg border border-outline-variant/50 bg-surface-container-low p-4 xl:ml-auto xl:max-w-md">
                        <form class="space-y-3" action="/terapeuta/mis-datos/foto" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label class="block" for="foto">
                                <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Foto de perfil</span>
                                <input class="block w-full rounded-lg border border-outline-variant bg-white text-sm font-semibold text-on-surface file:mr-4 file:border-0 file:bg-primary file:px-4 file:py-3 file:font-extrabold file:text-on-primary focus:border-primary focus:ring-primary/30" id="foto" name="foto" type="file" accept="image/png,image/jpeg,image/webp">
                                @error('foto')
                                    <small class="field-error">{{ $message }}</small>
                                @enderror
                            </label>
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <button class="inline-flex flex-1 items-center justify-center gap-2 rounded-full bg-primary px-5 py-3 text-sm font-extrabold text-on-primary transition-opacity hover:opacity-90" type="submit">
                                    <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                                    Cambiar foto
                                </button>
                            </div>
                        </form>

                        @if ($usuario->avatar_url && $usuario->profile_photo_path)
                            <form class="mt-3" method="POST" action="/terapeuta/mis-datos/foto-google">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-primary px-5 py-3 text-sm font-extrabold text-primary transition-colors hover:bg-primary/5">
                                    <span class="material-symbols-outlined text-[20px]">account_circle</span>
                                    Usar foto de Google
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                <section class="rounded-lg border border-outline-variant/40 bg-surface-container-lowest p-5 shadow-atmospheric lg:col-span-8 md:p-6">
                    <h2 class="mb-6 text-2xl font-extrabold text-on-surface">Información profesional</h2>

                    <form action="/terapeuta/mis-datos" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <label class="block" for="nacionalidad">
                                <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Nacionalidad</span>
                                <select class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="nacionalidad" name="nacionalidad">
                                    <option value="">Seleccionar</option>
                                    @foreach ($paises as $pais)
                                        <option value="{{ $pais }}" @selected($nacionalidad === $pais)>{{ $pais }}</option>
                                    @endforeach
                                </select>
                                @error('nacionalidad')<small class="field-error">{{ $message }}</small>@enderror
                            </label>

                            <div>
                                <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Teléfono de contacto</span>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-[minmax(0,12rem)_1fr]">
                                    <label class="block" for="telefono_lada">
                                        <span class="sr-only">País / lada</span>
                                        <select class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="telefono_lada" name="telefono_lada">
                                            <option value="">Seleccionar</option>
                                            @foreach ($ladas as $valor => $etiqueta)
                                                <option value="{{ $valor }}" @selected($telefonoLada === $valor)>{{ $etiqueta }}</option>
                                            @endforeach
                                        </select>
                                        @error('telefono_lada')<small class="field-error">{{ $message }}</small>@enderror
                                    </label>

                                    <label class="block" for="telefono">
                                        <span class="sr-only">Teléfono</span>
                                        <input class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="telefono" name="telefono" type="text" value="{{ old('telefono', $usuario->telefono) }}" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" placeholder="10 dígitos">
                                        <small class="mt-1 block text-xs font-semibold text-on-surface-variant">Ingresa 10 dígitos</small>
                                        @error('telefono')<small class="field-error">{{ $message }}</small>@enderror
                                    </label>
                                </div>
                            </div>

                            <label class="block" for="especialidad">
                                <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Especialidad</span>
                                <input class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="especialidad" name="especialidad" type="text" value="{{ old('especialidad', $usuario->especialidad) }}">
                                @error('especialidad')<small class="field-error">{{ $message }}</small>@enderror
                            </label>

                            <label class="block" for="experiencia_anios">
                                <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Años de experiencia</span>
                                <input class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="experiencia_anios" name="experiencia_anios" type="number" min="0" max="80" value="{{ old('experiencia_anios', $usuario->experiencia_anios) }}">
                                @error('experiencia_anios')<small class="field-error">{{ $message }}</small>@enderror
                            </label>

                            <label class="block" for="cedula_profesional">
                                <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Cédula profesional</span>
                                <input class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="cedula_profesional" name="cedula_profesional" type="text" value="{{ old('cedula_profesional', $usuario->cedula_profesional) }}">
                                @error('cedula_profesional')<small class="field-error">{{ $message }}</small>@enderror
                            </label>

                            <label class="block" for="institucion_formacion">
                                <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Institución de formación</span>
                                <input class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="institucion_formacion" name="institucion_formacion" type="text" value="{{ old('institucion_formacion', $usuario->institucion_formacion) }}">
                                @error('institucion_formacion')<small class="field-error">{{ $message }}</small>@enderror
                            </label>

                            <label class="block" for="enfoque_terapeutico">
                                <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Enfoque terapéutico</span>
                                <input class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="enfoque_terapeutico" name="enfoque_terapeutico" type="text" value="{{ old('enfoque_terapeutico', $usuario->enfoque_terapeutico) }}">
                                @error('enfoque_terapeutico')<small class="field-error">{{ $message }}</small>@enderror
                            </label>

                            <label class="block" for="modalidad_atencion">
                                <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Modalidad de atención</span>
                                <select class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="modalidad_atencion" name="modalidad_atencion">
                                    <option value="">Seleccionar</option>
                                    <option value="presencial" @selected($modalidad === 'presencial')>Presencial</option>
                                    <option value="online" @selected($modalidad === 'online')>Online</option>
                                    <option value="hibrida" @selected($modalidad === 'hibrida')>Híbrida</option>
                                </select>
                                @error('modalidad_atencion')<small class="field-error">{{ $message }}</small>@enderror
                            </label>

                            <section class="rounded-lg border border-primary/10 bg-surface-container-low p-5 md:col-span-2" id="locationSection" @unless ($mostrarUbicacion) hidden @endunless>
                                <div class="mb-5">
                                    <h3 class="text-sm font-extrabold uppercase text-primary">Ubicación de atención presencial</h3>
                                    <p class="mt-1 text-sm font-medium text-on-surface-variant">Solo se utiliza si ofreces atención presencial o híbrida.</p>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                    <label class="block" for="pais_atencion">
                                        <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">País</span>
                                        <select class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="pais_atencion" name="pais_atencion">
                                            <option value="">Seleccionar</option>
                                            @foreach ($paisesAtencion as $pais)
                                                <option value="{{ $pais }}" @selected($paisAtencion === $pais)>{{ $pais }}</option>
                                            @endforeach
                                        </select>
                                        @error('pais_atencion')<small class="field-error">{{ $message }}</small>@enderror
                                    </label>

                                    <label class="block" for="estado_atencion">
                                        <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Estado</span>
                                        <select class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="estado_atencion" name="estado_atencion" data-selected="{{ $estadoAtencion }}">
                                            <option value="">Seleccionar</option>
                                        </select>
                                        @error('estado_atencion')<small class="field-error">{{ $message }}</small>@enderror
                                    </label>

                                    <label class="block" for="ciudad_atencion">
                                        <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Ciudad</span>
                                        <input class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="ciudad_atencion" name="ciudad_atencion" type="text" value="{{ old('ciudad_atencion', $usuario->ciudad_atencion ?? '') }}">
                                        @error('ciudad_atencion')<small class="field-error">{{ $message }}</small>@enderror
                                    </label>

                                    <label class="block" for="codigo_postal_atencion">
                                        <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Código postal</span>
                                        <input class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="codigo_postal_atencion" name="codigo_postal_atencion" type="text" value="{{ old('codigo_postal_atencion', $usuario->codigo_postal_atencion ?? '') }}" maxlength="20">
                                        @error('codigo_postal_atencion')<small class="field-error">{{ $message }}</small>@enderror
                                    </label>

                                    <label class="block md:col-span-2" for="direccion_atencion">
                                        <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Dirección</span>
                                        <input class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="direccion_atencion" name="direccion_atencion" type="text" value="{{ old('direccion_atencion', $usuario->direccion_atencion ?? '') }}">
                                        @error('direccion_atencion')<small class="field-error">{{ $message }}</small>@enderror
                                    </label>
                                </div>
                            </section>

                            <label class="block md:col-span-2" for="biografia">
                                <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Biografía</span>
                                <textarea class="min-h-36 w-full rounded-lg border-outline-variant bg-white px-4 py-3 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="biografia" name="biografia" rows="6" placeholder="Describe tu trayectoria y enfoque para que tus pacientes te conozcan mejor.">{{ old('biografia', $usuario->biografia) }}</textarea>
                                @error('biografia')<small class="field-error">{{ $message }}</small>@enderror
                            </label>

                            <div class="flex justify-end md:col-span-2">
                                <button class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-primary px-8 py-4 font-extrabold text-on-primary shadow-lg shadow-primary/10 transition-opacity hover:opacity-90 sm:w-auto" type="submit">
                                    <span class="material-symbols-outlined text-[20px]">save</span>
                                    Guardar cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </section>

                <aside class="flex flex-col gap-6 lg:col-span-4">
                    <section class="rounded-lg border border-outline-variant/40 bg-surface-container-lowest p-5 shadow-atmospheric md:p-6">
                        <h2 class="text-2xl font-extrabold text-on-surface">Verificación profesional</h2>
                        <p class="mt-2 text-sm font-medium leading-relaxed text-on-surface-variant">Para proteger a los pacientes, requerimos validar tu formación profesional.</p>

                        <form class="mt-6 space-y-5" action="/terapeuta/mis-datos/credenciales" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label class="block" for="tipo_documento">
                                <span class="mb-2 block text-sm font-extrabold text-on-surface-variant">Tipo de documento</span>
                                <select class="h-12 w-full rounded-lg border-outline-variant bg-white px-4 font-semibold text-on-surface focus:border-primary focus:ring-primary/30" id="tipo_documento" name="tipo_documento" required>
                                    <option value="">Seleccionar</option>
                                    <option value="Cédula profesional" @selected(old('tipo_documento') === 'Cédula profesional')>Cédula profesional</option>
                                    <option value="Título profesional" @selected(old('tipo_documento') === 'Título profesional')>Título profesional</option>
                                    <option value="Certificación" @selected(old('tipo_documento') === 'Certificación')>Certificación</option>
                                    <option value="Identificación profesional" @selected(old('tipo_documento') === 'Identificación profesional')>Identificación profesional</option>
                                    <option value="Otro" @selected(old('tipo_documento') === 'Otro')>Otro</option>
                                </select>
                                @error('tipo_documento')<small class="field-error">{{ $message }}</small>@enderror
                            </label>

                            <label class="block rounded-lg border-2 border-dashed border-outline-variant bg-surface-container-low p-5 text-center transition-colors hover:border-primary hover:bg-primary/5" for="documento">
                                <span class="material-symbols-outlined mb-2 block text-[44px] text-outline">cloud_upload</span>
                                <span class="block text-sm font-extrabold text-on-surface-variant">Seleccionar archivo</span>
                                <span class="mb-4 block text-xs font-semibold text-on-surface-variant/80">PDF, JPG, PNG o WebP. Máximo 5MB.</span>
                                <input class="block w-full rounded-lg border border-outline-variant bg-white text-sm font-semibold text-on-surface file:mr-3 file:border-0 file:bg-primary file:px-4 file:py-2 file:font-extrabold file:text-on-primary focus:border-primary focus:ring-primary/30" id="documento" name="documento" type="file" accept=".pdf,image/png,image/jpeg,image/webp" required>
                                @error('documento')<small class="field-error">{{ $message }}</small>@enderror
                            </label>

                            <button class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-primary px-5 py-3 font-extrabold text-primary transition-colors hover:bg-primary/5" type="submit">
                                <span class="material-symbols-outlined text-[20px]">upload_file</span>
                                Subir documento
                            </button>
                        </form>
                    </section>

                    <section class="rounded-lg border border-outline-variant/40 bg-surface-container-lowest p-5 shadow-atmospheric md:p-6">
                        <h2 class="mb-5 text-sm font-extrabold uppercase tracking-wide text-on-surface">Documentos enviados</h2>

                        <div class="space-y-3">
                            @forelse ($credenciales as $credencial)
                                @php
                                    $documentoEstado = $credencial->estado ?? 'pendiente';
                                    $documentoEstadoClass = $documentoEstadoClasses[$documentoEstado] ?? 'border-outline-variant bg-surface-container text-on-surface-variant';
                                @endphp
                                <article class="rounded-lg border border-outline-variant/40 bg-surface-container p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-white text-primary">
                                            <span class="material-symbols-outlined">description</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                <div class="min-w-0">
                                                    <h3 class="break-words text-sm font-extrabold text-on-surface">{{ $credencial->tipo_documento }}</h3>
                                                    <p class="mt-1 break-words text-xs font-semibold text-on-surface-variant">{{ $credencial->nombre_original ?: 'Archivo enviado' }}</p>
                                                    <p class="mt-1 text-xs font-medium text-on-surface-variant">{{ $credencial->created_at ? $credencial->created_at->format('d/m/Y H:i') : 'Sin fecha' }}</p>
                                                </div>
                                                <span class="{{ $documentoEstadoClass }} inline-flex w-fit rounded-full border px-2.5 py-1 text-[11px] font-extrabold uppercase">
                                                    {{ ucfirst($documentoEstado) }}
                                                </span>
                                            </div>

                                            @if (!empty($credencial->comentario_revision))
                                                <p class="mt-3 rounded-lg bg-white px-3 py-2 text-xs font-semibold text-on-surface-variant">{{ $credencial->comentario_revision }}</p>
                                            @endif

                                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                                <a class="inline-flex items-center justify-center gap-1.5 rounded-full border border-primary px-3 py-2 text-xs font-extrabold text-primary transition-colors hover:bg-primary/5" href="/terapeuta/mis-datos/credenciales/{{ $credencial->id }}/ver" target="_blank" rel="noopener">
                                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                    Ver documento
                                                </a>

                                                <form method="POST" action="/terapeuta/mis-datos/credenciales/{{ $credencial->id }}" onsubmit="return confirm('¿Seguro que deseas eliminar este documento?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-full bg-error-container px-3 py-2 text-xs font-extrabold text-error transition-opacity hover:opacity-90">
                                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-lg border border-dashed border-outline-variant bg-surface-container-low p-5 text-center">
                                    <span class="material-symbols-outlined mb-2 text-[40px] text-primary">inventory_2</span>
                                    <p class="font-extrabold text-on-surface">Aún no has enviado documentos.</p>
                                    <p class="mt-2 text-sm font-medium text-on-surface-variant">Sube tu cédula, título o certificación para iniciar tu verificación profesional.</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </main>

    <script>
        const telefonoInput = document.getElementById('telefono');

        if (telefonoInput) {
            const limpiarTelefono = () => {
                telefonoInput.value = telefonoInput.value.replace(/\D/g, '').slice(0, 10);
            };

            limpiarTelefono();
            telefonoInput.addEventListener('input', limpiarTelefono);
        }

        const estadosAtencion = @json($estadosAtencion);
        const modalidadAtencionSelect = document.getElementById('modalidad_atencion');
        const locationSection = document.getElementById('locationSection');
        const paisAtencionSelect = document.getElementById('pais_atencion');
        const estadoAtencionSelect = document.getElementById('estado_atencion');

        function toggleLocationSection() {
            if (!modalidadAtencionSelect || !locationSection) {
                return;
            }

            const debeMostrar = ['presencial', 'hibrida'].includes(modalidadAtencionSelect.value);
            locationSection.hidden = !debeMostrar;
        }

        function updateEstadosByPais() {
            if (!paisAtencionSelect || !estadoAtencionSelect) {
                return;
            }

            const pais = paisAtencionSelect.value;
            const estados = pais ? (estadosAtencion[pais] || ['Otro']) : [];
            const estadoGuardado = estadoAtencionSelect.dataset.selected || '';

            estadoAtencionSelect.innerHTML = '<option value="">Seleccionar</option>';

            estados.forEach((estado) => {
                const option = document.createElement('option');
                option.value = estado;
                option.textContent = estado;
                option.selected = estado === estadoGuardado;
                estadoAtencionSelect.appendChild(option);
            });
        }

        if (modalidadAtencionSelect) {
            modalidadAtencionSelect.addEventListener('change', toggleLocationSection);
            toggleLocationSection();
        }

        if (paisAtencionSelect) {
            paisAtencionSelect.addEventListener('change', () => {
                if (estadoAtencionSelect) {
                    estadoAtencionSelect.dataset.selected = '';
                }

                updateEstadosByPais();
            });
            updateEstadosByPais();
        }
    </script>
@include('partials.marker-widget')
</body>
</html>
