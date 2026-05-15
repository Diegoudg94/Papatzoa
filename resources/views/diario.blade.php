<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Papatzoa - Diario de emociones</title>

  <!-- CSS: Mantengo la sintaxis de Blade por si estás en Laravel, 
       si no, cámbialo a href="css/terapeuta.css" -->
  <link rel="stylesheet" href="{{ asset('css/terapeuta.css') }}">

  <style>
    /* Estilos mínimos para asegurar que los chips y estados se vean */
    .is-active { font-weight: bold; color: #000; border-bottom: 2px solid #000; }
    .is-selected { background-color: #000 !important; color: #fff !important; }
    .hidden { display: none; }
    .chip { cursor: pointer; padding: 8px 16px; border: 1px solid #ccc; border-radius: 20px; background: #fff; }
    .sr-input { position: absolute; opacity: 0; pointer-events: none; }
  </style>
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
      <a class="header__button" href="#">Mi cuenta</a>
    </nav>
  </div>
</header>

<main class="main">
  <div class="main__container">

    <!-- PROGRESO -->
    <section class="content">
      <div class="progress">
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
    </section>

    <section class="content">
      <h1 class="title">Diario de emociones</h1>
      <p class="card__description">Registra una situación, pensamientos, emociones y reacciones.</p>
    </section>

    <!-- PASO 1 -->
    <section class="content" id="step1">
      <article class="card">
        <h2 class="card__title">1) Situación o antecedente</h2>
        <div id="antecedenteEdit">
          <p class="card__description">Describe brevemente qué ocurrió.</p>
          <textarea class="form__input" id="antecedente" rows="4" placeholder="Ej: discutí con alguien..."></textarea>
          <button class="button" id="btnGuardarAntecedente" type="button">Continuar</button>
        </div>
        <div id="antecedenteView" hidden>
          <p class="card__description"><strong>Situación guardada ✅</strong> <span id="antecedenteTimestamp"></span></p>
          <div class="note"><div class="note__text" id="antecedenteTextoView"></div></div>
        </div>
      </article>
    </section>

    <!-- PASO 2 -->
    <section class="content" id="step2" hidden>
      <article class="card">
        <h2 class="card__title">2) Pensamiento automático</h2>
        <div id="pensamientoEdit">
          <p class="card__description">¿Qué pasó por tu mente?</p>
          <textarea class="form__input" id="pensamiento" rows="4" placeholder="Ej: 'Voy a fracasar'"></textarea>
          <div class="choice">
            <div class="choice__title">¿Es un hecho o una interpretación?</div>
            <input class="sr-input" type="radio" name="tipoPensamiento" id="tipoHecho" value="Hecho">
            <input class="sr-input" type="radio" name="tipoPensamiento" id="tipoOpinion" value="Interpretación">
            <div class="chips">
              <button class="chip" type="button" data-radio="tipoHecho">Hecho</button>
              <button class="chip" type="button" data-radio="tipoOpinion">Interpretación</button>
            </div>
          </div>
          <button class="button" id="btnGuardarPensamiento" type="button">Continuar</button>
        </div>
        <div id="pensamientoView" hidden>
          <p class="card__description"><strong>Pensamiento guardado ✅</strong> <span id="pensamientoTimestamp"></span></p>
          <div class="note"><div class="note__text" id="pensamientoTextoView"></div></div>
          <div class="note"><div class="note__text"><strong>Clasificación:</strong> <span id="pensamientoTipoView"></span></div></div>
        </div>
      </article>
    </section>

    <!-- PASO 3 -->
    <section class="content" id="step3" hidden>
      <article class="card">
        <h2 class="card__title">3) Emoción</h2>
        <div id="emocionEdit">
          <p class="card__description">¿Qué emoción apareció?</p>
          <select class="form__input" id="emocion">
            <option value="">Selecciona emoción</option>
            <option value="Ansiedad">Ansiedad</option>
            <option value="Tristeza">Tristeza</option>
            <option value="Enojo">Enojo</option>
            <option value="Miedo">Miedo</option>
            <option value="Vergüenza">Vergüenza</option>
            <option value="Culpa">Culpa</option>
            <option value="Alegría">Alegría</option>
          </select>
          <label class="form__label">Intensidad: <strong id="intensidadValor">5</strong> /10</label>
          <input type="range" id="intensidad" min="0" max="10" value="5" />
          <button class="button" id="btnGuardarEmocion" type="button">Continuar</button>
        </div>
        <div id="emocionView" hidden>
          <p class="card__description"><strong>Emoción guardada ✅</strong></p>
          <div class="note"><div class="note__text" id="resumenEmocion"></div></div>
        </div>
      </article>
    </section>

    <!-- PASO 4 -->
    <section class="content" id="step4" hidden>
      <article class="card">
        <h2 class="card__title">4) Conducta o reacción</h2>
        <div id="conductaEdit">
          <p class="card__description">¿Qué hiciste después?</p>
          <textarea class="form__input" id="conducta" rows="4" placeholder="Ej: me aislé, lloré..."></textarea>
          <button class="button" id="btnGuardarConducta" type="button">Continuar</button>
        </div>
        <div id="conductaView" hidden>
          <p class="card__description"><strong>Reacción guardada ✅</strong></p>
          <div class="note"><div class="note__text" id="conductaTextoView"></div></div>
        </div>
      </article>
    </section>

    <!-- PASO 5 -->
    <section class="content" id="step5" hidden>
      <article class="card">
        <h2 class="card__title">5) ¿Cómo estás interpretando esto?</h2>
        <div id="distorsionesEdit">
        <p class="card__description">
  A veces nuestra mente interpreta las situaciones de forma extrema o poco objetiva.

  Selecciona las opciones que más se parezcan a lo que pensaste o sentiste.
