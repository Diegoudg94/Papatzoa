<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Diario de emociones</title>
  <link rel="stylesheet" href="terapeuta.css" />
</head>

<body>

<!-- ================= HEADER ================= -->
<header class="header">
  <div class="header__container">
    <a class="brand" href="dashboard.html">
      <span class="brand__icon">✳︎</span>
      <span class="brand__title">Papatzoa</span>
    </a>

    <nav class="header__center">
      <a class="header__link" href="dashboard.html">Inicio</a>
    </nav>

    <nav class="header__actions">
      <a class="header__button" href="#">Mi cuenta</a>
    </nav>
  </div>
</header>

<main class="main">
  <div class="main__container">

    <!-- ================= PROGRESO ================= -->
    <section class="content">
      <div class="progress">
        <div class="progress__step is-active" data-step="1">1. Antecedente</div>
        <div class="progress__line"></div>
        <div class="progress__step" data-step="2">2. Pensamiento</div>
        <div class="progress__line"></div>
        <div class="progress__step" data-step="3">3. Emoción</div>
      </div>
    </section>

    <!-- ================= TÍTULO ================= -->
    <section class="content">
      <h1 class="title">Diario de emociones</h1>
      <p class="card__description">
        Registra una situación, tu pensamiento y la emoción generada.
      </p>
    </section>

    <!-- =================================================
         PASO 1: ANTECEDENTE
         ================================================= -->
    <section class="content" id="step1">
      <article class="card">

        <h2 class="card__title">1) Antecedente inmediato</h2>

        <!-- MODO EDICIÓN -->
        <div id="antecedenteEdit">
          <textarea
            class="form__input"
            id="antecedente"
            rows="4"
            placeholder="¿Qué fue lo que paso?"
          ></textarea>

          <button class="button" id="btnGuardarAntecedente" type="button">
            Guardar antecedente
          </button>
        </div>

        <!-- MODO LECTURA -->
        <div id="antecedenteView" hidden>
          <p class="card__description">
            <strong>Antecedente guardado ✅</strong>
            <span id="antecedenteTimestamp"></span>
          </p>

          <div class="note">
            <div class="note__text" id="antecedenteTextoView"></div>
          </div>
        </div>

      </article>
    </section>

    <!-- =================================================
         PASO 2: PENSAMIENTO PASAR EL CUADRO DE EMOCIONES ENTRE EL PASO 1 Y 2 
         ================================================= -->
    <section class="content" id="step2" hidden>
      <article class="card">

        <h2 class="card__title">2) Pensamiento (autoverbalización)</h2>

        <!-- MODO EDICIÓN -->
        <div id="pensamientoEdit">

          <textarea
            class="form__input"
            id="pensamiento"
            rows="4"
            placeholder="¿Porque estoy sintiendo eso?"
          ></textarea>

          <!-- Hecho vs Opinión -->
          <div class="choice">
            <div class="choice__title">Hecho u Opinión</div>

            <input class="sr-input" type="radio" name="tipoPensamiento" id="tipoHecho" value="hecho">
            <input class="sr-input" type="radio" name="tipoPensamiento" id="tipoOpinion" value="opinion">

            <div class="chips">
              <button class="chip" type="button" data-radio="tipoHecho">Hecho</button>
              <button class="chip" type="button" data-radio="tipoOpinion">Opinión</button>
            </div>
          </div>

          <!-- Distorsiones --> <!-- Define el pensamiento que tiene el usuario en ese momento sea verdad o no  -->.
          <div class="choice">
            <div class="choice__title">Distorsiones cognitivas (opcional)</div>

            <input class="sr-input" type="checkbox" id="d_catastro" value="Catastrofización">
            <input class="sr-input" type="checkbox" id="d_mente" value="Lectura de mente">
            <input class="sr-input" type="checkbox" id="d_general" value="Generalización">
            <input class="sr-input" type="checkbox" id="d_bn" value="Blanco / negro">
            <input class="sr-input" type="checkbox" id="d_personal" value="Personalización">

            
            

            <div class="chips">
              <button class="chip" data-check="d_catastro">Catastrofización</button>
              <button class="chip" data-check="d_mente">Lectura de mente</button>
              <button class="chip" data-check="d_general">Generalización</button>
              <button class="chip" data-check="d_bn">Blanco / negro</button>
              <button class="chip" data-check="d_personal">Personalización</button>
            </div>
          </div>

          <button class="button" id="btnGuardarPensamiento" type="button">
            Guardar pensamiento
          </button>

        </div>


        <!-- MODO LECTURA -->
        <div id="pensamientoView" hidden>
          <p class="card__description">
            <strong>Pensamiento guardado ✅</strong>
            <span id="pensamientoTimestamp"></span>
          </p>

          <div class="note">
            <div class="note__text" id="pensamientoTextoView"></div>
          </div>

          <div class="note">
            <div class="note__text">
              <strong>Tipo:</strong> <span id="pensamientoTipoView"></span><br>
              <strong>Distorsiones:</strong> <span id="pensamientoDistorsionesView"></span>
            </div>
          </div>
        </div>

      </article>
    </section>

    <!-- =================================================
         PASO 3: EMOCIÓN agregar opcion que diga otro y un input para escribir la emocion   y un paso 4 donde el usuario diga que crees que pueda hacer para cambiar esa emocion en caso de que sea negativa de forma opcional para el usuario
         ================================================= -->
    <section class="content" id="step3" hidden>
      <article class="card">

        <h2 class="card__title">3) Emoción generada</h2>

        <select class="form__input" id="emocion">
          <option value="">Selecciona emoción</option>
          <option value="Ansiedad">Ansiedad</option>
          <option value="Tristeza">Tristeza</option>
          <option value="Enojo">Enojo</option>
          <option value="Miedo">Miedo</option>
          <option value="Calma">Calma</option>
          <option value="Alegría">Alegría</option>
        </select>

        <label class="form__label">
          Intensidad:
          <strong id="intensidadValor">5</strong>
        </label>

        <input type="range" id="intensidad" min="0" max="10" value="5" />

        <button class="button" id="btnGuardarEmocion" type="button">
          Guardar emoción
        </button>

        <p class="card__description" id="resumenEmocion"></p>

      </article>
    </section>

  </div>
