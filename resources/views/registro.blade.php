<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Registro | Papatzoa</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Hanken+Grotesk:wght@600&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-highest": "#e2e2e5",
                        "on-error-container": "#93000a",
                        "on-tertiary-fixed": "#181b24",
                        "on-primary-fixed": "#000f5c",
                        "on-secondary-fixed": "#270058",
                        "on-background": "#1a1c1e",
                        "surface-dim": "#dadadc",
                        "on-primary": "#ffffff",
                        "primary": "#2d4add",
                        "background": "#f9f9fc",
                        "on-surface-variant": "#444655",
                        "on-surface": "#1a1c1e",
                        "on-secondary-container": "#533487",
                        "on-primary-container": "#fffbff",
                        "inverse-surface": "#2f3133",
                        "surface-variant": "#e2e2e5",
                        "outline": "#757686",
                        "on-secondary-fixed-variant": "#543589",
                        "on-secondary": "#ffffff",
                        "primary-container": "#4b65f7",
                        "primary-fixed": "#dee0ff",
                        "surface-container-high": "#e8e8ea",
                        "tertiary-container": "#71747f",
                        "on-tertiary-container": "#fefcff",
                        "error": "#ba1a1a",
                        "secondary-fixed": "#ebdcff",
                        "on-primary-fixed-variant": "#052fc8",
                        "surface-bright": "#f9f9fc",
                        "surface": "#f9f9fc",
                        "surface-container-low": "#f3f3f6",
                        "outline-variant": "#c5c5d8",
                        "secondary-fixed-dim": "#d4bbff",
                        "tertiary": "#585c66",
                        "on-tertiary": "#ffffff",
                        "surface-container": "#eeeef0",
                        "secondary": "#6d4ea2",
                        "on-tertiary-fixed-variant": "#434751",
                        "on-error": "#ffffff",
                        "surface-tint": "#314ddf",
                        "inverse-on-surface": "#f0f0f3",
                        "surface-container-lowest": "#ffffff",
                        "secondary-container": "#c5a3ff",
                        "inverse-primary": "#bac3ff",
                        "tertiary-fixed-dim": "#c3c6d2",
                        "error-container": "#ffdad6",
                        "tertiary-fixed": "#e0e2ef",
                        "primary-fixed-dim": "#bac3ff",
                        "sage-deep": "#4A6741",
                        "ivory-soft": "#FAF9F6"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        "2xl": "1rem",
                        full: "9999px"
                    },
                    fontFamily: {
                        "headline-md": ["Manrope"],
                        "body-lg": ["Manrope"],
                        "headline-lg": ["Manrope"],
                        "body-md": ["Manrope"],
                        "label-md": ["Hanken Grotesk"],
                        "headline-lg-mobile": ["Manrope"]
                    },
                    fontSize: {
                        "headline-md": ["24px", { lineHeight: "1.3", fontWeight: "700" }],
                        "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }],
                        "headline-lg": ["48px", { lineHeight: "1.2", letterSpacing: "0", fontWeight: "800" }],
                        "body-md": ["16px", { lineHeight: "1.6", fontWeight: "400" }],
                        "label-md": ["14px", { lineHeight: "1.4", letterSpacing: "0.05em", fontWeight: "600" }],
                        "headline-lg-mobile": ["32px", { lineHeight: "1.2", fontWeight: "800" }]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .form-card-shadow {
            box-shadow: 0 10px 30px rgba(83, 109, 254, 0.08);
        }
        input:focus, select:focus {
            outline: none;
            border-color: #2d4add;
            box-shadow: 0 0 0 4px rgba(45, 74, 221, 0.1);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e2e5;
            border-radius: 10px;
        }
        @keyframes fade-in-down {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-down {
            animation: fade-in-down 0.3s ease-out forwards;
        }
    </style>
</head>
<body class="bg-ivory-soft font-body-md text-on-surface overflow-x-hidden">
    <main class="min-h-screen flex flex-col md:flex-row">
        <section class="hidden md:flex md:w-5/12 lg:w-1/2 p-12 lg:p-20 flex-col justify-between relative overflow-hidden bg-ivory-soft border-r border-outline-variant/20">
            <div class="z-10">
                <div class="flex items-center gap-2 mb-12">
                    <span class="text-headline-md font-headline-md font-extrabold text-primary">Papatzoa</span>
                </div>

                <div class="max-w-md">
                    <h1 class="font-headline-lg text-headline-lg text-on-background mb-6 leading-tight">
                        Crea tu espacio seguro para comprender y acompañar tus emociones.
                    </h1>
                    <p class="font-body-lg text-body-lg text-on-surface-variant mb-10">
                        Papatzoa es más que una herramienta; es un refugio digital diseñado para transitar el camino del bienestar emocional con respaldo profesional y privacidad absoluta.
                    </p>

                    <div class="flex flex-wrap gap-3 mb-12">
                        <span class="px-4 py-2 bg-primary/5 text-primary rounded-full font-label-md text-label-md border border-primary/10">Seguimiento emocional</span>
                        <span class="px-4 py-2 bg-primary/5 text-primary rounded-full font-label-md text-label-md border border-primary/10">Privacidad y confianza</span>
                        <span class="px-4 py-2 bg-primary/5 text-primary rounded-full font-label-md text-label-md border border-primary/10">Acompañamiento terapéutico</span>
                        <span class="px-4 py-2 bg-primary/5 text-primary rounded-full font-label-md text-label-md border border-primary/10">Bienestar mental</span>
                    </div>
                </div>
            </div>

            <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
        </section>

        <section class="flex-1 flex items-center justify-center p-6 md:p-12 lg:p-20 bg-background custom-scrollbar md:overflow-y-auto">
            <div class="w-full max-w-xl">
                <div class="md:hidden flex justify-center mb-8">
                    <span class="text-headline-md font-headline-md font-extrabold text-primary">Papatzoa</span>
                </div>

                <div class="bg-surface-container-lowest p-8 md:p-12 rounded-2xl form-card-shadow border border-outline-variant/20">
                    <header class="mb-10">
                        <h2 class="font-headline-md text-headline-md text-on-surface mb-2">Crear cuenta</h2>
                        <p class="text-on-surface-variant">Completa tus datos para comenzar tu viaje.</p>
                    </header>

                    @if(session('success'))
                        <div id="successMessage" class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm leading-relaxed text-green-800">
                            <strong class="block font-bold">Usuario registrado exitosamente.</strong>
                            Serás redirigido a la página de inicio de sesión en
                            <span id="countdown">5</span>
                            segundos...
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm leading-relaxed text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('google_login_error'))
                        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm leading-relaxed text-red-800">
                            {{ session('google_login_error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="register-error mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm leading-relaxed text-red-800">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <a class="w-full h-14 bg-white border border-outline-variant hover:bg-surface transition-all rounded-xl flex items-center justify-center gap-3 font-headline-md text-body-md text-on-surface mb-4 no-underline" href="{{ url('/registro/google') }}">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                        </svg>
                        Continuar con Google
                    </a>

                    <div class="relative flex items-center py-4 mb-6">
                        <div class="flex-grow border-t border-outline-variant/30"></div>
                        <span class="flex-shrink mx-4 text-on-surface-variant font-label-md text-label-md">o</span>
                        <div class="flex-grow border-t border-outline-variant/30"></div>
                    </div>

                    <form method="POST" action="{{ url('/registro') }}" id="registrationForm" class="space-y-6" autocomplete="on">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-2">
                                <label class="font-label-md text-label-md text-on-surface-variant" for="nombre">Nombre</label>
                                <input class="w-full h-12 px-4 rounded-lg bg-surface border border-outline-variant transition-all focus:border-primary" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Tu nombre" required type="text">
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="font-label-md text-label-md text-on-surface-variant" for="apellido">Apellido</label>
                                <input class="w-full h-12 px-4 rounded-lg bg-surface border border-outline-variant transition-all focus:border-primary" id="apellido" name="apellido" value="{{ old('apellido') }}" placeholder="Tus apellidos" required type="text">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-2">
                                <label class="font-label-md text-label-md text-on-surface-variant" for="sexo">Sexo</label>
                                <select class="w-full h-12 px-4 rounded-lg bg-surface border border-outline-variant transition-all focus:border-primary" id="sexo" name="sexo" required>
                                    <option disabled value="" {{ old('sexo') ? '' : 'selected' }}>Selecciona una opción</option>
                                    <option value="femenino" {{ old('sexo') === 'femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="masculino" {{ old('sexo') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="otro" {{ old('sexo') === 'otro' ? 'selected' : '' }}>Otro</option>
                                    <option value="prefiero_no_decir" {{ old('sexo') === 'prefiero_no_decir' ? 'selected' : '' }}>Prefiero no decirlo</option>
                                </select>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="font-label-md text-label-md text-on-surface-variant" for="edad">Edad</label>
                                <input class="w-full h-12 px-4 rounded-lg bg-surface border border-outline-variant transition-all focus:border-primary" id="edad" min="10" max="120" name="edad" value="{{ old('edad') }}" placeholder="Tu edad" required type="number">
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-label-md text-label-md text-on-surface-variant" for="correo">Correo electrónico</label>
                            <input class="w-full h-12 px-4 rounded-lg bg-surface border border-outline-variant transition-all focus:border-primary" id="correo" name="correo" type="email" value="{{ old('correo') }}" placeholder="tucorreo@ejemplo.com" required>
                        </div>

                        <div class="flex flex-col gap-2">
                            <div class="flex justify-between items-center">
                                <label class="font-label-md text-label-md text-on-surface-variant" for="password">Contraseña</label>
                                <button class="text-primary font-label-md text-label-md hover:underline" id="togglePassword" type="button">Mostrar</button>
                            </div>
                            <input class="w-full h-12 px-4 rounded-lg bg-surface border border-outline-variant transition-all focus:border-primary" id="password" name="password" type="password" placeholder="********" required minlength="8" pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" title="La contraseña debe tener mínimo 8 caracteres, una mayúscula, un número y un carácter especial.">
                            <div class="flex items-start gap-2 mt-1">
                                <span class="material-symbols-outlined text-[16px] text-tertiary-container mt-0.5">info</span>
                                <p class="text-[12px] leading-snug text-on-surface-variant">8 caracteres, una mayúscula, un número y un símbolo.</p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="font-label-md text-label-md text-on-surface-variant" for="password_confirmation">Confirmar contraseña</label>
                            <input class="w-full h-12 px-4 rounded-lg bg-surface border border-outline-variant transition-all focus:border-primary" id="password_confirmation" name="password_confirmation" type="password" placeholder="Repite tu contraseña" required>
                        </div>

                        <div class="flex flex-col gap-3 pt-2">
                            <label class="flex items-start gap-3 cursor-pointer group" for="isTherapist">
                                <input class="mt-1 w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary/20 transition-all" type="checkbox" name="terapeuta" value="1" id="isTherapist" {{ old('terapeuta') ? 'checked' : '' }}>
                                <div class="flex flex-col">
                                    <span class="font-label-md text-label-md text-on-surface group-hover:text-primary transition-colors">¿Eres terapeuta?</span>
                                    <p class="{{ old('terapeuta') ? '' : 'hidden' }} text-[13px] text-on-surface-variant mt-1 leading-relaxed border-l-2 border-primary/20 pl-3" id="therapistInfo">
                                        Después del registro podrás completar tu perfil profesional y subir tus credenciales para ser verificado por nuestro equipo.
                                    </p>
                                </div>
                            </label>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-surface-container rounded-lg">
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                            <p class="text-[12px] text-on-tertiary-fixed-variant">Tus datos son tratados con confidencialidad y bajo protocolos de seguridad clínica.</p>
                        </div>

                        <button class="w-full h-14 bg-sage-deep hover:bg-[#3d5536] text-white font-headline-md text-body-md rounded-xl shadow-lg transition-all active:scale-[0.98] mt-4" type="submit">
                            Crear cuenta
                        </button>

                        <div class="text-center pt-4">
                            <p class="text-on-surface-variant font-body-md">
                                ¿Ya tienes cuenta?
                                <a class="text-primary font-bold hover:underline" href="{{ url('/login') }}">Inicia sesión</a>
                            </p>
                        </div>
                    </form>
                </div>

                <footer class="mt-8 flex flex-wrap justify-center gap-x-6 gap-y-2 opacity-60">
                    <span class="font-label-md text-[12px] text-on-surface">Privacidad</span>
                    <span class="font-label-md text-[12px] text-on-surface">Términos</span>
                    <span class="font-label-md text-[12px] text-on-surface">Soporte</span>
                </footer>
            </div>
        </section>
    </main>

    <script>
        const therapistCheckbox = document.getElementById('isTherapist');
        const therapistInfo = document.getElementById('therapistInfo');

        if (therapistCheckbox && therapistInfo) {
            therapistCheckbox.addEventListener('change', () => {
                if (therapistCheckbox.checked) {
                    therapistInfo.classList.remove('hidden');
                    therapistInfo.classList.add('animate-fade-in-down');
                } else {
                    therapistInfo.classList.add('hidden');
                    therapistInfo.classList.remove('animate-fade-in-down');
                }
            });
        }

        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');

        if (toggleBtn && passwordInput && confirmInput) {
            toggleBtn.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                confirmInput.setAttribute('type', type);
                toggleBtn.textContent = type === 'password' ? 'Mostrar' : 'Ocultar';
            });
        }

        const countdownElement = document.getElementById('countdown');

        if (countdownElement) {
            let segundos = 5;

            const intervalo = setInterval(() => {
                segundos--;
                countdownElement.textContent = segundos;

                if (segundos <= 0) {
                    clearInterval(intervalo);
                    window.location.href = '{{ url('/login') }}';
                }
            }, 1000);
        }
    </script>
    @include('partials.marker-widget')
</body>
</html>
