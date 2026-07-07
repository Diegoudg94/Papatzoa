<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
</head>

<body>

  <!-- HEADER -->
  <header class="header">
    <div class="header__container">
      <a class="brand" href="#">
        <span class="brand__icon" aria-hidden="true">✳︎</span>
        <span class="brand__title">¿Cómo te sientes hoy?</span>
      </a>

      <nav class="header__actions" aria-label="Acciones de usuario">
        <div class="dropdown">
          <button class="header__button" id="menuButton" type="button" aria-haspopup="true" aria-expanded="false">
            Mi cuenta
          </button>
          <div class="dropdown__menu" id="dropdownMenu">
            <a class="dropdown__item" href="#">Mis datos</a>
            <a class="dropdown__item" href="/logout">Cerrar sesión</a>
          </div>
        </div>
      </nav>
    </div>
  </header>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="main">
    <div class="main__container">

      <!-- Hero/Welcome -->
      <section class="hero">
        <div class="hero__content">
          <h1 class="hero__title">
            Hola, {{ session('usuario_nombre') }} 👋
          </h1>
          <p class="hero__text">
            Bienvenido nuevamente a Papatzoa.
          </p>
        </div>
      </section>

      <!-- SECCIÓN 1: TARJETAS (CITAS, DIARIO, AYUDA) -->
      <section class="content">
        <div class="cards">
          <!-- Tarjeta Citas -->
          <article class="card">
            <a href="/citas" class="card__link">
              <img src="{{ asset('images/pexels-cottonbro-4101143.jpg') }}" alt="Mis próximas citas" class="card__image" />
              <h2 class="card__title">Mis próximas citas</h2>
              <p class="card__description">Consulta o agenda una cita con tu psicólogo</p>
            </a>
          </article>

          <!-- Tarjeta Diario -->
          <article class="card">
            <a href="/diario" class="card__link">
              <img src="{{ asset('images/pexels-pixabay-208147.jpg') }}" alt="Diario de emociones" class="card__image" />
              <h2 class="card__title">Diario de emociones</h2>
              <p class="card__description">Registra tus emociones del día a día</p>
            </a>
          </article>

          <!-- Tarjeta Ayuda -->
          <article class="card">
            <a href="/ayuda" class="card__link">
              <img src="{{ asset('images/pexels-rdne-5530681.jpg') }}" alt="Recursos de ayuda" class="card__image" />
              <h2 class="card__title">Recursos de ayuda</h2>
              <p class="card__description">Explora recursos de ayuda</p>
            </a>
          </article>
        </div>
      </section>

      <!-- SECCIÓN 2: VINCULACIÓN CON TERAPEUTA -->
      <section class="content" style="margin-top: 2rem;">
        <article class="card">
          @if (session('success_vinculacion'))
            <div style="background:#dcfce7; color:#166534; padding:14px; border-radius:10px; margin-bottom:18px; border:1px solid #86efac;">
              {{ session('success_vinculacion') }}
            </div>
          @endif

          @if (session('error_vinculacion'))
            <div style="background:#fee2e2; color:#991b1b; padding:14px; border-radius:10px; margin-bottom:18px; border:1px solid #fca5a5;">
              {{ session('error_vinculacion') }}
            </div>
          @endif

          @if (!$terapeuta)
            <h2 class="card__title">Vincular terapeuta</h2>
            <p class="card__description" style="margin-bottom:18px;">
              Aún no tienes un terapeuta vinculado.
            </p>
            <p style="margin-bottom:20px; color:#6b7280; line-height:1.7;">
              Ingresa el PIN de vinculación proporcionado por tu terapeuta para conectar tu cuenta.
            </p>

            <!-- Formulario Corregido -->
            <form action="/vincular-terapeuta" method="POST">
              @csrf
              
              <div class="form__group" style="margin-bottom: 20px;">
                <label class="form__label" for="codigo">PIN de vinculación</label>
                <input class="form__input" type="text" id="codigo" name="codigo" placeholder="Ejemplo: 483921" maxlength="6" required />
              </div>

              <div class="form__group" style="margin-bottom: 20px;">
                <label class="form__label" for="motivo">¿Qué te gustaría trabajar en terapia?</label>
                <textarea
                    class="form__input"
                    id="motivo"
                    name="motivo"
                    rows="5"
                    placeholder="Ejemplo: ansiedad, problemas familiares, estrés..."
                    required
                ></textarea>
              </div>

              <button class="button" type="submit">Vincular terapeuta</button>
            </form>

          @else
            @php
              $nombreCompletoTerapeuta = trim(($terapeuta->nombre ?? '') . ' ' . ($terapeuta->apellido ?? ''));
              $nombreCompletoTerapeuta = $nombreCompletoTerapeuta !== '' ? $nombreCompletoTerapeuta : 'Terapeuta';
              $inicialesTerapeuta = strtoupper(substr($terapeuta->nombre ?? 'T', 0, 1) . substr($terapeuta->apellido ?? '', 0, 1));
              $fotoTerapeuta = null;

              if (!empty($terapeuta->profile_photo_path)) {
                  $fotoTerapeuta = asset('storage/' . ltrim($terapeuta->profile_photo_path, '/'));
              } elseif (!empty($terapeuta->avatar_url)) {
                  $fotoTerapeuta = $terapeuta->avatar_url;
              }

              $estadoVerificacion = strtolower((string) ($terapeuta->estado_verificacion ?? ''));
              $badgeLabel = 'No enviada';
              $badgeClass = 'verification-badge--no-enviada';

              if ((bool) ($terapeuta->terapeuta_verificado ?? false) || in_array($estadoVerificacion, ['aprobado', 'aprobada', 'verificado', 'verificada'], true)) {
                  $badgeLabel = 'Aprobado / Verificado';
                  $badgeClass = 'verification-badge--aprobada';
              } elseif (in_array($estadoVerificacion, ['rechazado', 'rechazada'], true)) {
                  $badgeLabel = 'Rechazado';
                  $badgeClass = 'verification-badge--rechazada';
              } elseif (in_array($estadoVerificacion, ['pendiente', 'en_revision', 'en revisión'], true)) {
                  $badgeLabel = 'Pendiente';
                  $badgeClass = 'verification-badge--pendiente';
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
            @endphp

            <h2 class="card__title">Tu terapeuta</h2>
            <p class="card__description">
              Actualmente estás vinculado con:
              <button type="button" class="therapist-profile-link" id="openTherapistModal">
                {{ $nombreCompletoTerapeuta }}
              </button>
            </p>

            <div class="therapist-modal" id="therapistModal" role="dialog" aria-modal="true" aria-labelledby="therapistModalTitle" aria-hidden="true">
              <div class="therapist-modal__overlay" data-close-therapist-modal></div>
              <div class="therapist-modal__content" tabindex="-1">
                <button type="button" class="therapist-modal__close" id="closeTherapistModal" aria-label="Cerrar perfil del terapeuta">
                  &times;
                </button>

                <h2 class="therapist-modal__title" id="therapistModalTitle">Perfil del terapeuta</h2>

                <div class="therapist-modal__header">
                  @if ($fotoTerapeuta)
                    <img class="therapist-modal__avatar" src="{{ $fotoTerapeuta }}" alt="Foto de {{ $nombreCompletoTerapeuta }}">
                  @else
                    <div class="therapist-modal__avatar therapist-modal__avatar--initials" aria-hidden="true">
                      {{ $inicialesTerapeuta }}
                    </div>
                  @endif

                  <div>
                    <h3 class="therapist-modal__name">{{ $nombreCompletoTerapeuta }}</h3>
                    @if (!empty($terapeuta->correo))
                      <p class="therapist-modal__email">{{ $terapeuta->correo }}</p>
                    @endif
                    <span class="verification-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                  </div>
                </div>

                <div class="therapist-modal__grid">
                  @if (!empty($terapeuta->especialidad))
                    <div class="therapist-info-item">
                      <span>Especialidad</span>
                      <strong>{{ $terapeuta->especialidad }}</strong>
                    </div>
                  @endif

                  @if (!empty($terapeuta->experiencia_anios))
                    <div class="therapist-info-item">
                      <span>Años de experiencia</span>
                      <strong>{{ $terapeuta->experiencia_anios }}</strong>
                    </div>
                  @endif

                  @if (!empty($modalidadAtencion))
                    <div class="therapist-info-item">
                      <span>Modalidad de terapia</span>
                      <strong>{{ $modalidadAtencion }}</strong>
                    </div>
                  @endif

                  @if (!empty($terapeuta->nacionalidad))
                    <div class="therapist-info-item">
                      <span>Nacionalidad</span>
                      <strong>{{ $terapeuta->nacionalidad }}</strong>
                    </div>
                  @endif

                  @if ($telefonoCompleto !== '')
                    <div class="therapist-info-item">
                      <span>Teléfono de contacto</span>
                      <strong>{{ $telefonoCompleto }}</strong>
                    </div>
                  @endif

                  @if (!empty($terapeuta->cedula_profesional))
                    <div class="therapist-info-item">
                      <span>Cédula profesional</span>
                      <strong>{{ $terapeuta->cedula_profesional }}</strong>
                    </div>
                  @endif

                  @if (!empty($terapeuta->institucion_formacion))
                    <div class="therapist-info-item">
                      <span>Institución de formación</span>
                      <strong>{{ $terapeuta->institucion_formacion }}</strong>
                    </div>
                  @endif

                  @if (!empty($terapeuta->enfoque_terapeutico))
                    <div class="therapist-info-item">
                      <span>Enfoque terapéutico</span>
                      <strong>{{ $terapeuta->enfoque_terapeutico }}</strong>
                    </div>
                  @endif
                </div>

                @if (!empty($terapeuta->biografia))
                  <div class="therapist-info-item therapist-info-item--full">
                    <span>Biografía</span>
                    <p>{{ $terapeuta->biografia }}</p>
                  </div>
                @endif

                <div class="therapist-info-item therapist-info-item--full">
                  @if ($mostrarLugarAtencion)
                    <span>Lugar de atención</span>
                    <p>{{ $direccionCompleta !== '' ? $direccionCompleta : 'No especificado' }}</p>
                  @else
                    <span>Atención en línea</span>
                    <p>Sesiones disponibles en modalidad online.</p>
                  @endif
                </div>
              </div>
            </div>
          @endif
        </article>
      </section>

    </div> 
  </main>

  <script>
    const menuButton = document.getElementById('menuButton');
    const dropdownMenu = document.getElementById('dropdownMenu');

    if (menuButton && dropdownMenu) {
      menuButton.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdownMenu.classList.toggle('show');
      });

      window.addEventListener('click', (e) => {
        if (!menuButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
          dropdownMenu.classList.remove('show');
        }
      });
    }

    const modal = document.getElementById('therapistModal');
    const openBtn = document.getElementById('openTherapistModal');
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

      const content = modal.querySelector('.therapist-modal__content');
      if (content) {
        content.scrollTop = 0;
      }

      closeBtn?.focus();
    }

    function closeModal() {
      if (!modal) return;

      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('modal-open');
      document.body.style.top = '';

      window.scrollTo(0, scrollY);
      openBtn?.focus();
    }

    openBtn?.addEventListener('click', function (event) {
      event.preventDefault();
      openModal();
    });

    closeBtn?.addEventListener('click', function (event) {
      event.preventDefault();
      closeModal();
    });

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
</body>
</html>
