<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Citas</title>
  <link rel="stylesheet" href="citas.css" />
</head>
<body>

  <header class="header">
  <div class="header__container">

    <!-- Izquierda: logo -->
    <a class="brand" href="dashboard.html">
      <span class="brand__icon" aria-hidden="true">✳︎</span>
      <span class="brand__title">¿Cómo te sientes hoy?</span>
    </a>

    <!-- Centro: Inicio -->
    <nav class="header__center" aria-label="Navegación principal">
      <a class="header__link header__link--active" href="dashboard.html">
        Inicio
      </a>
    </nav>

    <!-- Derecha: cuenta -->
    <nav class="header__actions" aria-label="Acciones de usuario">
      <a class="header__button" href="#">Mi cuenta</a>
    </nav>

  </div>
</header>

  <main class="main">
    <div class="main__container">

      <section class="hero"></section>

      <!-- Cuadro: próxima cita -->
      <section class="content">
        <h1 class="title">Mi próxima cita</h1>

        <article class="card">
  <div class="card__image" aria-hidden="true"></div>
  <h2 class="card__title">Cita con Psicólogo</h2>

  <p class="card__description" id="citaVacia">
    No tienes citas agendadas.
  </p>

  <p class="card__description" id="citaDetalle" hidden>
    <strong>Fecha:</strong> <span id="citaFecha"></span><br>
    <strong>Hora:</strong> <span id="citaHora"></span><br>
    <strong>Motivo:</strong> <span id="citaMotivo"></span><br>
    <strong>Estatus:</strong> <span id="citaEstatus"></span>
  </p>

  <button
    class="button button--secondary"
    id="btnCancelarCita"
    type="button"
    hidden
  >
    Cancelar cita
  </button>
</article>
      </section>

      <!-- Formulario: agendar cita -->
      <section class="content">
        <h2 class="card__title">Agendar una cita</h2>

        <form class="form" id="formCita" action="#" method="post" novalidate>
          <fieldset class="form__fieldset">
            <legend class="form__legend">Detalles de la cita</legend>

            <div class="form__group">
              <label class="form__label" for="fecha">¿Qué fecha quieres?</label>
              <input class="form__input" type="date" id="fecha" name="fecha" required />
            </div>

            <div class="form__group">
              <label class="form__label" for="hora">Selecciona una hora</label>
              <input class="form__input" type="time" id="hora" name="hora" required />
            </div>

            <div class="form__group">
              <label class="form__label" for="motivo">Motivo / temas a tratar</label>
              <textarea
                class="form__input"
                id="motivo"
                name="motivo"
                rows="4"
                placeholder="Ej: ansiedad, estrés, conflictos personales..."
                required
              ></textarea>
            </div>
          </fieldset>

          <button class="button" type="submit">Agendar</button>
        </form>
      </section>

    </div>
  </main>

  <script>
  const form = document.getElementById('formCita');
  const btnCancelar = document.getElementById('btnCancelarCita');

  const citaVacia = document.getElementById('citaVacia');
  const citaDetalle = document.getElementById('citaDetalle');

  const citaFecha = document.getElementById('citaFecha');
  const citaHora = document.getElementById('citaHora');
  const citaMotivo = document.getElementById('citaMotivo');
  const citaEstatus = document.getElementById('citaEstatus');

  // Al agendar cita
  form.addEventListener('submit', (e) => {
    e.preventDefault();

    const fechaValue = document.getElementById('fecha').value;
    const horaValue = document.getElementById('hora').value;
    const motivoValue = document.getElementById('motivo').value.trim();

    if (!fechaValue || !horaValue || !motivoValue) {
      alert('Por favor llena fecha, hora y motivo.');
      return;
    }

    const fechaFormateada = new Date(fechaValue + 'T00:00:00')
      .toLocaleDateString('es-MX', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
      });

    // Mostrar cita
    citaFecha.textContent = fechaFormateada;
    citaHora.textContent = horaValue;
    citaMotivo.textContent = motivoValue;
    citaEstatus.textContent = '(pendiente de confirmación)';

    citaVacia.hidden = true;
    citaDetalle.hidden = false;
    btnCancelar.hidden = false;

    form.reset();
  });

  // Al cancelar cita
  btnCancelar.addEventListener('click', () => {
    citaDetalle.hidden = true;
    btnCancelar.hidden = true;
    citaVacia.hidden = false;
  });
</script>