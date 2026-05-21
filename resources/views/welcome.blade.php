<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Papatzoa | Salud mental y seguimiento emocional</title>

    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>

<body>

    <!-- HEADER -->
    <header class="header">
        <div class="header__container">

            <a href="/" class="brand">
                <span class="brand__icon">✳︎</span>
                <span class="brand__name">Papatzoa</span>
            </a>

            <nav class="nav">
                <a href="#acerca">Acerca de</a>
                <a href="#funciona">Cómo funciona</a>
                <a href="#valores">Valores</a>
                <a href="/login" class="nav__link">Iniciar sesión</a>
                <a href="/registro" class="nav__button">Registrarse</a>
            </nav>

        </div>
    </header>


    <main>

        <!-- HERO -->
        <section class="hero">
            <div class="hero__container">

                <div class="hero__content">

                    <span class="hero__badge">
                        Salud mental + tecnología
                    </span>

                    <h1 class="hero__title">
                        Tu salud mental también merece seguimiento.
                    </h1>
                   <p class="hero__text">
    Papatzoa toma inspiración del náhuatl <em>papatzoa</em>, raíz asociada al apapacho:
    cuidar, acompañar y abrazar con el alma. Desde esa idea, la plataforma conecta pacientes
    y terapeutas para registrar emociones y fortalecer el seguimiento en salud mental.
</p>
                    <div class="hero__actions">
                        <a href="/registro" class="button button--primary">
                            Crear cuenta
                        </a>

                        <a href="/login" class="button button--secondary">
                            Iniciar sesión
                        </a>
                    </div>

                </div>

                <div class="hero__image-card">

                    <img
                        src="{{ asset('images/pexels-polina-tankilevitch-5234576.jpg') }}"
                        alt="Sesión terapéutica apoyada por tecnología"
                        class="hero__image"
                    >

                    <div class="hero__image-badge">
                        <strong>Seguimiento emocional</strong>
                        <span>Paciente + terapeuta + tecnología</span>
                    </div>

                </div>

            </div>
        </section>


        <!-- ACERCA -->
        <section class="section" id="acerca">
            <div class="section__container">

                <div class="section__heading">
                    <span class="section__tag">¿Qué es Papatzoa?</span>

                    <h2>
                        Una plataforma para acompañar procesos terapéuticos.
                    </h2>

                    <p>
                        Papatzoa permite centralizar información emocional relevante para que pacientes y terapeutas
                        puedan dar seguimiento al bienestar mental de una forma más organizada, accesible y segura.
                    </p>
                </div>

                <div class="cards">

                    <article class="card">
                        <div class="card__icon">🧠</div>
                        <h3>Diario emocional</h3>
                        <p>
                            El paciente puede registrar cómo se siente, qué pensamientos tuvo y qué situaciones afectaron su día.
                        </p>
                    </article>

                    <article class="card">
                        <div class="card__icon">📋</div>
                        <h3>Expediente digital</h3>
                        <p>
                            El terapeuta puede consultar información relevante del paciente desde un panel clínico centralizado.
                        </p>
                    </article>

                    <article class="card">
                        <div class="card__icon">🔐</div>
                        <h3>Vinculación segura</h3>
                        <p>
                            Pacientes y terapeutas pueden vincularse mediante un PIN único, manteniendo control sobre la relación clínica.
                        </p>
                    </article>

                </div>

            </div>
        </section>


        <!-- CÓMO FUNCIONA -->
        <section class="section section--soft" id="funciona">
            <div class="section__container">

                <div class="section__heading">
                    <span class="section__tag">Cómo funciona</span>

                    <h2>
                        Un flujo simple para pacientes y terapeutas.
                    </h2>
                </div>

                <div class="steps">

                    <div class="step">
                        <span>1</span>
                        <h3>Crear cuenta</h3>
                        <p>El usuario se registra como paciente o terapeuta.</p>
                    </div>

                    <div class="step">
                        <span>2</span>
                        <h3>Vincular terapeuta</h3>
                        <p>El paciente ingresa el PIN proporcionado por su terapeuta.</p>
                    </div>

                    <div class="step">
                        <span>3</span>
                        <h3>Registrar emociones</h3>
                        <p>El paciente documenta emociones, pensamientos y motivos de consulta.</p>
                    </div>

                    <div class="step">
                        <span>4</span>
                        <h3>Dar seguimiento</h3>
                        <p>El terapeuta consulta el expediente y da seguimiento al proceso.</p>
                    </div>

                </div>

            </div>
        </section>


        <!-- VALORES -->
        <section class="section" id="valores">
            <div class="section__container">

                <div class="section__heading">
                    <span class="section__tag">Nuestros valores</span>

                    <h2>
                        Tecnología con enfoque humano.
                    </h2>
                </div>

                <div class="values">

                    <article class="value">
                        <h3>Empatía</h3>
                        <p>Diseñado para acompañar, no para juzgar.</p>
                    </article>

                    <article class="value">
                        <h3>Privacidad</h3>
                        <p>La información emocional debe tratarse con responsabilidad.</p>
                    </article>

                    <article class="value">
                        <h3>Prevención</h3>
                        <p>El seguimiento constante puede ayudar a detectar señales tempranas.</p>
                    </article>

                    <article class="value">
                        <h3>Innovación</h3>
                        <p>Usamos tecnología para fortalecer el cuidado de la salud mental.</p>
                    </article>

                </div>

            </div>
        </section>


        <!-- CTA -->
        <section class="cta">
            <div class="cta__container">

                <h2>
                    Tu bienestar emocional merece acompañamiento continuo.
                </h2>

                <p>
                    Empieza a usar Papatzoa y da el primer paso hacia un seguimiento más organizado de tu salud mental.
                </p>

                <a href="/registro" class="button button--primary">
                    Registrarme ahora
                </a>

            </div>
        </section>

    </main>


    <!-- FOOTER -->
    <footer class="footer">
        <p>
            © 2026 Papatzoa. Plataforma de seguimiento emocional y acompañamiento terapéutico.
            info@papatzoacare.com
        </p>
    </footer>

</body>
</html>