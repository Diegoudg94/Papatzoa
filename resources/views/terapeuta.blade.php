@php
    // Nota: Lo ideal es pasar $terapeuta desde el Controlador, 
    // pero mantenemos tu lógica funcional.
    $usuarioId = session('usuario_id');
    $terapeuta = $usuarioId
        ? \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $usuarioId)
            ->first()
        : null;
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel del terapeuta</title>
    <link rel="stylesheet" href="{{ asset('css/terapeuta.css') }}" />
</head>

<body>

    <!-- ===================================== -->
    <!-- HEADER -->
    <!-- ===================================== -->
    <header class="header">
        <div class="header__container">
            <!-- Logo -->
            <a class="brand" href="/terapeuta">
                <span class="brand__icon" aria-hidden="true">✳︎</span>
                <span class="brand__title">Panel del terapeuta</span>
            </a>

            <!-- Navegación -->
            <nav class="header__center" aria-label="Navegación principal">
                <a class="header__link" href="/terapeuta">Inicio</a>
                <a class="header__link" href="/pacientes">Pacientes</a>
            </nav>

            <!-- Dropdown cuenta -->
            <nav class="header__actions" aria-label="Acciones de usuario">
                <div class="dropdown">
                    <button class="header__button" id="menuButton" type="button">
                        Mi cuenta
                    </button>
                    <div class="dropdown__menu" id="dropdownMenu">
                        <a class="dropdown__item" href="/terapeuta/mis-datos">Mis datos</a>
                        <a class="dropdown__item" href="/logout">Cerrar sesión</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- ===================================== -->
    <!-- MAIN -->
    <!-- ===================================== -->
    <main class="main">
        <div class="main__container">

            <!-- HERO -->
            <section class="hero">
                <div class="hero__content">
                    <h1 class="hero__title">
                        Hola, {{ session('usuario_nombre') }} 👋
                    </h1>
                    <p class="hero__text">
                        Bienvenido al panel clínico de Papatzoa.
                    </p>
                </div>
            </section>

            <!-- PRÓXIMAS CITAS -->
            <section class="content">
                <h2 class="title">Próximas citas</h2>
                <article class="card clinical-table-card">
                    <p class="card__description" style="margin-bottom: 12px;">
                        Aquí verás las próximas citas agendadas por tus pacientes.
                    </p>

                    <div class="table-wrap clinical-table-wrap">
                        <table class="table clinical-table clinical-table--appointments" aria-label="Tabla de próximas citas">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th class="cell-center">Fecha</th>
                                    <th class="cell-center">Hora</th>
                                    <th>Motivo / descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($proximasCitas as $cita)
                                    <tr>
                                        <td class="cell-patient" data-label="Paciente">
                                            <a class="table__link" href="/expediente/{{ $cita->paciente_id }}">
                                                {{ $cita->nombre }} {{ $cita->apellido }}
                                            </a>
                                        </td>

                                        <td class="cell-center cell-nowrap" data-label="Fecha">
                                            {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                        </td>

                                        <td class="cell-center cell-nowrap" data-label="Hora">
                                            {{ \Carbon\Carbon::parse($cita->hora)->format('H:i') }}
                                        </td>

                                        <td class="cell-text" data-label="Motivo">
                                            @php
                                                $motivoCifrado = $cita->motivo_encrypted ?? $cita->motivo ?? null;
                                                try {
                                                    echo $motivoCifrado ? \Illuminate\Support\Facades\Crypt::decryptString($motivoCifrado) : 'Sin registro';
                                                } catch (\Exception $e) {
                                                    echo 'No se pudo mostrar este dato.';
                                                }
                                            @endphp
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="table-empty-state" colspan="4">
                                            No tienes próximas citas confirmadas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>

            <!-- PENDIENTES -->
            <section class="content">
                <h2 class="title">Citas pendientes por confirmar</h2>

                <article class="card">
                    <p class="card__description">
                        Tienes <strong>{{ $pendientesCount }}</strong>
                        {{ $pendientesCount == 1 ? 'cita pendiente' : 'citas pendientes' }} por confirmar.

                        @if ($pendientesCount > 0)
                            <a class="table__link" href="/confirmar">
                                Ir a confirmar
                            </a>
                        @endif
                    </p>
                </article>
            </section>

            <!-- INVITAR PACIENTE -->
            <section class="content">
                <h2 class="title">Invitar paciente</h2>
                <article class="card">
                    <p class="card__description" style="margin-bottom: 20px;">
                        Genera un código para vincular pacientes a tu cuenta.
                    </p>

                    @if ($terapeuta && $terapeuta->codigo_vinculacion)
                        <div class="pin-box">
                            <h3>PIN de vinculación</h3>
                            <div class="pin-code">
                                {{ $terapeuta->codigo_vinculacion }}
                            </div>
                            @if ($terapeuta->codigo_expira_en)
                                <p class="pin-expiration">
                                Expira: {{ \Carbon\Carbon::parse($terapeuta->codigo_expira_en)->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if (

    !$terapeuta ||
    !$terapeuta->codigo_vinculacion ||
    (
        $terapeuta->codigo_expira_en &&
        now()->gt($terapeuta->codigo_expira_en)
    )

)

<form action="/generar-pin" method="POST">

    @csrf

    <button
        class="header__button"
        type="submit"
    >
        Generar PIN
    </button>

</form>

@else

<div class="pin-active">

    <strong>
        PIN activo actualmente
    </strong>

    <p>

        No puedes generar otro PIN
        hasta que expire el actual.

    </p>

</div>

@endif
                </article>
            </section>

        </div>
    </main>

    <!-- SCRIPT DROPDOWN -->
    <script>
        const menuButton = document.getElementById('menuButton');
        const dropdownMenu = document.getElementById('dropdownMenu');

        menuButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('show');
        });

        window.addEventListener('click', (e) => {
            if (!menuButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    </script>

@include('partials.marker-widget')
</body>
</html>
