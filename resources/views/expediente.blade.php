@php
  $activeTab = request('tab', 'emociones');

  if (! in_array($activeTab, ['emociones', 'citas', 'notas'], true)) {
    $activeTab = 'emociones';
  }
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Expediente del paciente</title>
  <link rel="stylesheet" href="{{ asset('css/terapeuta.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/expediente.css') }}" />
</head>
<body>

  <header class="header">
    <div class="header__container">
      <a class="brand" href="/terapeuta">
        <span class="brand__icon" aria-hidden="true">✳︎</span>
        <span class="brand__title">Expediente</span>
      </a>

      <nav class="header__center" aria-label="Navegación principal">
        <a class="header__link" href="/terapeuta">Inicio</a>
        <a class="header__link" href="/pacientes">Pacientes</a>
      </nav>

      <nav class="header__actions" aria-label="Acciones de usuario">
        <a class="header__button" href="/logout">Cerrar sesión</a>
      </nav>
    </div>
  </header>

  <main class="main">
    <div class="main__container">

      <section class="hero"></section>

      <section class="content">
        <h1 class="title">
          Paciente:
          <span class="patient-name">{{ $paciente->nombre }} {{ $paciente->apellido }}</span>
        </h1>
        <p class="card__description">
          Expediente clínico con registros compartidos por el paciente y notas privadas del terapeuta.
        </p>

        @if (session('success_expediente'))
          <div class="alert alert--success" style="margin-top:14px;">
            {{ session('success_expediente') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="alert alert--error" style="margin-top:14px;">
            Revisa la nota antes de guardarla.
          </div>
        @endif
      </section>

      <section class="content">
        <div class="tabs" role="tablist" aria-label="Secciones del expediente">
          <button class="tab {{ $activeTab === 'emociones' ? 'is-active' : '' }}" type="button" data-tab="emociones">Emociones</button>
          <button class="tab {{ $activeTab === 'citas' ? 'is-active' : '' }}" type="button" data-tab="citas">Citas / Sesiones</button>
          <button class="tab {{ $activeTab === 'notas' ? 'is-active' : '' }}" type="button" data-tab="notas">Notas</button>
        </div>
      </section>

      <section class="content tab-panel" id="tab-emociones" {{ $activeTab === 'emociones' ? '' : 'hidden' }}>
        <h2 class="title">Emociones registradas</h2>

        <article class="card emotion-dashboard-card">
          <div class="table-wrap emotion-table-wrap">
            <table class="table expediente-emociones-table" aria-label="Emociones registradas">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Emoción</th>
                  <th>Intensidad</th>
                  <th>Detalles</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($emociones as $emocion)
                  @php
                    $intensity = is_null($emocion->intensidad) ? null : (int) $emocion->intensidad;
                    $intensityClass = 'intensity-empty';
                    $intensityLabel = 'Sin dato';
                    $intensityText = 'Sin dato';

                    if (! is_null($intensity)) {
                      if ($intensity <= 2) {
                        $intensityClass = 'intensity-low';
                        $intensityLabel = 'Baja';
                      } elseif ($intensity <= 4) {
                        $intensityClass = 'intensity-mild';
                        $intensityLabel = 'Leve';
                      } elseif ($intensity <= 6) {
                        $intensityClass = 'intensity-medium';
                        $intensityLabel = 'Media';
                      } elseif ($intensity <= 8) {
                        $intensityClass = 'intensity-high';
                        $intensityLabel = 'Alta';
                      } else {
                        $intensityClass = 'intensity-critical';
                        $intensityLabel = 'Muy alta';
                      }

                      $intensityText = $intensity . '/10 · ' . $intensityLabel;
                    }
                  @endphp
                  <tr class="emotion-summary-row">
                    <td class="emotion-date-cell" data-label="Fecha">{{ $emocion->created_at->format('d/m/Y') }}</td>
                    <td class="emotion-time-cell" data-label="Hora">{{ $emocion->created_at->format('H:i') }}</td>
                    <td data-label="Emoción">
                      <span class="emotion-pill">
                        <span class="emotion-pill__dot" aria-hidden="true"></span>
                        {{ $emocion->emocion ?: 'Sin registro' }}
                      </span>
                    </td>
                    <td data-label="Intensidad">
                      <span class="intensity-badge {{ $intensityClass }}">{{ $intensityText }}</span>
                    </td>
                    <td class="emotion-actions-cell" data-label="Detalles">
                      <button
                        class="details-toggle"
                        type="button"
                        aria-expanded="false"
                        aria-controls="emocion-detalle-{{ $emocion->id }}"
                        data-target="emocion-detalle-{{ $emocion->id }}"
                      >
                        Ver más detalles
                      </button>
                    </td>
                  </tr>
                  <tr class="emotion-detail-row" id="emocion-detalle-{{ $emocion->id }}" hidden>
                    <td colspan="5">
                      <div class="emotion-detail-card">
                        <div class="emotion-detail-item">
                          <span class="emotion-detail-label">Situación</span>
                          <p>{{ $emocion->situacion ?: 'Sin información' }}</p>
                        </div>
                        <div class="emotion-detail-item">
                          <span class="emotion-detail-label">Pensamiento automático</span>
                          <p>{{ $emocion->pensamiento ?: 'Sin información' }}</p>
                        </div>
                        <div class="emotion-detail-item">
                          <span class="emotion-detail-label">Conducta</span>
                          <p>{{ $emocion->conducta ?: 'Sin información' }}</p>
                        </div>
                        <div class="emotion-detail-item">
                          <span class="emotion-detail-label">Interpretación</span>
                          <p>{{ $emocion->interpretacion ?: 'Sin información' }}</p>
                        </div>
                        <div class="emotion-detail-item emotion-detail-item--wide">
                          <span class="emotion-detail-label">Reestructuración</span>
                          <p>{{ $emocion->reestructuracion ?: 'Sin información' }}</p>
                        </div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td class="emotion-empty-state" colspan="5">Sin registros de emociones.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </article>
      </section>

      <section class="content tab-panel" id="tab-sesiones" {{ $activeTab === 'citas' ? '' : 'hidden' }}>
        <h2 class="title">Citas / Sesiones</h2>

        <article class="card clinical-table-card">
          <div class="table-wrap clinical-table-wrap">
            <table class="table clinical-table clinical-table--sessions expediente-table" aria-label="Citas y sesiones">
              <thead>
                <tr>
                  <th class="cell-center">Fecha</th>
                  <th class="cell-center">Hora</th>
                  <th>Motivo</th>
                  <th class="cell-center">Estado</th>
                  <th class="cell-center">Notas</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($citas as $cita)
                  @php
                    $estado = strtolower($cita->estado ?: 'pendiente');
                    $estadoClass = match ($estado) {
                      'aceptada', 'aceptado', 'confirmada', 'confirmado' => 'status-badge--accepted',
                      'cancelada', 'cancelado', 'rechazada', 'rechazado' => 'status-badge--cancelled',
                      default => 'status-badge--pending',
                    };
                    $notasDeEstaCita = $notasSesion->where('cita_id', $cita->id);
                  @endphp
                  <tr class="session-summary-row">
                    <td class="cell-center cell-nowrap" data-label="Fecha">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                    <td class="cell-center cell-nowrap" data-label="Hora">
                      {{ $cita->hora ? \Carbon\Carbon::parse($cita->hora)->format('H:i') : 'Sin registro' }}
                    </td>
                    <td class="cell-text" data-label="Motivo">{{ $cita->motivo ?: 'Sin registro' }}</td>
                    <td class="cell-center" data-label="Estado">
                      <span class="status-badge {{ $estadoClass }}">{{ ucfirst($cita->estado ?: 'pendiente') }}</span>
                    </td>
                    <td class="cell-center" data-label="Notas">
                      <button
                        class="session-note-toggle"
                        type="button"
                        aria-expanded="false"
                        aria-controls="session-note-panel-{{ $cita->id }}"
                        data-target="session-note-panel-{{ $cita->id }}"
                      >
                        Añadir / ver notas
                      </button>
                    </td>
                  </tr>
                  <tr class="session-note-row" id="session-note-panel-{{ $cita->id }}" hidden>
                    <td colspan="5">
                      <div class="session-note-panel">
                        <form class="session-note-form" method="POST" action="/expediente/{{ $paciente->id }}/citas/{{ $cita->id }}/nota">
                          @csrf

                          <div class="form__group">
                            <label class="form__label" for="nota-sesion-{{ $cita->id }}">Nueva nota</label>
                            <textarea
                              class="form__input session-note-textarea"
                              id="nota-sesion-{{ $cita->id }}"
                              name="nota"
                              rows="4"
                              maxlength="3000"
                              required
                            ></textarea>
                          </div>

                          <button class="button btn-compact session-note-submit" type="submit">Guardar nota</button>
                        </form>

                        <div class="session-note-history">
                          <h3 class="session-note-title">Notas anteriores</h3>

                          @forelse ($notasDeEstaCita as $notaSesion)
                            <article class="session-note-card">
                              <div class="session-note-card__header">
                                <div class="session-note-meta">
                                  {{ \Carbon\Carbon::parse($notaSesion->created_at)->format('d/m/Y H:i') }}
                                </div>
                                <div class="session-note-actions">
                                  <button
                                    class="button btn-compact btn-secondary session-note-edit-toggle"
                                    type="button"
                                    aria-expanded="false"
                                    aria-controls="session-note-edit-{{ $notaSesion->id }}"
                                    data-target="session-note-edit-{{ $notaSesion->id }}"
                                  >
                                    Editar
                                  </button>
                                  <form
                                    class="session-note-delete-form"
                                    method="POST"
                                    action="/expediente/{{ $paciente->id }}/citas/{{ $cita->id }}/nota/{{ $notaSesion->id }}"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar esta nota?')"
                                  >
                                    @csrf
                                    @method('DELETE')
                                    <button class="button btn-compact btn-danger" type="submit">Eliminar</button>
                                  </form>
                                </div>
                              </div>
                              <p class="session-note-text">{{ $notaSesion->nota }}</p>

                              <form
                                class="session-note-edit-form"
                                id="session-note-edit-{{ $notaSesion->id }}"
                                method="POST"
                                action="/expediente/{{ $paciente->id }}/citas/{{ $cita->id }}/nota/{{ $notaSesion->id }}"
                                hidden
                              >
                                @csrf
                                @method('PUT')

                                <label class="form__label" for="nota-sesion-edit-{{ $notaSesion->id }}">Editar nota</label>
                                <textarea
                                  class="form__input session-note-textarea"
                                  id="nota-sesion-edit-{{ $notaSesion->id }}"
                                  name="nota"
                                  rows="4"
                                  maxlength="3000"
                                  required
                                >{{ $notaSesion->nota }}</textarea>

                                <div class="session-note-actions session-note-actions--edit">
                                  <button class="button btn-compact session-note-submit" type="submit">Guardar cambios</button>
                                  <button class="button btn-compact btn-secondary session-note-edit-cancel" type="button">Cancelar</button>
                                </div>
                              </form>
                            </article>
                          @empty
                            <p class="session-note-empty">Aún no hay notas para esta sesión.</p>
                          @endforelse
                        </div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td class="table-empty-state" colspan="5">Sin citas registradas.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </article>
      </section>

      <section class="content tab-panel" id="tab-notas" {{ $activeTab === 'notas' ? '' : 'hidden' }}>
        <h2 class="title">Notas del terapeuta</h2>

        <article class="card">
          <div class="notes">
            <h3 class="card__title">Notas registradas</h3>

            @forelse ($notas as $nota)
              <div class="note">
                <div class="note__meta">{{ $nota->created_at->format('d/m/Y H:i') }}</div>
                <div class="note__text">{{ $nota->nota ?: 'No se pudo mostrar esta nota.' }}</div>
              </div>
            @empty
              <p class="card__description">Aún no hay notas registradas.</p>
            @endforelse
          </div>

          <form class="form" method="POST" action="/expediente/{{ $paciente->id }}/notas">
            @csrf

            <div class="form__group">
              <label class="form__label" for="nota">Nueva nota</label>
              <textarea
                class="form__input"
                id="nota"
                name="nota"
                rows="5"
                maxlength="3000"
                required
              >{{ old('nota') }}</textarea>
              @error('nota')
                <p class="form__error">{{ $message }}</p>
              @enderror
            </div>

            <button class="button" type="submit">Guardar nota</button>
          </form>
        </article>
      </section>

    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const tabButtons = document.querySelectorAll('.tab');
      const panels = {
        emociones: document.getElementById('tab-emociones'),
        citas: document.getElementById('tab-sesiones'),
        notas: document.getElementById('tab-notas'),
      };

      tabButtons.forEach((button) => {
        button.addEventListener('click', () => {
          const key = button.dataset.tab;

          if (! panels[key]) {
            return;
          }

          tabButtons.forEach((item) => item.classList.remove('is-active'));
          button.classList.add('is-active');

          Object.values(panels).forEach((panel) => {
            panel.hidden = true;
          });

          panels[key].hidden = false;
        });
      });

      document.querySelectorAll('.details-toggle').forEach((button) => {
        button.addEventListener('click', () => {
          const detailRow = document.getElementById(button.dataset.target);
          const summaryRow = button.closest('.emotion-summary-row');
          if (! detailRow || ! summaryRow) {
            return;
          }

          const isExpanded = button.getAttribute('aria-expanded') === 'true';

          button.setAttribute('aria-expanded', String(!isExpanded));
          button.textContent = isExpanded ? 'Ver más detalles' : 'Ocultar detalles';
          detailRow.hidden = isExpanded;
          summaryRow.classList.toggle('is-expanded', !isExpanded);
        });
      });

      document.querySelectorAll('.session-note-toggle').forEach((button) => {
        button.addEventListener('click', () => {
          const panelRow = document.getElementById(button.dataset.target);
          const summaryRow = button.closest('.session-summary-row');
          if (! panelRow || ! summaryRow) {
            return;
          }

          const isExpanded = button.getAttribute('aria-expanded') === 'true';

          button.setAttribute('aria-expanded', String(!isExpanded));
          button.textContent = isExpanded ? 'Añadir / ver notas' : 'Ocultar';
          panelRow.hidden = isExpanded;
          summaryRow.classList.toggle('is-expanded', !isExpanded);
        });
      });

      document.querySelectorAll('.session-note-edit-toggle').forEach((button) => {
        button.addEventListener('click', () => {
          const editForm = document.getElementById(button.dataset.target);
          if (! editForm) {
            return;
          }

          const isExpanded = button.getAttribute('aria-expanded') === 'true';

          button.setAttribute('aria-expanded', String(!isExpanded));
          editForm.hidden = isExpanded;
        });
      });

      document.querySelectorAll('.session-note-edit-cancel').forEach((button) => {
        button.addEventListener('click', () => {
          const editForm = button.closest('.session-note-edit-form');
          if (! editForm) {
            return;
          }

          const toggle = document.querySelector(`.session-note-edit-toggle[data-target="${editForm.id}"]`);
          editForm.hidden = true;

          if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
          }
        });
      });
    });
  </script>

@include('partials.marker-widget')
</body>
</html>
