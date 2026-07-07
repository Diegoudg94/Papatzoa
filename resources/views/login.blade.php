<!DOCTYPE html>
<html lang="es">

<head>

    <!-- ===================================== -->
    <!-- CONFIGURACIÓN BÁSICA -->
    <!-- ===================================== -->

    <!-- Codificación UTF-8 -->
    <meta charset="UTF-8">

    <!-- Responsive design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS cargado desde public/css -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <!-- Título de la pestaña -->
    <title>Iniciar sesión</title>

</head>

<body>

    <!-- ===================================== -->
    <!-- CONTENEDOR PRINCIPAL -->
    <!-- ===================================== -->

    <main class="page">

        <!-- Tarjeta principal -->
        <section class="card">

            <!-- ===================================== -->
            <!-- ENCABEZADO -->
            <!-- ===================================== -->

            <header class="card__header">

                <!-- Título -->
                <h1 class="title">
                    Iniciar sesión
                </h1>

                <!-- Subtítulo -->
                <p class="subtitle">
                    Accede con tu correo y contraseña
                </p>

            </header>


            <!-- ===================================== -->
            <!-- FORMULARIO -->
            <!-- ===================================== -->

            <!--
                action="/login"
                -> envía el formulario a la ruta login
                
                method="post"
                -> protege los datos sensibles
                
                @csrf
                -> token de seguridad obligatorio en Laravel
 
                -->
                @if (session('registro_exitoso'))

  <div
    style="
      background:#dcfce7;
      color:#166534;
      padding:16px;
      border-radius:10px;
      margin-bottom:16px;
      border:1px solid #86efac;
      line-height:1.6;
      font-weight:700;
    "
  >
    <strong>
      {{ session('registro_exitoso') }}
    </strong>

    <br>

    <span style="font-weight:500;">
      Serás redirigido a la página de inicio de sesión en
      <span id="countdownLogin">5</span>
      segundos...
    </span>
  </div>

  <script>
    let segundosLogin = 5;

    const countdownLogin = document.getElementById('countdownLogin');

    const intervaloLogin = setInterval(() => {
      segundosLogin--;

      countdownLogin.textContent = segundosLogin;

      if (segundosLogin <= 0) {
        clearInterval(intervaloLogin);
        window.location.href = '/login';
      }
    }, 1000);
  </script>

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
            <form
                class="card"
                action="/login"
                method="post"
                autocomplete="on"
                novalidate
            >

                @csrf

                <!-- ===================================== -->
                <!-- FIELDSET -->
                <!-- ===================================== -->

                <fieldset class="form__fieldset">

                    <!-- Título del grupo -->
                    <legend class="form__legend">
                        Datos de acceso
                    </legend>

                    <!-- ===================================== -->
                    <!-- INPUT EMAIL -->
                    <!-- ===================================== -->

                    <div class="form__group">

                        <label
                            class="form__label"
                            for="email"
                        >
                            Correo electrónico
                        </label>

                        <input
                            class="form__input"
                            type="email"
                            id="email"
                            name="email"
                            placeholder="tu@correo.com"
                            required
                        />

                    </div>


                    <!-- ===================================== -->
                    <!-- INPUT PASSWORD -->
                    <!-- ===================================== -->

                    <div class="form__group">

                        <label
                            class="form__label"
                            for="password"
                        >
                            Contraseña
                        </label>

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


                <!-- ===================================== -->
                <!-- OPCIONES EXTRA -->
                <!-- ===================================== -->

                <div class="form__row">

                    <!-- Checkbox recordar -->
                    <label class="form__checkbox">

                        <input
                            class="form__checkbox-input"
                            type="checkbox"
                            name="remember"
                        >

                        <span class="form__checkbox-text">
                            Recordarme
                        </span>

                    </label>

                    <!-- Recuperar contraseña -->
                    <a class="form__link" href="#">
                        ¿Olvidaste tu contraseña?
                    </a>

                </div>


                <!-- ===================================== -->
                <!-- BOTÓN -->
                <!-- ===================================== -->

                <button class="button" type="submit">
                    Entrar
                </button>

                <div class="form__divider">
                    <span></span>
                    <small>o</small>
                    <span></span>
                </div>

                <button class="button button--google" id="googleLoginButton" type="button">
                    Iniciar sesión con Google
                </button>


                <!-- ===================================== -->
                <!-- FOOTER -->
                <!-- ===================================== -->

                <p class="form__footer">

                    ¿No tienes cuenta?

                    <!-- Redirección al registro -->
                    <a class="form__link" href="/registro">
                        Crear cuenta
                    </a>

                </p>

            </form>

        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script>
        const supabaseUrl = @json($supabaseUrl);
        const supabaseAnonKey = @json($supabaseAnonKey);
        const googleLoginRedirectTo = @json($googleLoginRedirectTo);
        const googleLoginButton = document.getElementById('googleLoginButton');
        const supabaseClient = window.supabase.createClient(supabaseUrl, supabaseAnonKey);

        googleLoginButton.addEventListener('click', async () => {
            googleLoginButton.disabled = true;
            googleLoginButton.textContent = 'Conectando con Google...';

            const { error } = await supabaseClient.auth.signInWithOAuth({
                provider: 'google',
                options: {
                    redirectTo: googleLoginRedirectTo,
                },
            });

            if (error) {
                googleLoginButton.disabled = false;
                googleLoginButton.textContent = 'Iniciar sesión con Google';
                alert('No se pudo iniciar sesión con Google. Intenta nuevamente.');
            }
        });
    </script>

</body>

</html>
