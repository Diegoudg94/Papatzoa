<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Citas</title>
  <link rel="stylesheet" href="{{ asset('css/citas.css') }}" />
  <style>
  .appointment-list {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 28px !important;
    margin-top: 24px !important;
  }

  .appointment-item {
    display: block !important;
    padding: 28px 30px !important;
    border-radius: 26px !important;
    background: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    box-shadow: 0 18px 45px rgba(17, 24, 39, 0.08) !important;
  }

  .appointment-info {
    display: grid !important;
    gap: 12px !important;
  }

  .appointment-info h3 {
    margin: 0 0 12px 0 !important;
    padding-bottom: 14px !important;
    border-bottom: 1px solid #e5e7eb !important;
    color: #111827 !important;
    font-size: 1.45rem !important;
  }

  .appointment-info p {
    margin: 0 !important;
    color: #374151 !important;
    line-height: 1.6 !important;
  }

  .appointment-info strong {
    color: #111827 !important;
  }

  @media (max-width: 900px) {
    .appointment-list {
      grid-template-columns: 1fr !important;
    }
  }
</style>
</head>

<body>

  <header class="header">
    <div class="header__container">

      <a class="brand" href="/dashboard">
        <span class="brand__icon" aria-hidden="true">✳︎</span>
        <span class="brand__title">¿Cómo te sientes hoy?</span>
      </a>

      <nav class="header__center" aria-label="Navegación principal">
        <a class="header__link header__link--active" href="/dashboard">
          Inicio
        </a>
      </nav>

      <nav class="header__actions" aria-label="Acciones de usuario">
        <a class="header__button" href="#">
          Mi cuenta
        </a>
      </nav>

    </div>
  </header>

  <main class="main">
    <div class="main__container">


      <section class="content">
        <h1 class="title">Citas con tu terapeuta</h1>

        <article class="card appointments-card">
          <div class="appointments-header">
            <div>
              <h2 class="card__title">Solicitudes y citas</h2>
              <p class="card__description">
                Aquí puedes consultar tus citas solicitadas, confirmadas o rechazadas por tu terapeuta.
              </p>
            </div>
          </div>

          @if ($citas->isEmpty())
            <div class="empty-state">
              <h3>No tienes citas solicitadas o agendadas.</h3>
              <p>
                Cuando envíes una solicitud, aparecerá aquí con el estatus correspondiente.
              </p>
            </div>
          @else
            <div class="appointment-list">
              @foreach ($citas as $cita)
                @php
                  $estadoClase = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú'], ['-', 'a', 'e', 'i', 'o', 'u'], $cita->estado));
                @endphp
                <article class="appointment-item appointment-item--{{ $estadoClase ?? strtolower($cita->estado) }}">

                  <div class="appointment-info">
                    <h3>
                      Cita con Psicólogo
                    </h3>

                    <p>
                      <strong>Fecha:</strong>
                      {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                    </p>

                    <p>
                      <strong>Hora tentativa:</strong>
                      {{ \Carbon\Carbon::parse($cita->hora)->format('H:i') }}
                    </p>

                    <p>
                      <strong>Tema a tratar:</strong>
                      @php
                        try {
                            echo \Illuminate\Support\Facades\Crypt::decryptString($cita->motivo);
                        } catch (\Exception $e) {
                            echo $cita->motivo;
                        }
                      @endphp
                    </p>

                    @php
  $estado = strtolower($cita->estado);

  if ($estado === 'aceptada' || $estado === 'aceptado') {
      $statusStyle = 'background:#dcfce7; color:#166534; border:1px solid #86efac;';
  } elseif ($estado === 'rechazada' || $estado === 'rechazado') {
      $statusStyle = 'background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;';
  } else {
      $statusStyle = 'background:#fff7ed; color:#c2410c; border:1px solid #fed7aa;';
  }
@endphp

<p>
  <strong>Status:</strong>
  <span
    style="
      display:inline-block;
      margin-left:6px;
      padding:7px 14px;
      border-radius:999px;
      font-weight:800;
      font-size:0.92rem;
      {{ $statusStyle }}
    "
  >
    {{ ucfirst($cita->estado) }}
  </span>
</p>
                  </div>

                </article>
              @endforeach
            </div>
          @endif
        </article>
      </section>

      <section class="content">
        <h2 class="card__title">Solicitar una cita</h2>

        <form action="/citas/solicitar" method="POST">
          @csrf

          <fieldset class="card">
            <legend class="form__legend"></legend>

            <p class="card__description" style="margin-bottom: 18px;">
              Selecciona una fecha y un horario tentativo para tu cita. Tu terapeuta revisará la solicitud y la confirmará en cuanto sea posible.
            </p>

            @if (session('success_cita'))
              <div class="alert alert--success">
                {{ session('success_cita') }}
              </div>
            @endif

            @if (session('error_cita'))
              <div class="alert alert--error">
                {{ session('error_cita') }}
              </div>
            @endif

            <div class="form__group">
              <label class="form__label" for="fecha">¿Qué fecha quieres?</label>
              <input
                class="form__input"
                type="date"
                id="fecha"
                name="fecha"
                required
              >
            </div>

            <div class="form__group">
              <label class="form__label" for="hora">Selecciona una hora tentativa</label>
              <input
                class="form__input"
                type="time"
                id="hora"
                name="hora"
                required
              >
            </div>

            <div class="form__group">
              <label class="form__label" for="motivo">Motivo / temas a tratar</label>
              <textarea
                class="form__input"
                id="motivo"
                name="motivo"
                rows="5"
                placeholder="Ej: ansiedad, estrés, conflictos personales..."
                required
              ></textarea>
            </div>

            <button class="button" type="submit">
              Solicitar cita
            </button>
          </fieldset>
        </form>
      </section>

    </div>
  </main>

</body>

</html>