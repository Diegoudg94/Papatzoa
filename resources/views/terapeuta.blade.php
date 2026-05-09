<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vista terapeuta</title>
  <link rel="stylesheet" href="terapeuta.css" />
</head>
<body>

  <header class="header">
    <div class="header__container">
      <a class="brand" href="dashboard.html">
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

      <!-- =======================
           SECCIÓN 1: Próximas citas
           ======================= -->
      <section class="content">
        <h1 class="title">Próximas citas</h1>

        <article class="card">
          <p class="card__description" style="margin-bottom: 12px;">
            Aquí verás las próximas citas agendadas por tus pacientes. (Por ahora es información de ejemplo.)
          </p>

          <div class="table-wrap">
            <table class="table" aria-label="Tabla de próximas citas">
              <thead>
                <tr>
                  <th>Paciente</th>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Motivo / descripción</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td data-label="Paciente">
                    <a class="table__link" href="expediente.html?paciente=Maria%20L%C3%B3pez">María López</a>
                  </td>
                  <td data-label="Fecha">20 diciembre 2025</td>
                  <td data-label="Hora">10:30</td>
                  <td data-label="Motivo">Ansiedad laboral y problemas de sueño.</td>
                </tr>

                <tr>
                  <td data-label="Paciente">
                    <a class="table__link" href="expediente.html?paciente=Carlos%20Hern%C3%A1ndez">Carlos Hernández</a>
                  </td>
                  <td data-label="Fecha">21 diciembre 2025</td>
                  <td data-label="Hora">16:00</td>
                  <td data-label="Motivo">Estrés por cambios personales y baja motivación.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </article>
      </section>

      <!-- ==================================
           SECCIÓN 2: Pendientes por confirmar
           ================================== -->
      <section class="content">
  <h2 class="title">Citas pendientes por confirmar</h2>

  <article class="card">
    <p class="card__description">
      Tienes <strong>2</strong> citas pendientes por confirmar. <!--NUMERO DINAMICO-->
      <a class="table__link" href="confirmar.html">Ir a confirmar</a>
    </p>
  </article>
</section>

    </div>
  </main>

  