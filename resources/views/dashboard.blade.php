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
          @if (!session('terapeuta_vinculado'))
            <h2 class="card__title">Vincular terapeuta</h2>
            <p class="card__description" style="margin-bottom:18px;">
              Aún no tienes un terapeuta vinculado.
            </p>
            <p style="margin-bottom:20px; color:#6b7280; line-height:1.7;">
              Ingresa el PIN de vinculación proporcionado por tu terapeuta para conectar tu cuenta.
            </p>

            <!-- Mensajes de Estado -->
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
            <h2 class="card__title">Tu terapeuta</h2>
            <p class="card__description">
              Actualmente estás vinculado con: <strong>{{ session('nombre_terapeuta') }}</strong>
            </p>
          @endif
        </article>
      </section>

    </div> 
  </main>

  <script>
    const menuButton = document.getElementById('menuButton');
    const dropdownMenu = document.getElementById('dropdownMenu');

    menuButton.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdownMenu.classList.toggle('show');
    });

    window.addEventListener('click', (e) => {
      if (!menuButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownMenu.classList.remove('show');
      }
    });
  </script>
</body>
</html>