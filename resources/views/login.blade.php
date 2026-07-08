<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Papatzoa - Iniciar sesión</title>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'surface-container': '#F6F3EE',
                        'surface-container-high': '#e9e5df',
                        'error-container': '#ffdad6',
                        'on-secondary-container': '#735f48',
                        'on-primary-fixed': '#002018',
                        'tertiary': '#804f48',
                        'surface-tint': '#376457',
                        'background': '#F6F3EE',
                        'inverse-surface': '#27313c',
                        'on-surface-variant': '#404945',
                        'on-tertiary-container': '#fffbff',
                        'tertiary-fixed': '#ffdad5',
                        'secondary-fixed-dim': '#dcc2a6',
                        'on-secondary-fixed-variant': '#56442e',
                        'tertiary-fixed-dim': '#f8b7ad',
                        'error': '#ba1a1a',
                        'primary-fixed-dim': '#a0d1c0',
                        'primary-container': '#507d6f',
                        'on-primary-container': '#f5fff9',
                        'secondary-fixed': '#fadec1',
                        'secondary-container': '#f7dbbe',
                        'on-error': '#ffffff',
                        'secondary': '#6f5b44',
                        'primary-fixed': '#bceddb',
                        'outline': '#717975',
                        'surface-dim': '#d1dbe8',
                        'surface-container-low': '#FFFFFF',
                        'outline-variant': '#c0c8c4',
                        'surface-container-lowest': '#FFFFFF',
                        'on-tertiary-fixed-variant': '#683a34',
                        'on-primary-fixed-variant': '#204f42',
                        'primary': '#376457',
                        'surface-bright': '#FFFFFF',
                        'tertiary-container': '#9c675f',
                        'inverse-on-surface': '#e8f2ff',
                        'on-tertiary-fixed': '#34100c',
                        'on-primary': '#ffffff',
                        'on-error-container': '#93000a',
                        'surface': '#F6F3EE',
                        'on-surface': '#1A1C1E',
                        'on-background': '#1A1C1E',
                        'on-tertiary': '#ffffff',
                        'on-secondary-fixed': '#271907',
                        'surface-container-highest': '#d9e3f1',
                        'inverse-primary': '#a0d1c0',
                        'surface-variant': '#d9e3f1',
                        'on-secondary': '#ffffff',
                    },
                    borderRadius: {
                        DEFAULT: '1rem',
                        lg: '2rem',
                        xl: '3rem',
                        full: '9999px',
                    },
                    spacing: {
                        xs: '4px',
                        margin: '32px',
                        gutter: '24px',
                        md: '24px',
                        lg: '48px',
                        xl: '80px',
                        sm: '12px',
                        base: '8px',
                    },
                    fontFamily: {
                        'body-md': ['Manrope'],
                        'label-md': ['Manrope'],
                        'body-lg': ['Manrope'],
                        'headline-lg': ['Manrope'],
                        'display-lg': ['Manrope'],
                        'headline-md': ['Manrope'],
                        'label-sm': ['Manrope'],
                    },
                    fontSize: {
                        'body-md': ['16px', { lineHeight: '24px', fontWeight: '400' }],
                        'label-md': ['14px', { lineHeight: '20px', letterSpacing: '0.01em', fontWeight: '500' }],
                        'body-lg': ['18px', { lineHeight: '28px', fontWeight: '400' }],
                        'headline-lg': ['32px', { lineHeight: '40px', letterSpacing: '-0.01em', fontWeight: '600' }],
                        'display-lg': ['48px', { lineHeight: '56px', letterSpacing: '-0.02em', fontWeight: '700' }],
                        'headline-md': ['24px', { lineHeight: '32px', fontWeight: '600' }],
                        'label-sm': ['12px', { lineHeight: '16px', letterSpacing: '0.05em', fontWeight: '600' }],
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Manrope', sans-serif;
            background-color: #F6F3EE;
            color: #1A1C1E;
        }

        .atmospheric-shadow {
            box-shadow: 0 10px 40px -10px rgba(55, 100, 87, 0.12);
        }

        .organic-shape {
            border-radius: 60% 40% 70% 30% / 40% 50% 60% 40%;
            filter: blur(60px);
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="min-h-screen overflow-x-hidden md:overflow-hidden">
    <div class="min-h-screen flex items-stretch">
        <section class="hidden md:flex w-1/2 bg-[#F6F3EE] relative overflow-hidden flex-col justify-between p-xl">
            <div class="absolute -top-20 -left-20 w-96 h-96 bg-primary opacity-10 organic-shape"></div>
            <div class="absolute top-1/2 -right-20 w-80 h-80 bg-tertiary opacity-10 organic-shape"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-sm mb-xl">
                    <span class="material-symbols-outlined text-primary text-[40px]">auto_awesome</span>
                    <h1 class="font-headline-md text-headline-md font-bold text-primary tracking-tight">Papatzoa</h1>
                </div>

                <h2 class="font-display-lg text-display-lg text-on-surface leading-tight mb-md max-w-lg">
                    Tu espacio seguro para registrar, comprender y acompañar tus emociones.
                </h2>

                <div class="flex flex-wrap gap-sm mt-lg">
                    <span class="px-md py-xs bg-white/60 backdrop-blur-sm rounded-full text-label-sm font-label-sm text-primary border border-primary/10">Seguimiento emocional</span>
                    <span class="px-md py-xs bg-white/60 backdrop-blur-sm rounded-full text-label-sm font-label-sm text-primary border border-primary/10">Acompañamiento terapéutico</span>
                    <span class="px-md py-xs bg-white/60 backdrop-blur-sm rounded-full text-label-sm font-label-sm text-primary border border-primary/10">Privacidad y confianza</span>
                </div>
            </div>

            <div class="flex-grow"></div>

            <div class="relative z-10 border-t border-outline-variant/20 pt-md flex items-center justify-between gap-md">
                <div class="flex flex-col gap-xs">
                    <p class="font-label-sm text-label-sm text-on-surface-variant opacity-80">© 2024 Papatzoa</p>
                    <p class="font-label-sm text-label-sm text-on-surface-variant opacity-60">Bienestar mental de grado clínico con calidez humana.</p>
                </div>

                <nav class="flex flex-wrap justify-end gap-md">
                    <a href="#" class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors">Privacidad</a>
                    <a href="#" class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors">Términos</a>
                    <a href="#" class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors">Contacto</a>
                    <a href="#" class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors">Soporte</a>
                </nav>
            </div>
        </section>

        <main class="w-full md:w-1/2 bg-surface-container-lowest flex items-center justify-center p-6 sm:p-margin">
            <div class="w-full max-w-[440px]">
                <div class="md:hidden flex items-center justify-center gap-xs mb-lg">
                    <span class="material-symbols-outlined text-primary text-[32px]">auto_awesome</span>
                    <span class="font-headline-md text-headline-md font-bold text-primary">Papatzoa</span>
                </div>

                <div class="bg-white p-6 sm:p-lg md:p-xl rounded-lg atmospheric-shadow border border-outline-variant/10">
                    <header class="text-center md:text-left mb-lg">
                        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Bienvenido de nuevo</h2>
                        <p class="font-body-md text-body-md text-on-surface-variant">Continúa tu seguimiento emocional y terapéutico.</p>
                    </header>

                    @if (session('registro_exitoso'))
                        <div class="mb-md rounded-[1rem] border border-green-200 bg-green-50 px-md py-sm text-green-800 font-body-md text-body-md">
                            <strong class="block font-bold">{{ session('registro_exitoso') }}</strong>
                            <span class="block mt-xs">
                                Serás redirigido a la página de inicio de sesión en
                                <span id="countdownLogin">5</span>
                                segundos...
                            </span>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-md rounded-[1rem] border border-green-200 bg-green-50 px-md py-sm text-green-800 font-body-md text-body-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-md rounded-[1rem] border border-red-200 bg-red-50 px-md py-sm text-red-800 font-body-md text-body-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('google_login_error'))
                        <div class="mb-md rounded-[1rem] border border-red-200 bg-red-50 px-md py-sm text-red-800 font-body-md text-body-md">
                            {{ session('google_login_error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="login-error mb-md rounded-[1rem] border border-red-200 bg-red-50 px-md py-sm text-red-800 font-body-md text-body-md">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/login') }}" class="space-y-md" autocomplete="on">
                        @csrf

                        <div class="space-y-xs">
                            <label class="font-label-md text-label-md text-on-surface ml-base" for="email">
                                Correo electrónico
                            </label>

                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">mail</span>
                                <input
                                    class="w-full pl-[52px] pr-md py-md bg-[#FBF9F6] border border-outline-variant rounded-full focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-body-md text-on-surface placeholder:text-outline/60"
                                    id="email"
                                    name="email"
                                    placeholder="nombre@ejemplo.com"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                >
                            </div>
                        </div>

                        <div class="space-y-xs">
                            <label class="font-label-md text-label-md text-on-surface ml-base" for="password">
                                Contraseña
                            </label>

                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">lock</span>
                                <input
                                    class="w-full pl-[52px] pr-[52px] py-md bg-[#FBF9F6] border border-outline-variant rounded-full focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-body-md text-on-surface placeholder:text-outline/60"
                                    id="password"
                                    name="password"
                                    placeholder="••••••••"
                                    type="password"
                                    required
                                >
                                <button
                                    class="absolute right-md top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors"
                                    type="button"
                                    id="togglePassword"
                                    aria-label="Mostrar contraseña"
                                    aria-pressed="false"
                                >
                                    <span class="material-symbols-outlined" id="togglePasswordIcon">visibility</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-sm px-base">
                            <label class="flex items-center gap-xs cursor-pointer group">
                                <input
                                    class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary/20 cursor-pointer"
                                    type="checkbox"
                                    name="remember"
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <span class="font-label-md text-label-md text-on-surface-variant group-hover:text-on-surface transition-colors">Recordarme</span>
                            </label>

                            <a class="font-label-md text-label-md text-primary hover:text-primary-container transition-all" href="#">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>

                        <button class="w-full py-md bg-primary text-white font-label-md text-label-md rounded-full hover:bg-primary-container active:scale-[0.98] transition-all atmospheric-shadow mt-base" type="submit">
                            Iniciar sesión
                        </button>
                    </form>

                    <div class="relative flex items-center justify-center my-lg">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-outline-variant/20"></div>
                        </div>
                        <span class="relative px-md bg-white font-label-sm text-label-sm text-outline-variant uppercase tracking-widest">o</span>
                    </div>

                    <button id="googleLoginButton" type="button" class="w-full py-md flex items-center justify-center gap-sm bg-white border border-outline-variant rounded-full font-label-md text-label-md text-on-surface-variant hover:bg-[#FBF9F6] active:scale-[0.98] transition-all disabled:cursor-not-allowed disabled:opacity-70">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"></path>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                        </svg>
                        <span id="googleLoginButtonText">Continuar con Google</span>
                    </button>

                    <p class="text-center mt-xl font-body-md text-body-md text-on-surface-variant">
                        ¿No tienes cuenta?
                        <a class="text-primary font-bold hover:text-primary-container" href="{{ url('/registro') }}">Crear cuenta</a>
                    </p>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const shouldShowPassword = passwordInput.type === 'password';

            passwordInput.type = shouldShowPassword ? 'text' : 'password';
            togglePassword.setAttribute('aria-pressed', shouldShowPassword ? 'true' : 'false');
            togglePassword.setAttribute('aria-label', shouldShowPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
            togglePasswordIcon.textContent = shouldShowPassword ? 'visibility_off' : 'visibility';
        });

        document.addEventListener('mousemove', (event) => {
            const shapes = document.querySelectorAll('.organic-shape');
            const x = event.clientX / window.innerWidth;
            const y = event.clientY / window.innerHeight;

            shapes.forEach((shape, index) => {
                const speed = (index + 1) * 30;
                shape.style.transform = `translate(${x * speed}px, ${y * speed}px)`;
            });
        });
    </script>

    @if (session('registro_exitoso'))
        <script>
            let segundosLogin = 5;
            const countdownLogin = document.getElementById('countdownLogin');

            const intervaloLogin = setInterval(() => {
                segundosLogin--;
                countdownLogin.textContent = segundosLogin;

                if (segundosLogin <= 0) {
                    clearInterval(intervaloLogin);
                    window.location.href = @json(url('/login'));
                }
            }, 1000);
        </script>
    @endif

    <script>
        const supabaseUrl = @json($supabaseUrl);
        const supabaseAnonKey = @json($supabaseAnonKey);
        const googleLoginRedirectTo = @json($googleLoginRedirectTo);
        const googleLoginButton = document.getElementById('googleLoginButton');
        const googleLoginButtonText = document.getElementById('googleLoginButtonText');
        const supabaseClient = window.supabase.createClient(supabaseUrl, supabaseAnonKey);

        googleLoginButton.addEventListener('click', async () => {
            googleLoginButton.disabled = true;
            googleLoginButtonText.textContent = 'Conectando con Google...';

            const { error } = await supabaseClient.auth.signInWithOAuth({
                provider: 'google',
                options: {
                    redirectTo: googleLoginRedirectTo,
                },
            });

            if (error) {
                googleLoginButton.disabled = false;
                googleLoginButtonText.textContent = 'Continuar con Google';
                alert('No se pudo iniciar sesión con Google. Intenta nuevamente.');
            }
        });
    </script>

    @include('partials.marker-widget')
</body>

</html>
