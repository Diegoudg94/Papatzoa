@php
  $nombreGoogle = old('nombre', $googleUser['nombre'] ?? '');
  $correoGoogle = $googleUser['correo'] ?? '';
  $avatarGoogle = $googleUser['avatar_url'] ?? '';
  $nombreCorto = trim(explode(' ', trim($nombreGoogle))[0] ?? '');
  $iniciales = collect(explode(' ', trim($nombreGoogle)))
      ->filter()
      ->take(2)
      ->map(fn ($parte) => mb_strtoupper(mb_substr($parte, 0, 1)))
      ->implode('');
  $terapeutaActivo = old('terapeuta') === '1' || old('terapeuta') === 1 || old('terapeuta') === true;
@endphp

<!DOCTYPE html>
<html class="light" lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Papatzoa | Completar Registro</title>
  <link href="https://fonts.googleapis.com" rel="preconnect" />
  <link crossorigin href="https://fonts.gstatic.com" rel="preconnect" />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Hanken+Grotesk:wght@600&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            sage: '#4A6741',
            ivory: '#FDFCF8',
            'soft-iris': '#536DFE',
            background: '#f9f9fc',
            surface: '#f9f9fc',
            'surface-container-low': '#f3f3f6',
            'surface-container': '#eeeef0',
            'on-surface': '#1a1c1e',
            'on-surface-variant': '#444655',
            outline: '#757686',
            'outline-variant': '#c5c5d8',
            error: '#ba1a1a',
            'error-container': '#ffdad6',
          },
          borderRadius: {
            DEFAULT: '8px',
            lg: '12px',
            xl: '16px',
            '2xl': '24px',
            full: '9999px',
          },
          fontFamily: {
            'body-md': ['Manrope', 'sans-serif'],
            'headline-md': ['Manrope', 'sans-serif'],
            'label-md': ['Hanken Grotesk', 'sans-serif'],
          },
          fontSize: {
            'body-md': ['16px', { lineHeight: '1.6', fontWeight: '400' }],
            'headline-md': ['24px', { lineHeight: '1.3', fontWeight: '700' }],
            'label-md': ['14px', { lineHeight: '1.4', letterSpacing: '0.05em', fontWeight: '600' }],
          },
        },
      },
    };
  </script>
  <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    .soft-shadow {
      box-shadow: 0 10px 30px rgba(74, 103, 65, 0.10);
    }

    .bg-mesh {
      background-color: #FDFCF8;
      background-image:
        radial-gradient(at 0% 0%, rgba(212, 187, 255, 0.12) 0px, transparent 50%),
        radial-gradient(at 100% 0%, rgba(186, 195, 255, 0.14) 0px, transparent 50%);
    }

    .form-input-focus:focus {
      outline: none;
      border-color: #536DFE;
      box-shadow: 0 0 0 4px rgba(83, 109, 254, 0.10);
    }

    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 44px;
      height: 24px;
      flex: 0 0 auto;
    }

    .toggle-switch input[type='checkbox'] {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      inset: 0;
      background-color: #e2e2e5;
      transition: .25s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: .25s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: #4A6741;
    }

    input:checked + .slider:before {
      transform: translateX(20px);
    }

  </style>
</head>

