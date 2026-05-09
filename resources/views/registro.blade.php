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
        
        <fieldset class="form__fieldset">
          <legend class="form__legend">Datos del usuario</legend>

          <div class="form__group">
            <label class="form__label" for="nombre">Nombre</label>
            <input
              class="form__input"
              type="text"
              id="nombre"
              name="nombre"
              placeholder="Tu nombre completo"
              required
            />
          </div>

          <div class="form__group">
            <label class="form__label" for="correo">Correo</label>
            <input
              class="form__input"
              type="email"
              id="correo"
              name="correo"
              placeholder="tucorreo@ejemplo.com"
              required
            />
          </div>

          <div class="form__group">
            <label class="form__label" for="password">Crea una contraseña</label>
            <input
              class="form__input"
              type="password"
              id="password"
              name="password"
              placeholder="Mínimo 8 caracteres"
              minlength="8"
              required
            />
          </div>

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

          <button class="button" type="submit">Registrarme</button>
        </fieldset>

        <div class="form__footer">
          ¿Ya tienes cuenta?
          <a class="form__link" href="#">Inicia sesión</a>
        </div>

      </form>
    </section>
  </main>
</body>
</html>