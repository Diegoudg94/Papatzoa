<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Completar registro | Papatzoa</title>
  <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>
  <main class="page">
    <section class="card">
      <h1>Completa tu registro</h1>
      <p style="margin-top: 6px; color:#6b7280; font-size: 0.95rem;">
        Detectamos estos datos desde tu cuenta de Google.
      </p>

      @if (($googleUser['avatar_url'] ?? null))
        <div style="display:flex; justify-content:center; margin:18px 0;">
          <img
            src="{{ $googleUser['avatar_url'] }}"
            alt="Avatar de Google"
            style="width:86px; height:86px; border-radius:50%; object-fit:cover; border:1px solid #e5e7eb;"
          >
        </div>
      @endif

      @if ($errors->any())
        <div
          style="
            background:#fee2e2;
            color:#991b1b;
            padding:12px;
            border-radius:8px;
            margin-bottom:16px;
            border:1px solid #fca5a5;
          "
        >
          <ul style="padding-left:18px;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="/completar-registro-google" method="POST" autocomplete="on">
        @csrf

        <fieldset class="form__fieldset">
          <legend class="form__legend">Datos de Google</legend>

          <div class="form__group">
            <label class="form__label" for="nombre">Nombre</label>
            <input
              class="form__input"
              type="text"
              id="nombre"
              name="nombre"
              value="{{ old('nombre', $googleUser['nombre']) }}"
              required
            />
          </div>

          <div class="form__group">
            <label class="form__label" for="correo">Correo</label>
            <input
              class="form__input"
              type="email"
              id="correo"
              value="{{ $googleUser['correo'] }}"
              readonly
            />
          </div>
        </fieldset>

        <fieldset class="form__fieldset">
          <legend class="form__legend">Datos faltantes</legend>

          <div class="form__group">
            <label class="form__label" for="edad">Edad</label>
            <input
              class="form__input"
              type="number"
              id="edad"
              name="edad"
              min="10"
              max="120"
              value="{{ old('edad') }}"
              required
            />
          </div>

          <div class="form__group">
            <label class="form__label" for="sexo">Sexo</label>
            <select class="form__input" id="sexo" name="sexo" required>
              <option value="">Selecciona una opción</option>
              <option value="masculino" @selected(old('sexo') === 'masculino')>Masculino</option>
              <option value="femenino" @selected(old('sexo') === 'femenino')>Femenino</option>
              <option value="otro" @selected(old('sexo') === 'otro')>Otro</option>
              <option value="prefiero_no_decir" @selected(old('sexo') === 'prefiero_no_decir')>Prefiero no decirlo</option>
            </select>
          </div>

          <div class="form__group">
            <span class="form__label">¿Eres terapeuta?</span>

            <label class="form__checkbox" for="terapeuta_no">
              <input
                class="form__checkbox-input"
                type="radio"
                id="terapeuta_no"
                name="terapeuta"
                value="0"
                @checked(old('terapeuta', '0') === '0')
                required
              />
              No
            </label>

            <label class="form__checkbox" for="terapeuta_si">
              <input
                class="form__checkbox-input"
                type="radio"
                id="terapeuta_si"
                name="terapeuta"
                value="1"
                @checked(old('terapeuta') === '1')
              />
              Sí
            </label>

            <p id="avisoTerapeuta" style="display:none; color:#92400e; background:#fef3c7; border:1px solid #fcd34d; padding:12px; border-radius:8px; line-height:1.5;">
              Tu cuenta quedará pendiente de verificación profesional. Más adelante se solicitarán documentos para validar tu perfil como terapeuta.
            </p>
          </div>

          <button class="button" type="submit">
            Finalizar registro
          </button>
        </fieldset>
      </form>
    </section>
  </main>

  <script>
    const terapeutaSi = document.getElementById('terapeuta_si');
    const terapeutaNo = document.getElementById('terapeuta_no');
    const avisoTerapeuta = document.getElementById('avisoTerapeuta');

    function toggleAvisoTerapeuta() {
      avisoTerapeuta.style.display = terapeutaSi.checked ? 'block' : 'none';
    }

    terapeutaSi.addEventListener('change', toggleAvisoTerapeuta);
    terapeutaNo.addEventListener('change', toggleAvisoTerapeuta);
    toggleAvisoTerapeuta();
  </script>
</body>
</html>
