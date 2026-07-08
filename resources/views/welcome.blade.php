<!DOCTYPE html>
<html class="scroll-smooth" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Papatzoa | Salud mental + tecnología</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary": "#376457",
                        "primary-container": "#507d6f",
                        "on-primary": "#ffffff",
                        "background": "#F6F3EE",
                        "surface": "#F6F3EE",
                        "surface-container": "#F6F3EE",
                        "surface-container-low": "#FFFFFF",
                        "surface-container-lowest": "#FFFFFF",
                        "on-surface": "#1A1C1E",
                        "on-surface-variant": "#404945",
                        "outline": "#717975",
                        "outline-variant": "#c0c8c4",
                        "secondary": "#6f5b44",
                        "tertiary": "#804f48",
                        "on-tertiary": "#ffffff"
                    },
                    "borderRadius": {
                        "DEFAULT": "1rem",
                        "lg": "2rem",
                        "xl": "3rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "unit": "8px",
                        "gutter": "24px",
                        "section-padding": "80px",
                        "margin-mobile": "20px",
                        "container-max": "1280px"
                    },
                    "fontFamily": {
                        "headline-md": ["Manrope"],
                        "headline-lg-mobile": ["Manrope"],
                        "body-md": ["Manrope"],
                        "body-lg": ["Manrope"],
                        "headline-lg": ["Manrope"],
                        "label-md": ["Manrope"]
                    },
                    "fontSize": {
                        "headline-md": ["24px", {"lineHeight": "1.3", "fontWeight": "700"}],
                        "headline-lg-mobile": ["32px", {"lineHeight": "1.2", "fontWeight": "800"}],
                        "body-md": ["16px", {"lineHeight": "1.6", "fontWeight": "400"}],
                        "body-lg": ["18px", {"lineHeight": "1.6", "fontWeight": "400"}],
                        "headline-lg": ["48px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "800"}],
                        "label-md": ["14px", {"lineHeight": "1.4", "letterSpacing": "0.05em", "fontWeight": "600"}]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .atmospheric-shadow {
            box-shadow: 0 10px 40px -10px rgba(55, 100, 87, 0.12);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(55, 100, 87, 0.1);
        }
        body {
            background-color: #F6F3EE;
            font-family: 'Manrope', sans-serif;
            color: #1A1C1E;
        }
        .organic-shape {
            border-radius: 60% 40% 70% 30% / 40% 50% 60% 40%;
            filter: blur(60px);
        }
    </style>
</head>
<body class="antialiased overflow-x-hidden">
    <!-- TopNavBar -->
    <nav class="fixed top-0 w-full z-50 bg-surface/80 backdrop-blur-md border-b border-outline-variant/10">
        <div class="flex justify-between items-center w-full px-margin-mobile md:px-gutter max-w-container-max mx-auto h-20">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                <span class="text-headline-md font-headline-md font-extrabold text-primary">Papatzoa</span>
            </div>
            <div class="hidden md:flex items-center gap-10">
                <a class="font-body-md text-body-md text-primary font-bold border-b-2 border-primary pb-1" href="#acerca">Acerca de</a>
                <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="#como-funciona">Cómo funciona</a>
                <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="#valores">Valores</a>
            </div>
            <div class="flex items-center gap-4">
                <a class="hidden md:block font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ url('/login') }}">Iniciar sesión</a>
                <a class="bg-primary text-on-primary px-8 py-2.5 rounded-full font-label-md text-label-md hover:bg-primary-container transition-all active:scale-95 atmospheric-shadow" href="{{ url('/registro') }}">
                    Registrarse
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-40 pb-section-padding px-margin-mobile md:px-gutter max-w-container-max mx-auto">
        <!-- Subtle Decorations -->
        <div class="absolute -top-20 -left-20 w-96 h-96 bg-primary opacity-5 organic-shape pointer-events-none"></div>
        <div class="absolute top-1/2 -right-20 w-80 h-80 bg-tertiary opacity-5 organic-shape pointer-events-none"></div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center relative z-10">
            <div class="space-y-8">
                <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-primary/10 border border-primary/20">
                    <span class="font-label-md text-label-md text-primary uppercase tracking-widest">Salud mental + tecnología</span>
                </div>
                <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface leading-tight tracking-tight">
                    Tu salud mental también merece seguimiento.
                </h1>
                <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed max-w-xl">
                    Papatzoa toma inspiración del náhuatl <i class="italic">papatzoa</i>, raíz asociada al apapacho: cuidar, acompañar y abrazar con el alma. Desde esa idea, la plataforma conecta pacientes y terapeutas para registrar emociones y fortalecer el seguimiento en salud mental.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a class="h-14 px-10 rounded-full bg-primary text-on-primary font-label-md text-label-md atmospheric-shadow hover:bg-primary-container transition-all active:scale-95 inline-flex items-center justify-center" href="{{ url('/registro') }}">
                        Crear cuenta
                    </a>
                    <a class="h-14 px-10 rounded-full bg-white text-primary border border-primary/20 font-label-md text-label-md hover:bg-surface-container-low transition-colors active:scale-95 inline-flex items-center justify-center" href="{{ url('/login') }}">
                        Iniciar sesión
                    </a>
                </div>
            </div>
            <div class="relative group">
                <div class="relative aspect-square md:aspect-[4/5] rounded-[48px] overflow-hidden atmospheric-shadow border border-white/50">
                    <img class="w-full h-full object-cover" data-alt="A high-resolution, serene photograph of a modern therapeutic setting. A compassionate therapist sits in a soft chair holding a digital tablet while a patient relaxes on a nearby comfortable sofa in a bright, airy room with natural wood accents and large windows. The lighting is warm and natural, creating an atmosphere of professional empathy and trust. The overall aesthetic is clean and minimalist, aligning with a premium healthcare brand." src="https://lh3.googleusercontent.com/aida-public/AB6AXuD0z_vSgdVi6DXjh4I7ttCilht5daYSaT2D6_Dp5VjSYk8jBbTgl6nrBDWIcp20Tltfdms90axQKqwp1UxWUt7wzrTGst1bFhrt8nNKPleWDYmo_tFO_iMEtXPvSQLS-NIuXTdq6E2U2naUa2rPz1-59TrVGjZNSM1X65T08xBNwgESpOkVBDAxKFVVr16lYcqlLnsca3VZKdlatTsN7YvyhCwuUgfdNsO_0JxnPBDr7oiPZ1m0XPZ-6w" alt="Sesión terapéutica apoyada por tecnología"/>
                    <div class="absolute bottom-8 left-8 right-8 glass-card p-8 rounded-3xl shadow-xl">
                        <p class="font-headline-md text-headline-md text-on-surface">Seguimiento emocional</p>
                        <p class="font-body-md text-body-md text-on-surface-variant">Paciente + terapeuta + tecnología</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Value Prop Section -->
    <section class="py-section-padding bg-white/50" id="acerca">
        <div class="max-w-container-max mx-auto px-margin-mobile md:px-gutter">
            <div class="text-center mb-20 space-y-4">
                <span class="text-primary font-label-md text-label-md uppercase tracking-widest">¿Qué es Papatzoa?</span>
                <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Una plataforma para acompañar procesos terapéuticos.</h2>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl mx-auto">
                    Papatzoa permite centralizar información emocional relevante para que pacientes y terapeutas puedan dar seguimiento al bienestar mental de una forma más organizada, accesible y segura.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-white p-10 rounded-[40px] border border-outline-variant/20 atmospheric-shadow hover:-translate-y-2 transition-all group">
                    <div class="w-16 h-16 rounded-2xl bg-primary/5 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-primary text-4xl">psychology</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Diario emocional</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        El paciente puede registrar cómo se siente, qué pensamientos tuvo y qué situaciones afectaron su día a día mediante interfaces amigables.
                    </p>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-10 rounded-[40px] border border-outline-variant/20 atmospheric-shadow hover:-translate-y-2 transition-all group">
                    <div class="w-16 h-16 rounded-2xl bg-primary/5 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-primary text-4xl">clinical_notes</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Expediente digital</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        El terapeuta puede consultar información relevante del paciente desde un panel clínico centralizado, optimizando el tiempo de sesión.
                    </p>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-10 rounded-[40px] border border-outline-variant/20 atmospheric-shadow hover:-translate-y-2 transition-all group">
                    <div class="w-16 h-16 rounded-2xl bg-primary/5 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-primary text-4xl">encrypted</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Vinculación segura</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        Pacientes y terapeutas pueden vincularse mediante un PIN único, manteniendo control total sobre la privacidad de la relación clínica.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-section-padding" id="valores">
        <div class="max-w-container-max mx-auto px-margin-mobile md:px-gutter">
            <div class="flex flex-col md:flex-row justify-between items-end mb-20 gap-6 border-b border-outline-variant/20 pb-8">
                <div class="max-w-xl">
                    <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Tecnología con enfoque humano.</h2>
                </div>
                <div class="text-on-surface-variant font-body-md">Valores que guían nuestro propósito</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Value 1 -->
                <div class="p-10 rounded-[32px] bg-white border border-outline-variant/10 hover:border-primary/30 transition-all atmospheric-shadow">
                    <h4 class="font-label-md text-label-md text-primary mb-4 uppercase tracking-widest">Empatía</h4>
                    <p class="font-body-md text-body-md text-on-surface-variant">Diseñado para acompañar, no para juzgar los procesos individuales.</p>
                </div>
                <!-- Value 2 -->
                <div class="p-10 rounded-[32px] bg-white border border-outline-variant/10 hover:border-primary/30 transition-all atmospheric-shadow">
                    <h4 class="font-label-md text-label-md text-primary mb-4 uppercase tracking-widest">Privacidad</h4>
                    <p class="font-body-md text-body-md text-on-surface-variant">La información emocional debe tratarse con máxima responsabilidad.</p>
                </div>
                <!-- Value 3 -->
                <div class="p-10 rounded-[32px] bg-white border border-outline-variant/10 hover:border-primary/30 transition-all atmospheric-shadow">
                    <h4 class="font-label-md text-label-md text-primary mb-4 uppercase tracking-widest">Prevención</h4>
                    <p class="font-body-md text-body-md text-on-surface-variant">El seguimiento constante ayuda a detectar señales tempranas de alerta.</p>
                </div>
                <!-- Value 4 -->
                <div class="p-10 rounded-[32px] bg-white border border-outline-variant/10 hover:border-primary/30 transition-all atmospheric-shadow">
                    <h4 class="font-label-md text-label-md text-primary mb-4 uppercase tracking-widest">Innovación</h4>
                    <p class="font-body-md text-body-md text-on-surface-variant">Usamos tecnología para fortalecer el cuidado tradicional de la salud.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works Section -->
    <section class="py-section-padding bg-primary text-on-primary rounded-t-[60px]" id="como-funciona">
        <div class="max-w-container-max mx-auto px-margin-mobile md:px-gutter">
            <div class="text-center mb-20">
                <span class="text-white/60 font-label-md text-label-md uppercase tracking-widest block mb-4">Cómo funciona</span>
                <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-white">Un flujo simple para pacientes y terapeutas.</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="relative group">
                    <div class="w-14 h-14 rounded-full bg-white text-primary flex items-center justify-center font-bold text-xl mb-8 atmospheric-shadow">1</div>
                    <h4 class="font-headline-md text-headline-md text-white mb-4">Crear cuenta</h4>
                    <p class="font-body-md text-body-md text-white/80">El usuario se registra de forma sencilla como paciente o terapeuta profesional.</p>
                </div>
                <!-- Step 2 -->
                <div class="relative group">
                    <div class="w-14 h-14 rounded-full bg-white text-primary flex items-center justify-center font-bold text-xl mb-8 atmospheric-shadow">2</div>
                    <h4 class="font-headline-md text-headline-md text-white mb-4">Vincular terapeuta</h4>
                    <p class="font-body-md text-body-md text-white/80">El paciente ingresa el PIN de seguridad proporcionado por su terapeuta.</p>
                </div>
                <!-- Step 3 -->
                <div class="relative group">
                    <div class="w-14 h-14 rounded-full bg-white text-primary flex items-center justify-center font-bold text-xl mb-8 atmospheric-shadow">3</div>
                    <h4 class="font-headline-md text-headline-md text-white mb-4">Registrar emociones</h4>
                    <p class="font-body-md text-body-md text-white/80">El paciente documenta emociones, pensamientos y motivos de consulta.</p>
                </div>
                <!-- Step 4 -->
                <div class="relative group">
                    <div class="w-14 h-14 rounded-full bg-white text-primary flex items-center justify-center font-bold text-xl mb-8 atmospheric-shadow">4</div>
                    <h4 class="font-headline-md text-headline-md text-white mb-4">Dar seguimiento</h4>
                    <p class="font-body-md text-body-md text-white/80">El terapeuta consulta el expediente dinámico y da seguimiento al proceso.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="py-24 px-margin-mobile md:px-gutter max-w-container-max mx-auto">
        <div class="relative rounded-[60px] overflow-hidden bg-white p-12 md:p-24 text-center atmospheric-shadow border border-outline-variant/10">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary opacity-5 organic-shape"></div>
            <div class="relative z-10 max-w-3xl mx-auto space-y-10">
                <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Tu bienestar emocional merece acompañamiento continuo.</h2>
                <p class="font-body-lg text-body-lg text-on-surface-variant">Empieza a usar Papatzoa hoy y da el primer paso hacia un seguimiento más organizado de tu salud mental.</p>
                <div class="pt-4">
                    <a class="bg-primary text-white px-12 py-5 rounded-full font-headline-md text-headline-md hover:scale-105 hover:bg-primary-container transition-all active:scale-95 atmospheric-shadow inline-flex items-center justify-center" href="{{ url('/registro') }}">
                        Registrarme ahora
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-surface border-t border-outline-variant/20 pt-20 pb-12">
        <div class="max-w-container-max mx-auto px-margin-mobile md:px-gutter">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                        <span class="font-headline-md text-headline-md text-primary">Papatzoa</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant max-w-xs mb-8">
                        Tecnología con sentido humano para el acompañamiento y seguimiento de la salud mental.
                    </p>
                </div>
                <div>
                    <h5 class="font-label-md text-label-md text-on-surface mb-6 uppercase tracking-widest">Plataforma</h5>
                    <nav class="flex flex-col gap-4">
                        <a class="text-on-surface-variant hover:text-primary transition-colors" href="#como-funciona">Cómo funciona</a>
                        <a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Para Terapeutas</a>
                        <a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Para Pacientes</a>
                    </nav>
                </div>
                <div>
                    <h5 class="font-label-md text-label-md text-on-surface mb-6 uppercase tracking-widest">Compañía</h5>
                    <nav class="flex flex-col gap-4">
                        <a class="text-on-surface-variant hover:text-primary transition-colors" href="#acerca">Acerca de</a>
                        <a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Contacto</a>
                        <a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Privacidad</a>
                        <a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Términos</a>
                    </nav>
                </div>
            </div>
            <div class="pt-8 border-t border-outline-variant/20 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="font-label-md text-label-md text-on-surface-variant/60">© 2024 Papatzoa. Bienestar mental de grado clínico con calidez humana.</p>
                <div class="flex gap-8">
                    <a class="text-on-surface-variant/60 hover:text-primary transition-colors" href="#"><span class="material-symbols-outlined">language</span></a>
                    <a class="text-on-surface-variant/60 hover:text-primary transition-colors" href="#"><span class="material-symbols-outlined">mail</span></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const observerOptions = {
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('opacity-100', 'translate-y-0');
                    entry.target.classList.remove('opacity-0', 'translate-y-12');
                }
            });
        }, observerOptions);

        document.querySelectorAll('section').forEach(section => {
            section.classList.add('transition-all', 'duration-1000', 'opacity-0', 'translate-y-12');
            observer.observe(section);
        });

        window.addEventListener('load', () => {
            const hero = document.querySelector('section');
            if (hero) {
                hero.classList.remove('opacity-0', 'translate-y-12');
                hero.classList.add('opacity-100', 'translate-y-0');
            }
        });
    </script>
</body>
</html>
