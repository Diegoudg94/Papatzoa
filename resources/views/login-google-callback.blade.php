<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Iniciando sesión con Google | Papatzoa</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
  <main class="page">
    <section class="card">
      <h1 class="title">Iniciando sesión</h1>
      <p class="subtitle" id="statusMessage">
        Estamos validando tu cuenta de Google.
      </p>

      <div class="auth-message auth-message--error" id="errorMessage"></div>

      <div class="form__footer">
        <a class="form__link" href="/login">Volver al inicio de sesión</a>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
  <script>
    const supabaseUrl = @json($supabaseUrl);
    const supabaseAnonKey = @json($supabaseAnonKey);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const statusMessage = document.getElementById('statusMessage');
    const errorMessage = document.getElementById('errorMessage');
    const supabaseClient = window.supabase.createClient(supabaseUrl, supabaseAnonKey);

    function showError(message) {
      statusMessage.textContent = 'No se pudo completar el inicio de sesión con Google.';
      errorMessage.textContent = message;
      errorMessage.style.display = 'block';
    }

    async function completeGoogleLogin() {
      const { data, error } = await supabaseClient.auth.getSession();

      if (error || !data.session || !data.session.user) {
        showError('No recibimos una sesión válida de Google. Intenta iniciar sesión nuevamente.');
        return;
      }

      const user = data.session.user;
      const metadata = user.user_metadata || {};

      const laravelResponse = await fetch('/login/google/validar', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
          email: user.email,
          supabase_id: user.id,
          name: metadata.full_name || metadata.name || user.email,
          avatar_url: metadata.avatar_url || metadata.picture || null,
        }),
      });

      const responseData = await laravelResponse.json();

      if (!responseData.redirect) {
        showError('Laravel no pudo validar tu cuenta.');
        return;
      }

      window.location.href = responseData.redirect;
    }

    completeGoogleLogin().catch(() => {
      showError('Ocurrió un error inesperado durante el inicio de sesión con Google.');
    });
  </script>
</body>
</html>
