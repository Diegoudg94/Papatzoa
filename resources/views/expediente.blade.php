<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Expediente del paciente</title>
  <link rel="stylesheet" href="{{ asset('css/terapeuta.css') }}" />
</head>
<body>

  <header class="header">
    <div class="header__container">
      <a class="brand" href="terapeuta.html">
        <span class="brand__icon" aria-hidden="true">✳︎</span>
        <span class="brand__title">Expediente</span>
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

      <!-- Paciente -->
      <section class="content">
        <h1 class="title">
          Paciente:
          <span class="patient-name">
    {{ $paciente->nombre }} {{ $paciente->apellido }}
</span>
        </h1>

        <p class="card__description">
          Expediente clínico (vista de ejemplo). La información cambiará según el paciente.
        </p>
      </section>

      <!-- Tabs -->
      <section class="content">
        <div class="tabs" role="tablist" aria-label="Secciones del expediente">
          <button class="tab is-active" type="button" data-tab="emociones">Emociones</button>
          <button class="tab" type="button" data-tab="sesiones">Sesiones</button>
          <button class="tab" type="button" data-tab="notas">Notas</button>
        </div>
      </section>

      <!-- Panel: Emociones -->
      <section class="content tab-panel" id="tab-emociones">
        <h2 class="title">Historial de emociones</h2>

        <article class="card">
          <div class="table-wrap">
            <table class="table" aria-label="Historial de emociones">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Emoción</th>
                  <th>Descripción</th>
                </tr>
              </thead>
              <tbody id="emocionesBody">
                <!-- filas por JS -->
              </tbody>
            </table>
          </div>
        </article>
      </section>

      <!-- Panel: Sesiones -->
      <section class="content tab-panel" id="tab-sesiones" hidden>
        <h2 class="title">Historial de sesiones</h2>

        <article class="card">
          <div class="table-wrap">
            <table class="table" aria-label="Historial de sesiones">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Motivo</th>
                  <th>De qué se habló</th>
                  <th>Conclusión</th>
                </tr>
              </thead>
              <tbody id="sesionesBody">
                <!-- filas por JS -->
              </tbody>
            </table>
          </div>
        </article>
      </section>

      <!-- Panel: Notas -->
      <section class="content tab-panel" id="tab-notas" hidden>
        <h2 class="title">Notas del terapeuta</h2>

        <article class="card">
          <p class="card__description" style="margin-bottom:12px;">
            Escribe una nota para el expediente (se guardará con fecha y hora).
          </p>

          <form class="form" id="notaForm" novalidate>
            <div class="form__group">
              <label class="form__label" for="notaTexto">Nueva nota</label>
              <textarea
                class="form__input"
                id="notaTexto"
                rows="4"
                placeholder="Ej: El paciente muestra avances en manejo de ansiedad..."
                required
              ></textarea>
            </div>

            <button class="button" type="submit">Guardar nota</button>
          </form>

          <div class="notes" id="notesContainer" style="margin-top:16px;">
            <h3 class="card__title">Notas registradas</h3>
            <!-- notas por JS -->
          </div>
        </article>
      </section>

    </div>
  </main>

  <script>
    /* ===== 1) Paciente dinámico ===== */
    

    /* ===== 2) Data mock (sin backend) ===== */
    const MOCK = {
      "María López": {
        emociones: [
          { fecha: "10 dic 2025", hora: "21:10", emocion: "Ansiedad", desc: "Siento presión en el pecho y me cuesta dormir." },
          { fecha: "12 dic 2025", hora: "08:05", emocion: "Tristeza", desc: "Me levanté sin ganas, pensé en muchas cosas negativas." },
          { fecha: "13 dic 2025", hora: "19:40", emocion: "Calma", desc: "Caminé 30 minutos y me ayudó a despejar la mente." },
        ],
        sesiones: [
          { fecha: "05 dic 2025", hora: "17:00", motivo: "Ansiedad laboral", hablo: "Disparadores en el trabajo y pensamientos recurrentes.", conclusion: "Técnicas de respiración y límites laborales." },
          { fecha: "09 dic 2025", hora: "17:00", motivo: "Sueño y hábitos", hablo: "Rutina nocturna y uso de pantallas.", conclusion: "Higiene del sueño y diario emocional." },
        ],
        notasIniciales: [
          { meta: "13 dic 2025 — 18:20", texto: "Se sugiere continuar con respiración 4-7-8 durante la semana." },
        ],
      },

      "Carlos Hernández": {
        emociones: [
          { fecha: "09 dic 2025", hora: "22:05", emocion: "Estrés", desc: "Me siento abrumado por cambios y responsabilidades." },
          { fecha: "11 dic 2025", hora: "07:55", emocion: "Cansancio", desc: "Dormí poco, tuve pensamientos rumiantes." },
          { fecha: "13 dic 2025", hora: "20:15", emocion: "Esperanza", desc: "Hablé con un amigo y me ayudó a ver opciones." },
        ],
        sesiones: [
          { fecha: "02 dic 2025", hora: "16:00", motivo: "Motivación", hablo: "Objetivos personales y frustración por estancamiento.", conclusion: "Plan semanal de hábitos + metas pequeñas." },
          { fecha: "07 dic 2025", hora: "16:00", motivo: "Estrés", hablo: "Detonantes y reestructuración cognitiva.", conclusion: "Registro de pensamientos + pausa activa diaria." },
        ],
        notasIniciales: [
          { meta: "07 dic 2025 — 17:15", texto: "Se acordó rutina breve de ejercicio y registro de pensamientos." },
        ],
      },

      "Ana Martínez": {
        emociones: [
          { fecha: "08 dic 2025", hora: "19:10", emocion: "Pánico", desc: "Sentí taquicardia en un lugar concurrido." },
          { fecha: "10 dic 2025", hora: "12:30", emocion: "Ansiedad", desc: "Me preocupa salir sola a la calle." },
        ],
        sesiones: [
          { fecha: "01 dic 2025", hora: "12:00", motivo: "Ansiedad social", hablo: "Situaciones evitadas y síntomas físicos.", conclusion: "Exposición gradual + respiración diafragmática." },
        ],
        notasIniciales: [
          { meta: "01 dic 2025 — 13:10", texto: "Se explicó exposición gradual y señales de seguridad." },
        ],
      },
    };

    const data = MOCK["{{ $paciente->nombre }} {{ $paciente->apellido }}"]

    /* ===== 3) Render ===== */
    const emocionesBody = document.getElementById('emocionesBody');
    const sesionesBody = document.getElementById('sesionesBody');
    const notesContainer = document.getElementById('notesContainer');

    function renderEmociones(items) {
      emocionesBody.innerHTML = items.length
        ? items.map(i => `
            <tr>
              <td data-label="Fecha">${i.fecha}</td>
              <td data-label="Hora">${i.hora}</td>
              <td data-label="Emoción">${i.emocion}</td>
              <td data-label="Descripción">${i.desc}</td>
            </tr>
          `).join('')
        : `<tr><td colspan="4">Sin registros de emociones.</td></tr>`;
    }

    function renderSesiones(items) {
      sesionesBody.innerHTML = items.length
        ? items.map(s => `
            <tr>
              <td data-label="Fecha">${s.fecha}</td>
              <td data-label="Hora">${s.hora}</td>
              <td data-label="Motivo">${s.motivo}</td>
              <td data-label="De qué se habló">${s.hablo}</td>
              <td data-label="Conclusión">${s.conclusion}</td>
            </tr>
          `).join('')
        : `<tr><td colspan="5">Sin sesiones registradas.</td></tr>`;
    }

    function renderNotasIniciales(items) {
      const h3 = notesContainer.querySelector('h3');
      notesContainer.innerHTML = '';
      notesContainer.appendChild(h3);

      if (!items.length) {
        const p = document.createElement('p');
        p.className = 'card__description';
        p.textContent = 'Aún no hay notas registradas.';
        notesContainer.appendChild(p);
        return;
      }

      items.forEach(n => {
        const div = document.createElement('div');
        div.className = 'note';
        div.innerHTML = `
          <div class="note__meta">${n.meta}</div>
          <div class="note__text"></div>
        `;
        div.querySelector('.note__text').textContent = n.texto;
        notesContainer.appendChild(div);
      });
    }

    renderEmociones(data.emociones);
    renderSesiones(data.sesiones);
    renderNotasIniciales(data.notasIniciales);

    /* ===== 4) Agregar nota (front) ===== */
    const notaForm = document.getElementById('notaForm');
    const notaTexto = document.getElementById('notaTexto');

    notaForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const texto = notaTexto.value.trim();
      if (!texto) {
        alert('Escribe una nota antes de guardar.');
        return;
      }

      const now = new Date();
      const fecha = now.toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' });
      const hora = now.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });

      const div = document.createElement('div');
      div.className = 'note';
      div.innerHTML = `
        <div class="note__meta">${fecha} — ${hora}</div>
        <div class="note__text"></div>
      `;
      div.querySelector('.note__text').textContent = texto;

      notesContainer.appendChild(div);
      notaForm.reset();
    });

    /* ===== 5) Tabs ===== */
    const tabButtons = document.querySelectorAll('.tab');
    const panels = {
      emociones: document.getElementById('tab-emociones'),
      sesiones: document.getElementById('tab-sesiones'),
      notas: document.getElementById('tab-notas'),
    };

    tabButtons.forEach((btn) => {
      btn.addEventListener('click', () => {
        const key = btn.dataset.tab;

        tabButtons.forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');

        Object.values(panels).forEach(p => p.hidden = true);
        panels[key].hidden = false;
      });
    });
  </script>

</body>
</html>