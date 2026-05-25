<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  />

  <title>Confirmar citas</title>

  <link
    rel="stylesheet"
    href="{{ asset('css/terapeuta.css') }}"
  />
</head>

<body>

  <!-- HEADER -->
  <header class="header">

    <div class="header__container">

      <a class="brand" href="/terapeuta">

        <span
          class="brand__icon"
          aria-hidden="true"
        >
          ✳︎
        </span>

        <span class="brand__title">
          Confirmar citas
        </span>

      </a>

      <nav
        class="header__center"
        aria-label="Navegación principal"
      >

        <a
          class="header__link"
          href="/terapeuta"
        >
          Volver
        </a>

      </nav>

      <nav
        class="header__actions"
        aria-label="Acciones de usuario"
      >

        <a
          class="header__button"
          href="#"
        >
          Mi cuenta
        </a>

      </nav>

    </div>

  </header>


  <!-- CONTENIDO PRINCIPAL -->
  <main class="main">

    <div class="main__container">

      <section class="hero"></section>

      <section class="content">

        <h1 class="title">
          Citas pendientes por confirmar
        </h1>

        <article class="card">

          <p
            class="card__description"
            style="margin-bottom: 12px;"
          >
            Aquí aparecerán las solicitudes de cita enviadas por tus pacientes vinculados.
            Puedes aceptar o rechazar cada solicitud.
          </p>

          @if (session('success_confirmar'))
            <div
              class="card__description"
              style="
                margin-bottom: 16px;
                padding: 12px;
                border-radius: 12px;
                background: #dcfce7;
                color: #166534;
                border: 1px solid #86efac;
              "
            >
              {{ session('success_confirmar') }}
            </div>
          @endif

          <div class="table-wrap">

            <table
              class="table"
              aria-label="Tabla de citas pendientes por confirmar"
            >

              <thead>
                <tr>
                  <th>Paciente</th>
                  <th>Fecha solicitada</th>
                  <th>Hora tentativa</th>
                  <th>Motivo / descripción</th>
                  <th>Acciones</th>
                </tr>
              </thead>

              <tbody>

                @forelse ($solicitudes as $solicitud)

                  <tr>

                    <td data-label="Paciente">
                      <a
                        class="table__link"
                        href="/expediente/{{ $solicitud->paciente_id }}"
                      >
                        {{ $solicitud->nombre }} {{ $solicitud->apellido }}
                      </a>
                    </td>

                    <td data-label="Fecha solicitada">
                      {{ $solicitud->fecha }}
                    </td>

                    <td data-label="Hora tentativa">
                      {{ $solicitud->hora }}
                    </td>

                    <td data-label="Motivo">
                      @php
                        try {
                            echo \Illuminate\Support\Facades\Crypt::decryptString($solicitud->motivo);
                        } catch (\Exception $e) {
                            echo $solicitud->motivo;
                        }
                      @endphp
                    </td>

                    <td data-label="Acciones">

                      <div class="actions">

                        <!-- ACEPTAR CITA -->
                        <form
                          action="/citas/{{ $solicitud->id }}/aceptar"
                          method="POST"
                        >
                          @csrf

                          <button
                            class="btn btn--accept"
                            type="submit"
                          >
                            Aceptar
                          </button>

                        </form>


                        <!-- RECHAZAR CITA -->
                        <form
                          action="/citas/{{ $solicitud->id }}/rechazar"
                          method="POST"
                        >
                          @csrf

                          <div class="comment">

                            <input
                              class="comment__input"
                              type="text"
                              name="comentario"
                              placeholder="Comentario opcional"
                            />

                            <button
                              class="btn btn--reject"
                              type="submit"
                            >
                              Rechazar
                            </button>

                          </div>

                        </form>

                      </div>

                    </td>

                  </tr>

                @empty

                  <tr>
                    <td
                      colspan="5"
                      style="text-align:center;"
                    >
                      No tienes citas pendientes por confirmar.
                    </td>
                  </tr>

                @endforelse

              </tbody>

            </table>

          </div>

        </article>

      </section>

    </div>

  </main>

</body>

</html>