<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registro | Papatzoa</title>
 <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>
  <main class="page">
    <section class="card">
      <h1>Registro en Papatzoa</h1>
      <p style="margin-top: 6px; color:#6b7280; font-size: 0.95rem;">
        Crea tu cuenta para comenzar.
      </p>

  <form action="/registro" method="POST" autocomplete="on">

  @csrf

  <!-- Mensaje éxito -->
  @if(session('success'))

  <div
    id="successMessage"
    style="
      background:#dcfce7;
      color:#166534;
      padding:16px;
      border-radius:10px;
      margin-bottom:16px;
      border:1px solid #86efac;
      line-height:1.6;
    "
  >

    <strong>
      Usuario registrado exitosamente.
    </strong>

    <br>

    Serás redirigido a la página de inicio de sesión en

    <span id="countdown">
      5
    </span>

    segundos...

  </div>

@endif

  <!-- Errores -->
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

  <fieldset class="form__fieldset">

    <legend class="form__legend">
      Datos del usuario
    </legend>

  <!-- Nombre -->
<div class="form__group">

  <label class="form__label" for="nombre">
    Nombre
  </label>

  <input
    class="form__input"
    type="text"
    id="nombre"
    name="nombre"
    placeholder="Tu nombre"
    required
  />

</div>


<!-- Apellido -->
<div class="form__group">

  <label class="form__label" for="apellido">
    Apellido
  </label>

  <input
    class="form__input"
    type="text"
    id="apellido"
    name="apellido"
    placeholder="Tus apellidos"
    required
  />

</div>


<!-- Sexo -->
<div class="form__group">

  <label class="form__label" for="sexo">
    Sexo
  </label>

  <select
    class="form__input"
    id="sexo"
    name="sexo"
    required
  >
    <option value="">
      Selecciona una opción
    </option>

    <option value="masculino">
      Masculino
    </option>

    <option value="femenino">
      Femenino
    </option>

    <option value="otro">
      Otro
    </option>

    <option value="prefiero_no_decir">
      Prefiero no decirlo
    </option>

  </select>

</div>


<!-- Edad -->
<div class="form__group">

  <label class="form__label" for="edad">
    Edad
  </label>

  <input
    class="form__input"
    type="number"
    id="edad"
    name="edad"
    min="10"
    max="120"
    placeholder="Tu edad"
    required
  />

</div>




    <!-- Correo -->
    <div class="form__group">
      <label class="form__label" for="correo">
        Correo
      </label>

      <input
        class="form__input"
        type="email"
        id="correo"
        name="correo"
        placeholder="tucorreo@ejemplo.com"
        required
      />
    </div>

    <!-- Password -->
    <div class="form__group">

      <label class="form__label" for="password">
        Crea una contraseña
      </label>

      <input
        class="form__input"
        type="password"
        id="password"
        name="password"
        placeholder="********"
        required
        minlength="8"

        pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$"

        title="La contraseña debe tener mínimo 8 caracteres, una mayúscula, un número y un carácter especial."
      />

      <small style="color:#6b7280;">
        Debe incluir:
        8 caracteres,
        una mayúscula,
        un número
        y un símbolo especial.
      </small>

    </div>

    <!-- Confirmar password -->
    <div class="form__group">

      <label class="form__label" for="password_confirmation">
        Confirmar contraseña
      </label>

      <input
        class="form__input"
        type="password"
        id="password_confirmation"
        name="password_confirmation"
        placeholder="Repite tu contraseña"
        required
      />

    </div>

    <!-- Checkbox terapeuta -->
    <div class="form__row">

      <label class="form__checkbox" for="terapeuta">

        <input
          class="form__checkbox-input"
          type="checkbox"
          id="terapeuta"
          name="terapeuta"
          value="1"
        />

        ¿Eres terapeuta?

      </label>

    </div>

    <!-- Botón -->
    <button class="button" type="submit">
      Registrarme
    </button>

  </fieldset>

  <div class="form__footer">
    ¿Ya tienes cuenta?
    <a class="form__link" href="/login">
      Inicia sesión
    </a>
  </div>

</form>
    </section>
  </main>
  <script>

  // =========================
  // REDIRECCIÓN AUTOMÁTICA
  // =========================

  const countdownElement =
    document.getElementById('countdown');

  // Solo ejecutar si existe el mensaje
  if (countdownElement)
  {

    let segundos = 5;

    const intervalo = setInterval(() =>
    {

      segundos--;

      countdownElement.textContent = segundos;

      // Cuando llegue a 0
      if (segundos <= 0)
      {

        clearInterval(intervalo);

        // Redirigir al login
        window.location.href = '/login';

      }

    }, 1000);

  }

</script>
</body>
</html>
