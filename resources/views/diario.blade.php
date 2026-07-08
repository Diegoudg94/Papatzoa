<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Papatzoa - Diario de emociones</title>
  <link rel="stylesheet" href="{{ asset('css/terapeuta.css') }}">
  <link rel="stylesheet" href="{{ asset('css/diario.css') }}">
</head>
<body>

<header class="header">
  <div class="header__container">
    <a class="brand" href="/dashboard">
      <span class="brand__icon">✳︎</span>
      <span class="brand__title">Papatzoa</span>
    </a>
    <nav class="header__center">
      <a class="header__link" href="/dashboard">Inicio</a>
    </nav>
    <nav class="header__actions">
      <a class="header__button" href="/logout">Cerrar sesión</a>
    </nav>
  </div>
</header>

<main class="main diario-page">
  <div class="main__container diario-layout">

    <section class="content diario-form-panel" id="nuevo-registro">
      <div class="diario-heading">
        <div>
          <h1 class="title">Diario de emociones</h1>
          <p class="card__description">Registra una situación, pensamientos, emociones y reacciones.</p>
        </div>
        <a class="button button--secondary" href="/diario">Añadir nueva emoción</a>
      </div>

      @if (session('success_diario'))
        <div class="diario-alert diario-alert--success">
          {{ session('success_diario') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="diario-alert diario-alert--error">
          Revisa los campos marcados antes de guardar.
        </div>
      @endif

      <form class="diario-form" method="POST" action="/diario">
        @csrf

        <div class="progress" aria-label="Pasos del registro emocional">
          <div class="progress__step is-active" data-step="1">1. Situación</div>
          <div class="progress__line"></div>
          <div class="progress__step" data-step="2">2. Pensamiento</div>
          <div class="progress__line"></div>
          <div class="progress__step" data-step="3">3. Emoción</div>
          <div class="progress__line"></div>
          <div class="progress__step" data-step="4">4. Conducta</div>
          <div class="progress__line"></div>
          <div class="progress__step" data-step="5">5. Interpretación</div>
          <div class="progress__line"></div>
          <div class="progress__step" data-step="6">6. Reestructuración</div>
        </div>

        <section class="content diario-step" id="step1">
          <article class="card">
            <h2 class="card__title">1) Situación o antecedente</h2>
            <p class="card__description">Describe brevemente qué ocurrió.</p>
            <textarea class="form__input" id="situacion" name="situacion" rows="4" placeholder="Ej: discutí con alguien...">{{ old('situacion') }}</textarea>
            @error('situacion') <p class="form__error">{{ $message }}</p> @enderror
            <div class="diario-actions">
              <button class="button" data-next="2" type="button">Siguiente</button>
            </div>
          </article>
        </section>

        <section class="content diario-step" id="step2" hidden>
          <article class="card">
            <h2 class="card__title">2) Pensamiento automático</h2>
            <p class="card__description">¿Qué pasó por tu mente?</p>
            <textarea class="form__input" id="pensamiento" name="pensamiento" rows="4" placeholder="Ej: voy a fracasar">{{ old('pensamiento') }}</textarea>
            @error('pensamiento') <p class="form__error">{{ $message }}</p> @enderror
            <div class="diario-actions">
              <button class="button button--secondary" data-previous="1" type="button">Volver</button>
              <button class="button" data-next="3" type="button">Siguiente</button>
            </div>
          </article>
        </section>

        <section class="content diario-step" id="step3" hidden>
          <article class="card">
            <h2 class="card__title">3) Emoción</h2>
            <p class="card__description">¿Qué emoción apareció?</p>
            <select class="form__input" id="emocion" name="emocion" required>
              <option value="">Selecciona emoción</option>
              @foreach (['Ansiedad', 'Tristeza', 'Enojo', 'Miedo', 'Vergüenza', 'Culpa', 'Alegría'] as $opcion)
                <option value="{{ $opcion }}" @selected(old('emocion') === $opcion)>{{ $opcion }}</option>
              @endforeach
            </select>
            @error('emocion') <p class="form__error">{{ $message }}</p> @enderror

            <label class="form__label" for="intensidad">Intensidad: <strong id="intensidadValor">{{ old('intensidad', 5) }}</strong> /10</label>
            <input class="diario-range" type="range" id="intensidad" name="intensidad" min="1" max="10" value="{{ old('intensidad', 5) }}" />
            @error('intensidad') <p class="form__error">{{ $message }}</p> @enderror
            <div class="diario-actions">
              <button class="button button--secondary" data-previous="2" type="button">Volver</button>
              <button class="button" data-next="4" type="button">Siguiente</button>
            </div>
          </article>
        </section>

        <section class="content diario-step" id="step4" hidden>
          <article class="card">
            <h2 class="card__title">4) Conducta o reacción</h2>
            <p class="card__description">¿Qué hiciste después?</p>
            <textarea class="form__input" id="conducta" name="conducta" rows="4" placeholder="Ej: me aislé, lloré...">{{ old('conducta') }}</textarea>
            @error('conducta') <p class="form__error">{{ $message }}</p> @enderror
            <div class="diario-actions">
              <button class="button button--secondary" data-previous="3" type="button">Volver</button>
              <button class="button" data-next="5" type="button">Siguiente</button>
            </div>
          </article>
        </section>

        <section class="content diario-step" id="step5" hidden>
          <article class="card">
            <h2 class="card__title">5) ¿Cómo estás interpretando esto?</h2>
            <p class="card__description">Selecciona las opciones que más se parezcan a lo que pensaste o sentiste.</p>
            <input type="hidden" id="interpretacion" name="interpretacion" value="{{ old('interpretacion') }}">

            <div class="chips">
              <button class="chip" data-label="Catastrofización" type="button">Catastrofización <small>Pensar que todo saldrá muy mal</small></button>
              <button class="chip" data-label="Lectura de mente" type="button">Lectura de mente <small>Asumir lo que otros piensan</small></button>
              <button class="chip" data-label="Generalización" type="button">Generalización <small>Creer que siempre pasa lo mismo</small></button>
              <button class="chip" data-label="Blanco / negro" type="button">Blanco / negro <small>Ver todo como éxito o fracaso</small></button>
              <button class="chip" data-label="Personalización" type="button">Personalización <small>Culparte automáticamente</small></button>
            </div>
            @error('interpretacion') <p class="form__error">{{ $message }}</p> @enderror
            <div class="diario-actions">
              <button class="button button--secondary" data-previous="4" type="button">Volver</button>
              <button class="button" data-next="6" type="button">Siguiente</button>
            </div>
          </article>
        </section>

        <section class="content diario-step" id="step6" hidden>
          <article class="card">
            <h2 class="card__title">6) Reestructuración cognitiva</h2>
            <p class="card__description">Escribe una interpretación más equilibrada.</p>
            <textarea class="form__input" id="reestructuracion" name="reestructuracion" rows="5" placeholder="Ej: tal vez no fue personal...">{{ old('reestructuracion') }}</textarea>
            @error('reestructuracion') <p class="form__error">{{ $message }}</p> @enderror
            <div class="diario-actions">
              <button class="button button--secondary" data-previous="5" type="button">Volver</button>
              <button class="button" type="submit">Guardar emoción</button>
            </div>
          </article>
        </section>
      </form>
    </section>

    <section class="content historial-panel">
      <div class="diario-heading">
        <div>
          <h2 class="historial-title">Historial emocional</h2>
          <p class="card__description">Registros ordenados del más reciente al más antiguo.</p>
        </div>
      </div>

      <div class="historial-list">
      @forelse ($emociones as $emocion)
        @php
          $intensity = is_null($emocion->intensidad) ? null : (int) $emocion->intensidad;
          $intensityClass = 'intensity-empty';
          $intensityLabel = 'Sin información';
          $intensityText = 'Sin información';

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

          $situacion = $emocion->situacion ?: 'Sin información';
        @endphp
        <article class="historial-card">
          <div class="historial-card__header">
            <div>
              <div class="historial-card__badges">
                <span class="emotion-pill">
                  <span class="emotion-pill__dot" aria-hidden="true"></span>
                  {{ $emocion->emocion ?: 'Sin información' }}
                </span>
                <span class="intensity-badge {{ $intensityClass }}">{{ $intensityText }}</span>
              </div>
              <p class="historial-card__date">{{ $emocion->created_at->format('d/m/Y') }} · {{ $emocion->created_at->format('H:i') }}</p>
            </div>
          </div>

          <div class="historial-summary">
            <strong>Situación:</strong>
            <p>{{ \Illuminate\Support\Str::limit($situacion, 150) }}</p>
          </div>

          <button
            class="details-toggle"
            type="button"
            aria-expanded="false"
            aria-controls="emocion-detalle-{{ $emocion->id }}"
            data-target="emocion-detalle-{{ $emocion->id }}"
          >
            Ver más detalles
          </button>

          <div class="historial-details" id="emocion-detalle-{{ $emocion->id }}" hidden>
            <div class="historial-grid">
              <div><strong>Pensamiento automático</strong><p>{{ $emocion->pensamiento ?: 'Sin información' }}</p></div>
              <div><strong>Conducta</strong><p>{{ $emocion->conducta ?: 'Sin información' }}</p></div>
              <div><strong>Interpretación</strong><p>{{ $emocion->interpretacion ?: 'Sin información' }}</p></div>
              <div class="historial-grid__wide"><strong>Reestructuración</strong><p>{{ $emocion->reestructuracion ?: 'Sin información' }}</p></div>
            </div>

            <div class="seguimientos">
              <div class="seguimientos__header">
                <h4>Seguimientos anteriores</h4>
              </div>

              @forelse ($emocion->seguimientos as $seguimiento)
                <div class="seguimiento-note">
                  <p>{{ $seguimiento->nota ?: 'Sin información' }}</p>
                  <span>{{ $seguimiento->created_at->format('d/m/Y') }} · {{ $seguimiento->created_at->format('H:i') }}</span>
                </div>
              @empty
                <p class="empty-text">Sin información</p>
              @endforelse

              <form class="seguimiento-form" id="seguimiento-{{ $emocion->id }}" method="POST" action="/diario/{{ $emocion->id }}/seguimiento">
                @csrf
                <label class="form__label" for="nota-{{ $emocion->id }}">Nueva nota de seguimiento</label>
                <textarea class="form__input" id="nota-{{ $emocion->id }}" name="nota" rows="3" maxlength="2000" required></textarea>
                <button class="button" type="submit">Guardar seguimiento</button>
              </form>
            </div>
          </div>
        </article>
      @empty
        <div class="empty-state">
          <h3>Aún no hay registros</h3>
          <p>Cuando guardes una emoción, aparecerá aquí con sus seguimientos.</p>
        </div>
      @endforelse
      </div>
    </section>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const steps = Array.from(document.querySelectorAll('.diario-step'));
  const progressSteps = Array.from(document.querySelectorAll('.progress__step'));
  const range = document.getElementById('intensidad');
  const rangeValue = document.getElementById('intensidadValor');
  const interpretacion = document.getElementById('interpretacion');

  function showStep(number) {
    steps.forEach((step, index) => {
      step.hidden = index + 1 !== number;
    });

    progressSteps.forEach((step, index) => {
      step.classList.toggle('is-active', index + 1 === number);
      step.classList.toggle('is-done', index + 1 < number);
    });

    document.getElementById(`step${number}`).scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  document.querySelectorAll('[data-next]').forEach((button) => {
    button.addEventListener('click', () => {
      const nextStep = Number(button.dataset.next);

      if (nextStep === 4 && !document.getElementById('emocion').value) {
        document.getElementById('emocion').reportValidity();
        return;
      }

      showStep(nextStep);
    });
  });

  document.querySelectorAll('[data-previous]').forEach((button) => {
    button.addEventListener('click', () => {
      showStep(Number(button.dataset.previous));
    });
  });

  if (range && rangeValue) {
    range.addEventListener('input', () => {
      rangeValue.textContent = range.value;
    });
  }

  document.querySelectorAll('.chip[data-label]').forEach((chip) => {
    chip.addEventListener('click', () => {
      chip.classList.toggle('is-selected');
      const selected = Array.from(document.querySelectorAll('.chip[data-label].is-selected'))
        .map((item) => item.dataset.label);
      interpretacion.value = selected.join(', ');
    });
  });

  document.querySelectorAll('.details-toggle').forEach((button) => {
    button.addEventListener('click', () => {
      const details = document.getElementById(button.dataset.target);
      const isOpening = details.hidden;

      details.hidden = !isOpening;
      button.setAttribute('aria-expanded', String(isOpening));
      button.textContent = isOpening ? 'Ocultar detalles' : 'Ver más detalles';

      if (isOpening) {
        details.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    });
  });
});
</script>

@include('partials.marker-widget')
</body>
</html>