</main>

<!-- ===================== JAVASCRIPT ===================== -->
<script>
document.addEventListener('DOMContentLoaded', () => {

  const format = d => d.toLocaleString('es-MX', { dateStyle:'short', timeStyle:'short' });

  const p1 = document.querySelector('[data-step="1"]');
  const p2 = document.querySelector('[data-step="2"]');
  const p3 = document.querySelector('[data-step="3"]');

  /* ===== PASO 1 ===== */
  btnGuardarAntecedente.onclick = () => {
    if (!antecedente.value.trim()) return alert('Escribe el antecedente.');

    antecedenteTextoView.textContent = antecedente.value;
    antecedenteTimestamp.textContent = ` — ${format(new Date())}`;

    antecedenteEdit.hidden = true;
    antecedenteView.hidden = false;

    step2.hidden = false;
    p1.classList.replace('is-active','is-done');
    p2.classList.add('is-active');
    step2.scrollIntoView({behavior:'smooth'});
  };

  /* ===== CHIPS ===== */
  document.querySelectorAll('[data-radio]').forEach(btn => {
    btn.onclick = () => {
      const input = document.getElementById(btn.dataset.radio);
      input.checked = true;
      btn.parentElement.querySelectorAll('.chip').forEach(b=>b.classList.remove('is-selected'));
      btn.classList.add('is-selected');
    };
  });

  document.querySelectorAll('[data-check]').forEach(btn => {
    btn.onclick = () => {
      const input = document.getElementById(btn.dataset.check);
      input.checked = !input.checked;
      btn.classList.toggle('is-selected', input.checked);
    };
  });

  /* ===== PASO 2 ===== */
  btnGuardarPensamiento.onclick = () => {
    if (!pensamiento.value.trim()) return alert('Escribe tu pensamiento.');
    const tipo = document.querySelector('input[name="tipoPensamiento"]:checked');
    if (!tipo) return alert('Selecciona Hecho u Opinión.');

    const dist = [...document.querySelectorAll('.sr-input[type="checkbox"]:checked')]
      .map(i => i.value);

    pensamientoTextoView.textContent = pensamiento.value;
    pensamientoTipoView.textContent = tipo.value;
    pensamientoDistorsionesView.textContent = dist.length ? dist.join(', ') : 'Ninguna';
    pensamientoTimestamp.textContent = ` — ${format(new Date())}`;

    pensamientoEdit.hidden = true;
    pensamientoView.hidden = false;

    step3.hidden = false;
    p2.classList.replace('is-active','is-done');
    p3.classList.add('is-active');
    step3.scrollIntoView({behavior:'smooth'});
  };

  /* ===== PASO 3 ===== */
  intensidad.oninput = () => intensidadValor.textContent = intensidad.value;

  btnGuardarEmocion.onclick = () => {
    if (!emocion.value) return alert('Selecciona una emoción.');
    resumenEmocion.textContent =
      `Emoción guardada ✅ — ${emocion.value} (${intensidad.value}/10)`;
  };

});
</script>

</body>
</html>