<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mis pacientes</title>
  <link rel="stylesheet" href="terapeuta.css" />
</head>
<body>

  <!-- Header consistente -->
  <header class="header">
    <div class="header__container">
      <a class="brand" href="terapeuta.html">
        <span class="brand__icon" aria-hidden="true">✳︎</span>
        <span class="brand__title">Panel del terapeuta</span>
      </a>

      <nav class="header__center" aria-label="Navegación principal">
        <a class="header__link" href="dashboard.html">Inicio</a>
        <a class="header__link" href="pacientes.html">Mis pacientes</a>
      </nav>

      <nav class="header__actions" aria-label="Acciones de usuario">
        <a class="header__button" href="#">Mi cuenta</a>
      </nav>
    </div>
  </header>

  <main class="main">
    <div class="main__container">

      <section class="hero"></section>

      <section class="content">
        <h1 class="title">Mis pacientes</h1>
        <p class="card__description" style="margin-bottom: 12px;">
          Esta tabla se llenará dinámicamente cuando tengamos backend. Por ahora es información de ejemplo.
        </p>

        <!-- Buscador simple (front) -->
        <article class="card" style="margin-bottom: 14px;">
          <div class="form__group" style="max-width: 420px;">
            <label class="form__label" for="buscador">Buscar paciente</label>
            <input class="form__input" id="buscador" type="text" placeholder="Ej: María, Carlos..." />
          </div>
        </article>

        <!-- Tabla -->
        <article class="card">
          <div class="table-wrap">
            <table class="table" aria-label="Tabla de pacientes">
              <thead>
                <tr>
                  <th>Paciente</th>
                  <th>Inicio de terapia</th>
                  <th>Próxima cita</th>
                  <th>Razón de visita</th>
                  <th>Puntaje salud mental</th>
                </tr>
              </thead>
              <tbody id="tablaPacientes">

                <tr>
                  <td data-label="Paciente">
                    <a class="table__link" href="expediente.html?paciente=Mar%C3%ADa%20L%C3%B3pez">María López</a>
                  </td>
                  <td data-label="Inicio">02 dic 2025</td>
                  <td data-label="Próxima cita">20 dic 2025 — 10:30</td>
                  <td data-label="Razón">Ansiedad laboral y problemas de sueño</td>
                  <td data-label="Puntaje">
                    <span class="score score--mid">68/100</span>
                  </td>
                </tr>

                <tr>
                  <td data-label="Paciente">
                    <a class="table__link" href="expediente.html?paciente=Carlos%20Hern%C3%A1ndez">Carlos Hernández</a>
                  </td>
                  <td data-label="Inicio">10 nov 2025</td>
                  <td data-label="Próxima cita">21 dic 2025 — 16:00</td>
                  <td data-label="Razón">Estrés por cambios personales y baja motivación</td>
                  <td data-label="Puntaje">
                    <span class="score score--low">52/100</span>
                  </td>
                </tr>

                <tr>
                  <td data-label="Paciente">
                    <a class="table__link" href="expediente.html?paciente=Ana%20Mart%C3%ADnez">Ana Martínez</a>
                  </td>
                  <td data-label="Inicio">18 oct 2025</td>
                  <td data-label="Próxima cita">22 dic 2025 — 12:00</td>
                  <td data-label="Razón">Ataques de pánico y ansiedad social</td>
                  <td data-label="Puntaje">
                    <span class="score score--low">48/100</span>
                  </td>
                </tr>

              </tbody>
            </table>
          </div>
        </article>
      </section>

    </div>
  </main>

  <script>
    // Filtro simple (front) por nombre/razón
    const buscador = document.getElementById('buscador');
    const filas = () => Array.from(document.querySelectorAll('#tablaPacientes tr'));

    buscador.addEventListener('input', () => {
      const q = buscador.value.toLowerCase().trim();

      filas().forEach(tr => {
        const texto = tr.innerText.toLowerCase();
        tr.style.display = texto.includes(q) ? '' : 'none';
      });
    });
  </script>

</body>
</html>