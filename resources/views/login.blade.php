<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Iniciar sesión</title>
</head>
<body> 
   <main class="page">
  <section class="card">
    <header class="card__header">
      <h1 class="title">Iniciar sesión</h1>
      <p class="subtitle">Accede con tu correo y contraseña</p>
    </header>

    <form class="card" action="dashboard.html" method="post" autocomplete="on" novalidate>
      <fieldset class="form__fieldset">
        <legend class="form__legend">Datos de acceso</legend>

        <div class="form__group">
          <label class="form__label" for="email">Correo electrónico</label>
          <input
            class="form__input"
            type="email"
            id="email"
            name="email"
            placeholder="tu@correo.com"
            required
          />
        </div>

        <div class="form__group">
          <label class="form__label" for="password">Contraseña</label>
          <input
            class="form__input"
            type="password"
            id="password"
            name="password"
            placeholder="••••••••"
            required
            minlength="6"
          />
        </div>
      </fieldset>

      <div class="form__row">
        <label class="form__checkbox">
          <input class="form__checkbox-input" type="checkbox" name="remember">
          <span class="form__checkbox-text">Recordarme</span>
        </label>

        <a class="form__link" href="#">¿Olvidaste tu contraseña?</a>
      </div>

      <button class="button" type="submit">Entrar</button>

      <p class="form__footer">
        ¿No tienes cuenta?
        <a class="form__link" href="nuevousuario.html">Crear cuenta</a>
      </p>
    </form>
  </section>
</main>
</main>
</body>
</html>