<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Conectando con Google | Papatzoa</title>
  <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>
  <main class="page">
    <section class="card">
      <h1>Conectando con Google</h1>
      <p id="statusMessage" style="margin-top: 8px; color:#6b7280; line-height:1.6;">
        Estamos validando tu cuenta.
      </p>

      <div
        id="errorMessage"
        style="
          display:none;
          background:#fee2e2;
          color:#991b1b;
          padding:12px;
          border-radius:8px;
          margin-top:16px;
          border:1px solid #fca5a5;
          line-height:1.5;
        "
      ></div>

      <div class="form__footer">
        <a class="form__link" href="/registro">Volver al registro</a>
      </div>
    </section>
  </main>

  <script>
    const supabaseUrl = @json($supabaseUrl);
    const supabaseAnonKey = @json($supabaseAnonKey);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const statusMessage = document.getElementById('statusMessage');
    const errorMessage = document.getElementById('errorMessage');

    function showError(message) {
      statusMessage.textContent = 'No se pudo completar el registro con Google.';
      errorMessage.textContent = message;
      errorMessage.style.display = 'block';
    }

    async function completeGoogleCallback() {
      const hashParams = new URLSearchParams(window.location.hash.replace(/^#/, ''));
      const accessToken = hashParams.get('access_token');

      if (!accessToken) {
        showError('No recibimos el token de Google. Intenta registrarte nuevamente.');
        return;
      }

      const userResponse = await fetch(`${supabaseUrl}/auth/v1/user`, {
        headers: {
          'apikey': supabaseAnonKey,
          'Authorization': `Bearer ${accessToken}`,
        },
      });

      if (!userResponse.ok) {
        showError('Supabase no pudo validar tu cuenta de Google.');
        return;
      }

      const user = await userResponse.json();
      const metadata = user.user_metadata || {};

      const laravelResponse = await fetch('/registro/google/callback', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
          supabase_id: user.id,
          nombre: metadata.full_name || metadata.name || user.email,
          correo: user.email,
          avatar_url: metadata.avatar_url || metadata.picture || null,
        }),
      });

      if (!laravelResponse.ok) {
        showError('Laravel no pudo guardar los datos temporales de Google.');
        return;
      }

      const data = await laravelResponse.json();
      window.location.href = data.redirect;
    }

    completeGoogleCallback().catch(() => {
      showError('Ocurrió un error inesperado durante el registro con Google.');
    });
  </script>
</body>
</html>
