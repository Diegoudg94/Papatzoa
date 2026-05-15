<!DOCTYPE html>
<html lang="es">

<head>

  <!-- ===================================== -->
  <!-- CONFIGURACIÓN BÁSICA -->
  <!-- ===================================== -->

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mis pacientes | Papatzoa</title>

  <!-- CSS principal -->
  <link rel="stylesheet" href="{{ asset('css/terapeuta.css') }}" />

</head>

<body>

  <!-- ===================================== -->
  <!-- HEADER -->
  <!-- ===================================== -->

  <header class="header">
    <div class="header__container">

      <!-- Logo / título -->
      <a class="brand" href="/terapeuta">
        <span class="brand__icon" aria-hidden="true">✳︎</span>
        <span class="brand__title">Panel del terapeuta</span>
      </a>

      <!-- Navegación -->
      <nav class="header__center" aria-label="Navegación principal">
        <a class="header__link" href="/terapeuta">Inicio</a>
        <a class="header__link" href="/pacientes">Mis pacientes</a>
      </nav>

      <!-- Acciones usuario -->
      <nav class="header__actions" aria-label="Acciones de usuario">
        <a class="header__button" href="#">Mi cuenta</a>
      </nav>

    </div>
  </header>


  <!-- ===================================== -->
  <!-- CONTENIDO PRINCIPAL -->
  <!-- ===================================== -->

  <main class="main">
    <div class="main__container">

      <!-- Hero decorativo -->
      <section class="hero"></section>

      <!-- ===================================== -->
      <!-- SECCIÓN PACIENTES -->
      <!-- ===================================== -->

      <section class="content">

        <h1 class="title">Mis pacientes</h1>

        <p class="card__description" style="margin-bottom: 12px;">
          Pacientes vinculados actualmente a tu panel clínico.
        </p>


        <!-- ===================================== -->
        <!-- BUSCADOR -->
        <!-- ===================================== -->

        <article class="card" style="margin-bottom: 14px;">
          <div class="form__group" style="max-width: 420px;">
            <label class="form__label" for="buscador">Buscar paciente</label>
            <input class="form__input" id="buscador" type="text" placeholder="Ej: María, Carlos..." />
          </div>
        </article>


        <!-- ===================================== -->
        <!-- TABLA DINÁMICA -->
        <!-- ===================================== -->

        <article class="card">
          <div class="table-wrap">

            <table class="table" aria-label="Tabla de pacientes">

              <!-- ENCABEZADOS -->
              <thead>
                <tr>
                  <th>Paciente</th>
                  <th>Fecha de Inicio</th>
                  <th>Próxima Cita</th>
                  <th>Motivo de consulta</th>
                  <th>Estado</th>
                </tr>
              </thead>

              <!-- CUERPO DINÁMICO (Backend) -->
              <tbody id="tablaPacientes">

                @forelse ($pacientes as $paciente)
                <tr>
                  <!-- Nombre -->
                  <td data-label="Paciente">
                    <a
    class="table__link"
    href="/expediente/{{ $paciente->id }}"
>
    {{ $paciente->nombre }} {{ $paciente->apellido }}
</a>
                  </td>

                  <!-- Fecha inicio -->
                  <td data-label="Inicio">
                    {{ $paciente->updated_at ? \Carbon\Carbon::parse($paciente->updated_at)->format('d/m/Y') : '—' }}
                  </td>

                  <!-- Próxima cita (Ejemplo de lógica para relación) -->
                  <td data-label="Próxima cita">
                    @if(isset($paciente->proxima_cita))
                      {{ \Carbon\Carbon::parse($paciente->proxima_cita)->format('d/m/Y — H:i') }}
                    @else
                      —
                    @endif
                  </td>

                  <!-- Motivo -->
                  <td data-label="Razón">
                    @php
                        $motivo = $paciente->motivo_terapia;
                        if ($motivo) {
                            try {
                                $motivo = Illuminate\Support\Facades\Crypt::decryptString($motivo);
                            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                                // Si no está encriptado (registros manuales o viejos), se deja el texto original
                            }
                        }
                    @endphp
                    {{ $motivo ?: 'Sin especificar' }}
                  </td>

                  <!-- Estado -->
                  <td data-label="Estado">
                    <span class="score score--mid">
                      Activo
                    </span>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" style="text-align: center; padding: 2rem; color: #6b7280;">
                    Aún no tienes pacientes vinculados a tu cuenta.
                  </td>
                </tr>
                @endforelse

              </tbody>

            </table>

          </div>
        </article>

      </section>

    </div>
  </main>


  <!-- ===================================== -->
  <!-- SCRIPT BUSCADOR -->
  <!-- ===================================== -->

  <script>
    /*
      Buscador simple frontend:
      Filtra las filas de la tabla comparando el texto 
      del input con el contenido de cada fila.
    */
    const buscador = document.getElementById('buscador');
    
    // Función para obtener las filas actuales (incluyendo las generadas por Blade)
    const obtenerFilas = () => Array.from(document.querySelectorAll('#tablaPacientes tr'));

    buscador.addEventListener('input', () => {
      const q = buscador.value.toLowerCase().trim();

      obtenerFilas().forEach(tr => {
        const textoFila = tr.innerText.toLowerCase();
        
        // Si no hay texto o coincide, mostramos; si no, ocultamos.
        tr.style.display = textoFila.includes(q) ? '' : 'none';
      });
    });
  </script>

</body>

</html>