<body class="bg-mesh font-body-md text-on-surface selection:bg-sage/20 selection:text-sage">
  <header class="fixed top-0 z-50 w-full bg-white/70 backdrop-blur-md">
    <nav class="mx-auto flex h-16 w-full max-w-[1280px] items-center justify-between px-5 md:px-6">
      <a class="text-headline-md font-extrabold tracking-tight text-sage" href="/">Papatzoa</a>

      <div class="hidden items-center gap-8 md:flex">
        <a class="font-label-md text-label-md text-on-surface-variant transition-colors hover:text-sage" href="/#acerca">Acerca de</a>
        <a class="font-label-md text-label-md text-on-surface-variant transition-colors hover:text-sage" href="/#como-funciona">Cómo funciona</a>
        <a class="font-label-md text-label-md text-on-surface-variant transition-colors hover:text-sage" href="/#valores">Valores</a>
      </div>

      <a class="rounded-full bg-sage px-6 py-2 font-label-md text-label-md text-white transition-all duration-200 hover:opacity-90 active:scale-95" href="#">
        Soporte
      </a>
    </nav>
  </header>

  <main class="flex min-h-screen items-center justify-center px-5 pb-20 pt-32">
    <section class="w-full max-w-[540px] rounded-2xl border border-sage/5 bg-white p-8 soft-shadow md:p-10">
      <div class="mb-10 text-center">
        <div class="relative mb-6 inline-block">
          <div class="mx-auto h-24 w-24 overflow-hidden rounded-full border-4 border-sage/10 bg-surface-container-low p-1 shadow-inner">
            @if ($avatarGoogle)
              <img
                class="h-full w-full rounded-full object-cover"
                src="{{ $avatarGoogle }}"
                alt="Foto de perfil de {{ $nombreGoogle ?: 'Google' }}"
              />
            @else
              <div class="flex h-full w-full items-center justify-center rounded-full bg-sage/10 text-2xl font-bold text-sage">
                {{ $iniciales ?: 'U' }}
              </div>
            @endif
          </div>
          <div class="absolute bottom-0 right-0 flex items-center justify-center rounded-full border-2 border-white bg-sage p-1.5 text-white shadow-sm">
            <span class="material-symbols-outlined text-[14px]">verified</span>
          </div>
        </div>

        <h1 class="mb-2 font-headline-md text-headline-md text-on-surface">
          {{ $nombreCorto ? 'Casi listo, ' . $nombreCorto : 'Casi listo' }}
        </h1>
        <p class="mx-auto max-w-[320px] text-body-md text-on-surface-variant">
          Confirma tus datos para empezar tu camino de bienestar.
        </p>
      </div>

      @if ($errors->any())
        <div class="register-error mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
          @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
          @endforeach
        </div>
      @endif

      @if (session('success'))
        <div class="mb-5 rounded-xl border border-sage/20 bg-sage/10 px-4 py-3 text-sm text-sage">
          {{ session('success') }}
        </div>
      @endif

      @if (session('error'))
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
          {{ session('error') }}
        </div>
      @endif

      <form method="POST" action="{{ url('/completar-registro-google') }}" class="space-y-6" autocomplete="on">
        @csrf

        <input type="hidden" name="nombre" value="{{ $nombreGoogle }}">
        <input type="hidden" name="correo" value="{{ $correoGoogle }}">
        <input type="hidden" name="avatar_url" value="{{ $avatarGoogle }}">

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="space-y-2">
            <label class="ml-1 block font-label-md text-label-md text-on-surface-variant">Nombre completo</label>
            <div class="flex items-center gap-3 rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3">
              <span class="material-symbols-outlined text-[20px] text-outline">person</span>
              <span class="font-medium text-on-surface/70">{{ $nombreGoogle }}</span>
            </div>
          </div>

          <div class="space-y-2">
            <label class="ml-1 block font-label-md text-label-md text-on-surface-variant">Correo electrónico</label>
            <div class="flex items-center gap-3 rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3">
              <span class="material-symbols-outlined text-[20px] text-outline">mail</span>
              <span class="truncate font-medium text-on-surface/70">{{ $correoGoogle }}</span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="space-y-2">
            <label class="ml-1 block font-label-md text-label-md text-on-surface-variant" for="edad">Edad</label>
            <input
              class="form-input-focus w-full rounded-xl border border-outline-variant bg-white px-4 py-3 text-on-surface transition-all placeholder:text-outline-variant"
              id="edad"
              name="edad"
              min="13"
              max="120"
              placeholder="25"
              type="number"
              value="{{ old('edad') }}"
              required
            />
          </div>

          <div class="space-y-2">
            <label class="ml-1 block font-label-md text-label-md text-on-surface-variant" for="sexo">Sexo</label>
            <div class="relative">
              <select
                class="form-input-focus w-full appearance-none rounded-xl border border-outline-variant bg-white px-4 py-3 text-on-surface transition-all"
                id="sexo"
                name="sexo"
                required
              >
                <option value="">Seleccionar</option>
                <option value="masculino" @selected(old('sexo') === 'masculino')>Masculino</option>
                <option value="femenino" @selected(old('sexo') === 'femenino')>Femenino</option>
                <option value="no-binario" @selected(old('sexo') === 'no-binario')>No binario</option>
                <option value="otro" @selected(old('sexo') === 'otro')>Otro / Prefiero no decir</option>
              </select>
              <span class="material-symbols-outlined pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-outline">expand_more</span>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-sage/10 bg-surface-container-low/50 p-4">
          <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <div class="rounded-lg bg-sage/10 p-2 text-sage">
                <span class="material-symbols-outlined">psychology</span>
              </div>
              <div>
                <label class="font-semibold leading-tight text-on-surface" for="terapeuta">¿Eres terapeuta?</label>
                <p class="text-[12px] text-on-surface-variant">Habilita funciones profesionales</p>
              </div>
            </div>

            <label class="toggle-switch" aria-label="Indicar si eres terapeuta">
              <input type="hidden" name="terapeuta" value="0">
              <input class="therapist-toggle" type="checkbox" name="terapeuta" value="1" id="terapeuta" @checked($terapeutaActivo)>
              <span class="slider"></span>
            </label>
          </div>

          <p id="avisoTerapeuta" class="{{ $terapeutaActivo ? '' : 'hidden' }} mt-4 rounded-lg border border-sage/20 bg-white px-4 py-3 text-sm leading-relaxed text-on-surface-variant">
            Después del registro podrás completar tu perfil profesional y subir tus credenciales para ser verificado.
          </p>
        </div>

        <div class="flex items-center justify-center gap-2 py-2">
          <span class="material-symbols-outlined text-[18px] text-sage">verified_user</span>
          <p class="font-label-md text-label-md text-on-surface-variant">Tus datos son tratados con total confidencialidad</p>
        </div>

        <button class="h-14 w-full rounded-xl bg-sage text-[18px] font-bold text-white shadow-lg shadow-sage/20 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-sage/30 active:scale-95" type="submit">
          Finalizar registro
        </button>
      </form>

      <div class="mt-8 flex justify-center gap-6 border-t border-outline-variant/30 pt-6">
        <a class="font-label-md text-label-md text-on-surface-variant underline decoration-outline-variant/50 underline-offset-4 transition-colors hover:text-sage" href="#">Términos</a>
        <a class="font-label-md text-label-md text-on-surface-variant underline decoration-outline-variant/50 underline-offset-4 transition-colors hover:text-sage" href="#">Privacidad</a>
      </div>
    </section>
  </main>

  <footer class="border-t border-outline-variant/30 bg-surface-container-low">
    <div class="mx-auto flex w-full max-w-[1280px] flex-col items-center justify-between gap-4 px-5 py-8 md:flex-row md:px-6">
      <div class="flex items-center gap-2">
        <span class="font-headline-md text-headline-md text-sage">Papatzoa</span>
        <span class="mx-2 h-1 w-1 rounded-full bg-outline-variant"></span>
        <span class="font-label-md text-label-md text-on-surface-variant">© {{ date('Y') }}</span>
      </div>
      <p class="font-label-md text-label-md text-on-surface-variant">Tecnología con sentido humano.</p>
      <div class="flex gap-6">
        <a class="font-label-md text-label-md text-on-surface-variant transition-colors hover:text-sage" href="#">Contacto</a>
        <a class="font-label-md text-label-md text-on-surface-variant transition-colors hover:text-sage" href="#">Soporte</a>
      </div>
    </div>
  </footer>

  <script>
    const terapeutaToggle = document.getElementById('terapeuta');
    const avisoTerapeuta = document.getElementById('avisoTerapeuta');

    if (terapeutaToggle && avisoTerapeuta) {
      terapeutaToggle.addEventListener('change', () => {
        avisoTerapeuta.classList.toggle('hidden', !terapeutaToggle.checked);
      });
    }
  </script>
  @include('partials.marker-widget')
</body>
</html>
