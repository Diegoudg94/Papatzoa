<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Confirmar citas</title>
  <link rel="stylesheet" href="terapeuta.css" />
</head>
<body>

  <header class="header">
    <div class="header__container">
      <a class="brand" href="terapeuta.html">
        <span class="brand__icon" aria-hidden="true">✳︎</span>
        <span class="brand__title">Confirmar citas</span>
      </a>

      <nav class="header__center" aria-label="Navegación principal">
        <a class="header__link" href="terapeuta.html">Volver</a>
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
        <h1 class="title">Citas pendientes por confirmar</h1>

        <article class="card">
          <p class="card__description" style="margin-bottom: 12px;">
            Confirma o rechaza citas solicitadas por pacientes. Puedes agregar un comentario opcional.
          </p>

          <div class="table-wrap">
            <table class="table" aria-label="Tabla de citas pendientes por confirmar">
              <thead>
                <tr>
                  <th>Paciente</th>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Motivo / descripción</th>
                  <th>Acciones</th>
                </tr>
              </thead>

              <tbody>
                <tr>
                  <td data-label="Paciente"><a class="table__link" href="#">María López</a></td>
                  <td data-label="Fecha">23 diciembre 2025</td>
                  <td data-label="Hora">09:00</td>
                  <td data-label="Motivo">Ansiedad laboral y problemas de sueño.</td>
                  <td data-label="Acciones">
                    <div class="actions">
                      <button class="btn btn--accept" type="button">Aceptar</button>
                      <button class="btn btn--reject" type="button">Rechazar</button>

                      <div class="comment">
                        <input class="comment__input" type="text" placeholder="Comentario (opcional)" />
                        <button class="btn btn--comment" type="button">Enviar comentario</button>
                      </div>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td data-label="Paciente"><a class="table__link" href="#">Carlos Hernández</a></td>
                  <td data-label="Fecha">24 diciembre 2025</td>
                  <td data-label="Hora">15:30</td>
                  <td data-label="Motivo">Estrés por cambios personales y baja motivación.</td>
                  <td data-label="Acciones">
                    <div class="actions">
                      <button class="btn btn--accept" type="button">Aceptar</button>
                      <button class="btn btn--reject" type="button">Rechazar</button>

                      <div class="comment">
                        <input class="comment__input" type="text" placeholder="Comentario (opcional)" />
                        <button class="btn btn--comment" type="button">Enviar comentario</button>
                      </div>
                    </div>
                  </td>
                </tr>

              </tbody>
            </table>
          </div>
        </article>
      </section>

    </div>
  </main>

</body>
</html>