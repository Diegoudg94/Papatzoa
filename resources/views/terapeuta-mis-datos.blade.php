@php
    $estado = $usuario->estado_verificacion ?? 'no_enviada';
    $estadoLabels = [
        'no_enviada' => 'No enviada',
        'no_aplica' => 'No enviada',
        'pendiente' => 'Pendiente',
        'aprobada' => 'Aprobada',
        'rechazada' => 'Rechazada',
    ];
    $estadoClass = in_array($estado, ['no_enviada', 'no_aplica'], true) ? 'no_enviada' : $estado;
    $nombreCompleto = trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? ''));
    $iniciales = collect(explode(' ', $nombreCompleto ?: 'T'))
        ->filter()
        ->take(2)
        ->map(fn ($parte) => mb_substr($parte, 0, 1))
        ->implode('');
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
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mis datos | Papatzoa</title>
    <link rel="stylesheet" href="{{ asset('css/terapeuta.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/terapeuta-datos.css') }}" />
</head>

<body>
    <header class="header">
        <div class="header__container">
            <a class="brand" href="/terapeuta">
                <span class="brand__icon" aria-hidden="true">✳︎</span>
                <span class="brand__title">Panel del terapeuta</span>
            </a>

            <nav class="header__center" aria-label="Navegación principal">
                <a class="header__link" href="/terapeuta">Inicio</a>
                <a class="header__link" href="/pacientes">Pacientes</a>
            </nav>

            <nav class="header__actions" aria-label="Acciones de usuario">
                <div class="dropdown">
                    <button class="header__button" id="menuButton" type="button">
                        Mi cuenta
                    </button>
                    <div class="dropdown__menu" id="dropdownMenu">
                        <a class="dropdown__item" href="/terapeuta/mis-datos">Mis datos</a>
                        <a class="dropdown__item" href="/logout">Cerrar sesión</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="profile-page">
        <div class="profile-page__container">
            <header class="profile-page__header">
                <div>
                    <h1>Mis datos</h1>
                    <p>Actualiza tu perfil profesional y envía documentación para revisión.</p>
                </div>
                <a class="btn-secondary" href="/terapeuta">Volver al panel</a>
            </header>

            @if (session('success'))
                <div class="profile-alert profile-alert--success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="profile-alert profile-alert--error">
                    Revisa los campos marcados antes de continuar.
                </div>
            @endif

            <section class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar" aria-label="Foto de perfil">
                        @if ($usuario->profile_photo_path)
                            <img
                                src="{{ asset('storage/' . $usuario->profile_photo_path) }}"
                                alt="Foto de perfil"
                                class="profile-avatar-img"
                            >
                        @elseif ($usuario->avatar_url)
                            <img
                                src="{{ $usuario->avatar_url }}"
                                alt="Foto de perfil"
                                class="profile-avatar-img"
                            >
                        @else
                            <div class="profile-avatar-placeholder">
                                {{ strtoupper($iniciales) }}
                            </div>
                        @endif
                    </div>

                    <div class="profile-summary">
                        <span class="profile-kicker">Perfil</span>
                        <h2>{{ $nombreCompleto ?: 'Terapeuta' }}</h2>
                        <p>{{ $usuario->correo }}</p>
                        <span class="verification-badge verification-badge--{{ $estadoClass }}">
                            {{ $estadoLabels[$estado] ?? ucfirst($estado) }}
                        </span>
                    </div>

                    <div class="profile-photo-form">
                        <form action="/terapeuta/mis-datos/foto" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label class="form-group" for="foto">
                                <span>Foto de perfil</span>
                                <input id="foto" name="foto" type="file" accept="image/png,image/jpeg,image/webp">
                                @error('foto')
                                    <small>{{ $message }}</small>
                                @enderror
                            </label>
                            <button class="btn-secondary" type="submit">Cambiar foto</button>
                        </form>
                        @if ($usuario->avatar_url && $usuario->profile_photo_path)
                            <form method="POST" action="/terapeuta/mis-datos/foto-google">
                                @csrf
                                <button type="submit" class="btn-secondary btn-secondary--compact">
                                    Usar foto de Google
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </section>

            <section class="profile-card">
                <h2>Información profesional</h2>
                <form class="profile-form" action="/terapeuta/mis-datos" method="POST">
                    @csrf
                    <div class="profile-form-grid">
                        <label class="form-group" for="nacionalidad">
                            <span>Nacionalidad</span>
                            @php $nacionalidad = old('nacionalidad', $usuario->nacionalidad); @endphp
                            <select id="nacionalidad" name="nacionalidad">
                                <option value="">Seleccionar</option>
                                @foreach ($paises as $pais)
                                    <option value="{{ $pais }}" @selected($nacionalidad === $pais)>{{ $pais }}</option>
                                @endforeach
                            </select>
                            @error('nacionalidad')<small>{{ $message }}</small>@enderror
                        </label>

                        <div class="form-group contact-phone-group">
                            <span>Teléfono de contacto</span>
                            <div class="phone-group phone-combo">
                                <label class="form-group phone-code" for="telefono_lada">
                                    <span>País / lada</span>
                                    @php $telefonoLada = old('telefono_lada', $usuario->telefono_lada); @endphp
                                    <select id="telefono_lada" name="telefono_lada">
                                        <option value="">Seleccionar</option>
                                        @foreach ($ladas as $valor => $etiqueta)
                                            <option value="{{ $valor }}" @selected($telefonoLada === $valor)>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                    @error('telefono_lada')<small>{{ $message }}</small>@enderror
                                </label>

                                <label class="form-group phone-number" for="telefono">
                                    <span>Teléfono</span>
                                    <input
                                        id="telefono"
                                        name="telefono"
                                        type="text"
                                        value="{{ old('telefono', $usuario->telefono) }}"
                                        maxlength="10"
                                        inputmode="numeric"
                                        pattern="[0-9]{10}"
                                        placeholder="10 dígitos"
                                    >
                                    <small class="phone-help">Ingresa 10 dígitos</small>
                                    @error('telefono')<small>{{ $message }}</small>@enderror
                                </label>
                            </div>
                        </div>

                        <label class="form-group" for="especialidad">
                            <span>Especialidad</span>
                            <input id="especialidad" name="especialidad" type="text" value="{{ old('especialidad', $usuario->especialidad) }}">
                            @error('especialidad')<small>{{ $message }}</small>@enderror
                        </label>

                        <label class="form-group" for="experiencia_anios">
                            <span>Años de experiencia</span>
                            <input id="experiencia_anios" name="experiencia_anios" type="number" min="0" max="80" value="{{ old('experiencia_anios', $usuario->experiencia_anios) }}">
                            @error('experiencia_anios')<small>{{ $message }}</small>@enderror
                        </label>

                        <label class="form-group" for="cedula_profesional">
                            <span>Cédula profesional</span>
                            <input id="cedula_profesional" name="cedula_profesional" type="text" value="{{ old('cedula_profesional', $usuario->cedula_profesional) }}">
                            @error('cedula_profesional')<small>{{ $message }}</small>@enderror
                        </label>

                        <label class="form-group" for="institucion_formacion">
                            <span>Institución de formación</span>
                            <input id="institucion_formacion" name="institucion_formacion" type="text" value="{{ old('institucion_formacion', $usuario->institucion_formacion) }}">
                            @error('institucion_formacion')<small>{{ $message }}</small>@enderror
                        </label>

                        <label class="form-group" for="enfoque_terapeutico">
                            <span>Enfoque terapéutico</span>
                            <input id="enfoque_terapeutico" name="enfoque_terapeutico" type="text" value="{{ old('enfoque_terapeutico', $usuario->enfoque_terapeutico) }}">
                            @error('enfoque_terapeutico')<small>{{ $message }}</small>@enderror
                        </label>

                        <label class="form-group" for="modalidad_atencion">
                            <span>Modalidad de atención</span>
                            <select id="modalidad_atencion" name="modalidad_atencion">
                                @php $modalidad = old('modalidad_atencion', $usuario->modalidad_atencion); @endphp
                                <option value="">Seleccionar</option>
                                <option value="presencial" @selected($modalidad === 'presencial')>Presencial</option>
                                <option value="online" @selected($modalidad === 'online')>Online</option>
                                <option value="hibrida" @selected($modalidad === 'hibrida')>Híbrida</option>
                            </select>
                            @error('modalidad_atencion')<small>{{ $message }}</small>@enderror
                        </label>

                        @php
                            $paisAtencion = old('pais_atencion', $usuario->pais_atencion ?? '');
                            $estadoAtencion = old('estado_atencion', $usuario->estado_atencion ?? '');
                            $mostrarUbicacion = in_array($modalidad, ['presencial', 'hibrida'], true);
                        @endphp
                        <section
                            class="location-section form-group--full"
                            id="locationSection"
                            @unless ($mostrarUbicacion) hidden @endunless
                        >
                            <h3>Ubicación de atención presencial</h3>

                            <div class="location-grid">
                                <label class="form-group" for="pais_atencion">
                                    <span>País</span>
                                    <select id="pais_atencion" name="pais_atencion">
                                        <option value="">Seleccionar</option>
                                        @foreach ($paisesAtencion as $pais)
                                            <option value="{{ $pais }}" @selected($paisAtencion === $pais)>{{ $pais }}</option>
                                        @endforeach
                                    </select>
                                    @error('pais_atencion')<small>{{ $message }}</small>@enderror
                                </label>

                                <label class="form-group" for="estado_atencion">
                                    <span>Estado</span>
                                    <select
                                        id="estado_atencion"
                                        name="estado_atencion"
                                        data-selected="{{ $estadoAtencion }}"
                                    >
                                        <option value="">Seleccionar</option>
                                    </select>
                                    @error('estado_atencion')<small>{{ $message }}</small>@enderror
                                </label>

                                <label class="form-group" for="ciudad_atencion">
                                    <span>Ciudad</span>
                                    <input
                                        id="ciudad_atencion"
                                        name="ciudad_atencion"
                                        type="text"
                                        value="{{ old('ciudad_atencion', $usuario->ciudad_atencion ?? '') }}"
                                    >
                                    @error('ciudad_atencion')<small>{{ $message }}</small>@enderror
                                </label>

                                <label class="form-group" for="codigo_postal_atencion">
                                    <span>Código postal</span>
                                    <input
                                        id="codigo_postal_atencion"
                                        name="codigo_postal_atencion"
                                        type="text"
                                        value="{{ old('codigo_postal_atencion', $usuario->codigo_postal_atencion ?? '') }}"
                                        maxlength="20"
                                    >
                                    @error('codigo_postal_atencion')<small>{{ $message }}</small>@enderror
                                </label>

                                <label class="form-group form-group--full" for="direccion_atencion">
                                    <span>Dirección</span>
                                    <input
                                        id="direccion_atencion"
                                        name="direccion_atencion"
                                        type="text"
                                        value="{{ old('direccion_atencion', $usuario->direccion_atencion ?? '') }}"
                                    >
                                    @error('direccion_atencion')<small>{{ $message }}</small>@enderror
                                </label>
                            </div>
                        </section>

                        <label class="form-group form-group--full" for="biografia">
                            <span>Biografía</span>
                            <textarea id="biografia" name="biografia" rows="5">{{ old('biografia', $usuario->biografia) }}</textarea>
                            @error('biografia')<small>{{ $message }}</small>@enderror
                        </label>
                        <div class="form-actions">
                            <button class="btn-primary" type="submit">Guardar cambios</button>
                        </div>
                    </div>
                </form>
            </section>

            <section class="profile-card">
                <h2>Verificación profesional</h2>
                <p class="verification-copy">
                    Para proteger la seguridad de los pacientes, las cuentas de terapeuta podrán requerir verificación profesional. Puedes subir tu cédula profesional, título o certificaciones para iniciar el proceso de revisión.
                </p>

                <form action="/terapeuta/mis-datos/credenciales" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid form-grid--document">
                        <label class="form-group" for="tipo_documento">
                            <span>Tipo de documento</span>
                            <select id="tipo_documento" name="tipo_documento" required>
                                <option value="">Seleccionar</option>
                                <option value="Cédula profesional" @selected(old('tipo_documento') === 'Cédula profesional')>Cédula profesional</option>
                                <option value="Título profesional" @selected(old('tipo_documento') === 'Título profesional')>Título profesional</option>
                                <option value="Certificación" @selected(old('tipo_documento') === 'Certificación')>Certificación</option>
                                <option value="Identificación profesional" @selected(old('tipo_documento') === 'Identificación profesional')>Identificación profesional</option>
                                <option value="Otro" @selected(old('tipo_documento') === 'Otro')>Otro</option>
                            </select>
                            @error('tipo_documento')<small>{{ $message }}</small>@enderror
                        </label>

                        <label class="form-group" for="documento">
                            <span>Archivo</span>
                            <input id="documento" name="documento" type="file" accept=".pdf,image/png,image/jpeg,image/webp" required>
                            @error('documento')<small>{{ $message }}</small>@enderror
                        </label>
                    </div>

                    <div class="profile-actions">
                        <button class="btn-primary" type="submit">Subir documento</button>
                    </div>
                </form>
            </section>

            <section class="profile-card">
                <h2>Documentos enviados</h2>

                <div class="credentials-list">
                    @forelse ($credenciales as $credencial)
                        <article class="credential-item">
                            <div class="credential-info">
                                <strong>{{ $credencial->tipo_documento }}</strong>
                                <span>{{ $credencial->nombre_original ?: 'Archivo enviado' }}</span>
                            </div>
                            <div class="credential-meta">
                                <span>{{ $credencial->created_at ? $credencial->created_at->format('d/m/Y H:i') : 'Sin fecha' }}</span>
                                <span class="verification-badge verification-badge--{{ $credencial->estado }}">
                                    {{ ucfirst($credencial->estado) }}
                                </span>
                            </div>
                            <div class="credential-actions">
                                <a
                                    href="/terapeuta/mis-datos/credenciales/{{ $credencial->id }}/ver"
                                    class="btn-secondary btn-secondary--compact"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    Ver documento
                                </a>

                                <form
                                    method="POST"
                                    action="/terapeuta/mis-datos/credenciales/{{ $credencial->id }}"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar este documento?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <p class="credentials-empty">Aún no has enviado documentos.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </main>

    <script>
        const menuButton = document.getElementById('menuButton');
        const dropdownMenu = document.getElementById('dropdownMenu');

        menuButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('show');
        });

        window.addEventListener('click', (e) => {
            if (!menuButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

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
        const codigoPostalAtencionInput = document.getElementById('codigo_postal_atencion');

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

        if (codigoPostalAtencionInput) {
            codigoPostalAtencionInput.addEventListener('input', () => {
                // Futuro: aquí se puede conectar una API de códigos postales para autocompletar país, estado y ciudad.
            });
        }
    </script>
@include('partials.marker-widget')
</body>
</html>
