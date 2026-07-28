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
  <main class="confirmar-citas-page">

    <div class="confirmar-citas-container">

      <header class="confirmar-citas-header">

        <h1 class="confirmar-citas-title">
          Citas pendientes por confirmar
        </h1>

        <p class="confirmar-citas-subtitle">
          Aquí aparecerán las solicitudes de cita enviadas por tus pacientes vinculados.
          Puedes aceptar o rechazar cada solicitud.
        </p>

      </header>

      @if (session('success_confirmar'))
        <div class="confirmar-citas-alert">
          {{ session('success_confirmar') }}
        </div>
      @endif

      @if (session('error_confirmar'))
        <div class="confirmar-citas-alert" style="background:#fee2e2; color:#991b1b; border-color:#fca5a5;">
          {{ session('error_confirmar') }}
        </div>
      @endif

      <section
        class="appointment-request-list"
        aria-label="Solicitudes de citas pendientes por confirmar"
      >

        @forelse ($solicitudes as $solicitud)

          @php
            $motivoCifrado = $solicitud->motivo_encrypted ?? $solicitud->motivo ?? null;
            $inicioSolicitud = $solicitud->starts_at
                ? \Carbon\Carbon::parse($solicitud->starts_at)->setTimezone($solicitud->timezone ?: 'America/Mexico_City')
                : \Carbon\Carbon::parse(($solicitud->fecha ?? now()->toDateString()) . ' ' . ($solicitud->hora ?: '00:00'));

            try {
                $motivo = $motivoCifrado
                    ? \Illuminate\Support\Facades\Crypt::decryptString($motivoCifrado)
                    : 'Sin registro';
            } catch (\Exception $e) {
                $motivo = 'No se pudo mostrar este dato.';
            }
          @endphp

          <article class="appointment-request-card">

            <div class="appointment-request-grid">

              <div class="appointment-request-info appointment-request-info--patient">
                <span class="appointment-request-label">Paciente</span>

                <a
                  class="appointment-request-patient"
                  href="/expediente/{{ $solicitud->paciente_id }}"
                >
                  {{ $solicitud->nombre }} {{ $solicitud->apellido }}
                </a>
              </div>

              <div class="appointment-request-info">
                <span class="appointment-request-label">Fecha solicitada</span>
                <span class="appointment-request-value">{{ $inicioSolicitud->format('d/m/Y') }}</span>
              </div>

              <div class="appointment-request-info">
                <span class="appointment-request-label">Hora</span>
                <span class="appointment-request-value">{{ $inicioSolicitud->format('H:i') }}</span>
              </div>

              <div class="appointment-request-info">
                <span class="appointment-request-label">Motivo</span>
                <span class="appointment-request-value appointment-request-reason">{{ $motivo }}</span>
              </div>

            </div>

            <div class="appointment-actions">

              <!-- ACEPTAR CITA -->
              <form
                class="appointment-accept-form"
                action="/citas/{{ $solicitud->id }}/aceptar"
                method="POST"
              >
                @csrf

                <button
                  class="btn-accept"
                  type="submit"
                >
                  Aceptar
                </button>

              </form>

              <!-- RECHAZAR CITA -->
              <form
                class="appointment-reject-form"
                action="/citas/{{ $solicitud->id }}/rechazar"
                method="POST"
              >
                @csrf

                <label
                  class="comment-label"
                  for="comentario-{{ $solicitud->id }}"
                >
                  Comentario opcional
                </label>

                <div class="appointment-actions-row">

                  <input
                    id="comentario-{{ $solicitud->id }}"
                    class="comment-input"
                    type="text"
                    name="comentario"
                    placeholder="Comentario opcional"
                  />

                  <button
                    class="btn-reject"
                    type="submit"
                  >
                    Rechazar
                  </button>

                </div>

              </form>

            </div>

          </article>

        @empty

          <article class="appointment-request-card appointment-request-card--empty">
            No tienes citas pendientes por confirmar.
          </article>

        @endforelse

      </section>

    </div>

  </main>

@include('partials.marker-widget')
</body>

</html>
