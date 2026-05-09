<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="dashboard.css" />
</head>
<body>

  <header class="header">
    <div class="header__container">
      <a class="brand" href="#">
        <span class="brand__icon" aria-hidden="true">✳︎</span>
        <span class="brand__title">¿Cómo te sientes hoy?</span>
      </a>

      <nav class="header__actions" aria-label="Acciones de usuario">
        <a class="header__link" href="#">Secondary</a>
        <a class="header__button" href="#">Mi cuenta</a>
      </nav>
    </div>
  </header>

  <main class="main">
    <div class="main__container">

      <section class="hero"></section>

      <section class="content">
        <div class="cards">

         <article class="card">
  <a href="citas.html" class="card__link">

    <img
      src="pexels-cottonbro-4101143.jpg"
      alt="Mis próximas citas"
      class="card__image"
    />

    <h2 class="card__title">Mis próximas citas</h2>
    <p class="card__description">
      Consulta o agenda una cita con tu psicólogo
    </p>

  </a>
</article>

          <article class="card">
  <a href="diario.html" class="card__link">

    <img
      src="pexels-pixabay-208147.jpg"
      alt="Diario de emociones"
      class="card__image"
    />

    <h2 class="card__title">Diario de emociones</h2>
    <p class="card__description">
      Registra tus emociones del día a día
    </p>

  </a>
</article>

          <article class="card">
  <a href="ayuda.html" class="card__link">

    <img
      src="pexels-rdne-5530681.jpg"
      alt="Recursos de ayuda"
      class="card__image"
    />

    <h2 class="card__title">Recursos de ayuda</h2>
    <p class="card__description">
      Explora recursos de ayuda
    </p>

  </a>
</article>

        </div>
      </section>

    </div>
  </main>

</body>
</html>