</p>
          <input class="sr-input" type="checkbox" id="d_catastro">
          <input class="sr-input" type="checkbox" id="d_mente">
          <input class="sr-input" type="checkbox" id="d_general">
          <input class="sr-input" type="checkbox" id="d_bn">
          <input class="sr-input" type="checkbox" id="d_personal">
          <div class="chips">

  <button
    class="chip"
    data-check="d_catastro"
    type="button"
  >
    Catastrofización
    <br>
    <small>
      Pensar que todo saldrá muy mal
    </small>
  </button>


  <button
    class="chip"
    data-check="d_mente"
    type="button"
  >
    Lectura de mente
    <br>
    <small>
      Asumir lo que otros piensan
    </small>
  </button>


  <button
    class="chip"
    data-check="d_general"
    type="button"
  >
    Generalización
    <br>
    <small>
      Creer que siempre pasa lo mismo
    </small>
  </button>


  <button
    class="chip"
    data-check="d_bn"
    type="button"
  >
    Blanco / negro
    <br>
    <small>
      Ver todo como éxito o fracaso
    </small>
  </button>


  <button
    class="chip"
    data-check="d_personal"
    type="button"
  >
    Personalización
    <br>
    <small>
      Culparte automáticamente
    </small>
  </button>

</div>
          <button class="button" id="btnGuardarDistorsiones" type="button">Continuar</button>
        </div>
        <div id="distorsionesView" hidden>
            <p class="card__description"><strong>Interpretaciones detectadas ✅</strong></p>
            <div class="note"><div id="distorsionesSeleccionadas"></div></div>
        </div>
      </article>
    </section>

    <!-- PASO 6 -->
    <section class="content" id="step6" hidden>
      <article class="card">
        <h2 class="card__title">6) Reestructuración cognitiva</h2>
        <div id="reestructuracionEdit">
          <p class="card__description">Escribe una interpretación más equilibrada.</p>
          <textarea class="form__input" id="reestructuracion" rows="5" placeholder="Ej: Tal vez no fue personal..."></textarea>
          <button class="button" id="btnGuardarReestructuracion" type="button">Finalizar registro</button>
        </div>
        <div id="reestructuracionView" hidden>
          <p class="card__description"><strong>Nueva interpretación guardada ✅</strong></p>
          <div class="note"><div class="note__text" id="reestructuracionTextoView"></div></div>
        </div>
      </article>
    </section>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {

  // --- DECLARACIÓN DE VARIABLES (Indispensable) ---
  const select = (id) => document.getElementById(id);

  // Pasos y progreso
  const steps = [null, select('step1'), select('step2'), select('step3'), select('step4'), select('step5'), select('step6')];
  const progressSteps = [null, document.querySelector('[data-step="1"]'), document.querySelector('[data-step="2"]'), document.querySelector('[data-step="3"]'), document.querySelector('[data-step="4"]'), document.querySelector('[data-step="5"]'), document.querySelector('[data-step="6"]')];

  // Elementos Paso 1
  const btnP1 = select('btnGuardarAntecedente');
  const antInput = select('antecedente');

  // Elementos Paso 2
  const btnP2 = select('btnGuardarPensamiento');
  const penInput = select('pensamiento');

  // Elementos Paso 3
  const btnP3 = select('btnGuardarEmocion');
  const emoSelect = select('emocion');
  const intInput = select('intensidad');
  const intValor = select('intensidadValor');

  // Elementos Paso 4
  const btnP4 = select('btnGuardarConducta');
  const conInput = select('conducta');

  // Elementos Paso 5
  const btnP5 = select('btnGuardarDistorsiones');

  // Elementos Paso 6
  const btnP6 = select('btnGuardarReestructuracion');
  const reestInput = select('reestructuracion');

  // Función genérica para cambiar de paso
  function goToStep(current, next) {
    progressSteps[current].classList.remove('is-active');
    progressSteps[next].classList.add('is-active');
    steps[next].hidden = false;
    steps[next].scrollIntoView({ behavior: 'smooth' });
  }

  /* LÓGICA PASO 1 */
  btnP1.onclick = () => {
    if (!antInput.value.trim()) return alert('Describe la situación.');
    select('antecedenteTextoView').textContent = antInput.value;
    select('antecedenteTimestamp').textContent = new Date().toLocaleString();
    select('antecedenteEdit').hidden = true;
    select('antecedenteView').hidden = false;
    goToStep(1, 2);
  };

  /* LÓGICA CHIPS RADIO (PASO 2) */
  document.querySelectorAll('[data-radio]').forEach(btn => {
    btn.onclick = () => {
      const input = select(btn.dataset.radio);
      input.checked = true;
      btn.parentElement.querySelectorAll('.chip').forEach(b => b.classList.remove('is-selected'));
      btn.classList.add('is-selected');
    };
  });

  /* LÓGICA PASO 2 */
  btnP2.onclick = () => {
    const tipo = document.querySelector('input[name="tipoPensamiento"]:checked');
    if (!penInput.value.trim() || !tipo) return alert('Completa el pensamiento y su tipo.');
    select('pensamientoTextoView').textContent = penInput.value;
    select('pensamientoTipoView').textContent = tipo.value;
    select('pensamientoTimestamp').textContent = new Date().toLocaleString();
    select('pensamientoEdit').hidden = true;
    select('pensamientoView').hidden = false;
    goToStep(2, 3);
  };

  /* LÓGICA PASO 3 */
  intInput.oninput = () => { intValor.textContent = intInput.value; };
  btnP3.onclick = () => {
    if (!emoSelect.value) return alert('Selecciona una emoción.');
    select('resumenEmocion').textContent = `Emoción: ${emoSelect.value} (${intInput.value}/10)`;
    select('emocionEdit').hidden = true;
    select('emocionView').hidden = false;
    goToStep(3, 4);
  };

  /* LÓGICA PASO 4 */
  btnP4.onclick = () => {
    if (!conInput.value.trim()) return alert('Describe tu reacción.');
    select('conductaTextoView').textContent = conInput.value;
    select('conductaEdit').hidden = true;
    select('conductaView').hidden = false;
    goToStep(4, 5);
  };

  /* LÓGICA CHIPS CHECKBOX (PASO 5) */
  document.querySelectorAll('[data-check]').forEach(btn => {
    btn.onclick = () => {
      const input = select(btn.dataset.check);
      input.checked = !input.checked;
      btn.classList.toggle('is-selected', input.checked);
    };
  });

  /* LÓGICA PASO 5 */
  btnP5.onclick = () => {
    const dists = {
      'd_catastro': 'Catastrofización',
      'd_mente': 'Lectura de mente',
      'd_general': 'Generalización',
      'd_bn': 'Blanco / negro',
      'd_personal': 'Personalización'
    };
    const seleccionadas = Object.keys(dists).filter(id => select(id).checked).map(id => dists[id]);

    if (seleccionadas.length === 0) return alert('Selecciona al menos una interpretación.');

    select('distorsionesSeleccionadas').innerHTML = seleccionadas.map(item => `• ${item}`).join('<br>');
    select('distorsionesEdit').hidden = true;
    select('distorsionesView').hidden = false;
    goToStep(5, 6);
  };

  /* LÓGICA PASO 6 */
  btnP6.onclick = () => {
    if (!reestInput.value.trim()) return alert('Escribe una reinterpretación.');
    select('reestructuracionTextoView').textContent = reestInput.value;
    select('reestructuracionEdit').hidden = true;
    select('reestructuracionView').hidden = false;
    alert('Registro emocional finalizado ✅');
  };
});
</script>

</body>
</html>