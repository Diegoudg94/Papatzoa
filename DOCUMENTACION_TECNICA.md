# Documentacion tecnica - Papatzoa

## 1. Descripcion general

Papatzoa es una aplicacion web Laravel para seguimiento de salud mental entre pacientes y terapeutas. El sistema permite registro local y con Google, inicio de sesion por rol, vinculacion paciente-terapeuta por PIN, solicitudes de citas, diario emocional, expediente clinico y gestion de datos profesionales del terapeuta.

Stack principal:

- Laravel 13.x
- PHP 8.3
- SQLite en desarrollo local
- Blade
- CSS estatico en `public/css`
- Vite y Tailwind configurados para assets de `resources`, aunque la mayoria de vistas cargan CSS desde `public/css`
- Supabase Auth para autenticacion con Google

## 2. Estructura del proyecto

```text
app/
  Http/Controllers/UsuarioController.php
  Models/
    Cita.php
    DiarioEmocion.php
    NotaSesion.php
    NotaTerapeuta.php
    SeguimientoEmocion.php
    TherapistCredential.php
    User.php
bootstrap/
config/
database/
  migrations/
  seeders/
public/
  css/
  images/
  index.php
resources/
  views/
  css/app.css
  js/app.js
routes/
  web.php
tests/
Dockerfile
composer.json
package.json
vite.config.js
```

Carpetas principales:

- `routes/web.php`: concentra la mayor parte de la logica HTTP mediante closures.
- `app/Http/Controllers/UsuarioController.php`: contiene registro, login local, logout y un metodo legado para generar PIN.
- `app/Models`: modelos Eloquent para usuarios, citas, diario emocional, seguimientos, notas y credenciales profesionales.
- `resources/views`: pantallas Blade de registro, login, dashboard, citas, diario, terapeuta, pacientes, expediente y callbacks Google.
- `public/css`: hojas de estilo usadas directamente por las vistas actuales.
- `database/migrations`: estructura de tablas y cambios incrementales.
- `storage/app/public`: fotos de perfil subidas por terapeutas.
- `storage/app/private`: documentos profesionales subidos como credenciales.

## 3. Dependencias

Backend (`composer.json`):

- `php`: `^8.3`
- `laravel/framework`: `^13.7`
- `laravel/tinker`: `^3.0`

Dependencias de desarrollo relevantes:

- `phpunit/phpunit`
- `laravel/pint`
- `laravel/pail`
- `fakerphp/faker`
- `mockery/mockery`

Frontend (`package.json`):

- `vite`
- `laravel-vite-plugin`
- `tailwindcss`
- `@tailwindcss/vite`
- `concurrently`

## 4. Configuracion local

Requisitos:

- PHP 8.3 o superior
- Composer
- Node.js y npm
- Extension SQLite/PDO SQLite habilitada

Instalacion:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
```

Variables relevantes de entorno:

```env
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/absoluta/al/proyecto/database/database.sqlite
SUPABASE_URL=https://tu-proyecto.supabase.co
SUPABASE_ANON_KEY=tu_anon_key
FILESYSTEM_DISK=local
```

Ejecucion local:

```bash
php artisan serve
```

Assets con Vite:

```bash
npm run dev
```

Comando combinado:

```bash
composer run dev
```

Ese comando levanta servidor Laravel, cola, logs con Pail y Vite mediante `concurrently`.

## 5. Docker

El proyecto incluye un `Dockerfile` basado en `php:8.3-apache`.

El contenedor:

- Instala dependencias del sistema para SQLite.
- Copia Composer desde la imagen oficial.
- Ejecuta `composer install`.
- Copia `.env.example` a `.env`.
- Crea `database/database.sqlite`.
- Genera `APP_KEY`.
- Ejecuta migraciones.
- Ajusta permisos de `storage`, `bootstrap/cache` y `database`.
- Configura Apache para servir desde `public`.

Construccion y ejecucion:

```bash
docker build -t papatzoa .
docker run --rm -p 8080:80 papatzoa
```

## 6. Modelo de datos

### `users`

Tabla base creada por `0001_01_01_000000_create_users_table.php` y extendida por migraciones posteriores.

| Campo | Tipo | Descripcion |
| --- | --- | --- |
| `id` | integer | Identificador del usuario |
| `supabase_id` | string nullable unique | ID del usuario en Supabase Auth |
| `nombre` | string | Nombre del usuario |
| `apellido` | string nullable | Apellido, requerido solo en registro local |
| `sexo` | string nullable | Sexo seleccionado |
| `edad` | integer nullable | Edad |
| `correo` | string unique | Correo de acceso |
| `password` | string nullable | Password hasheado; nullable para usuarios Google |
| `avatar_url` | string nullable | Avatar recibido desde Google |
| `auth_provider` | string nullable | `local` o `google` |
| `terapeuta` | boolean | `1` terapeuta, `0` paciente |
| `terapeuta_verificado` | boolean | Estado de verificacion profesional |
| `estado_verificacion` | string | `no_aplica`, `no_enviada`, `pendiente` u otros estados futuros |
| `codigo_vinculacion` | string nullable | PIN generado por terapeuta |
| `codigo_expira_en` | timestamp nullable | Expiracion del PIN |
| `terapeuta_id` | unsignedBigInteger nullable | Terapeuta vinculado al paciente |
| `motivo_terapia` | text nullable | Motivo inicial cifrado con `Crypt` |
| `telefono_lada` | string nullable | Lada telefonica |
| `telefono` | string nullable | Telefono normalizado a 10 digitos cuando aplica |
| `nacionalidad` | string nullable | Nacionalidad del terapeuta |
| `especialidad` | string nullable | Especialidad profesional |
| `biografia` | text nullable | Perfil profesional |
| `experiencia_anios` | integer nullable | Anios de experiencia |
| `cedula_profesional` | string nullable | Cedula profesional |
| `institucion_formacion` | string nullable | Institucion de formacion |
| `enfoque_terapeutico` | string nullable | Enfoque terapeutico |
| `modalidad_atencion` | string nullable | `presencial`, `online` o `hibrida` |
| `profile_photo_path` | string nullable | Foto subida al disco `public` |
| `pais_atencion` | string nullable | Pais para atencion presencial/hibrida |
| `estado_atencion` | string nullable | Estado para atencion presencial/hibrida |
| `ciudad_atencion` | string nullable | Ciudad para atencion presencial/hibrida |
| `direccion_atencion` | string nullable | Direccion de atencion |
| `codigo_postal_atencion` | string nullable | Codigo postal |

Relaciones en `User`:

- `diarioEmociones()`
- `citasComoPaciente()`
- `notasComoPaciente()`
- `pacientesAsignados()`
- `therapistCredentials()`

### `citas`

| Campo | Tipo | Descripcion |
| --- | --- | --- |
| `id` | integer | Identificador de la cita |
| `paciente_id` | unsignedBigInteger | Usuario paciente |
| `terapeuta_id` | unsignedBigInteger nullable | Usuario terapeuta |
| `fecha` | date | Fecha solicitada |
| `hora` | time | Hora tentativa |
| `motivo` | text nullable | Campo legado |
| `motivo_encrypted` | text nullable | Motivo cifrado activo |
| `estado` | string | `pendiente`, `aceptada` o `rechazada` |
| `comentario_terapeuta` | text nullable | Comentario de rechazo cifrado |
| `created_at` | timestamp | Fecha de creacion |
| `updated_at` | timestamp | Fecha de actualizacion |

### `diario_emociones`

Registros de diario emocional del paciente.

| Campo | Tipo | Descripcion |
| --- | --- | --- |
| `id` | integer | Identificador |
| `user_id` | foreignId | Paciente propietario |
| `emocion` | string nullable | Emocion principal, no cifrada para listar rapido |
| `intensidad` | integer nullable | Intensidad de 1 a 10 |
| `situacion_encrypted` | text nullable | Situacion cifrada |
| `pensamiento_encrypted` | text nullable | Pensamiento cifrado |
| `conducta_encrypted` | text nullable | Conducta cifrada |
| `interpretacion_encrypted` | text nullable | Interpretacion cifrada |
| `reestructuracion_encrypted` | text nullable | Reestructuracion cifrada |

### `seguimientos_emocion`

Seguimientos asociados a una entrada de diario.

| Campo | Tipo | Descripcion |
| --- | --- | --- |
| `id` | integer | Identificador |
| `diario_emocion_id` | foreignId | Entrada del diario |
| `user_id` | foreignId | Paciente propietario |
| `nota_encrypted` | text | Nota cifrada |

### `notas_terapeuta`

Notas generales del terapeuta sobre un paciente.

| Campo | Tipo | Descripcion |
| --- | --- | --- |
| `id` | integer | Identificador |
| `paciente_id` | foreignId | Paciente |
| `terapeuta_id` | foreignId | Terapeuta |
| `nota_encrypted` | text | Nota cifrada |

### `notas_sesion`

Notas de sesion asociadas a una cita.

| Campo | Tipo | Descripcion |
| --- | --- | --- |
| `id` | integer | Identificador |
| `cita_id` | foreignId | Cita asociada |
| `paciente_id` | foreignId | Paciente |
| `terapeuta_id` | foreignId | Terapeuta |
| `nota_encrypted` | text | Nota cifrada |

### `therapist_credentials`

Documentos subidos por terapeutas para verificacion profesional.

| Campo | Tipo | Descripcion |
| --- | --- | --- |
| `id` | integer | Identificador |
| `terapeuta_id` | foreignId | Terapeuta propietario |
| `tipo_documento` | string | Tipo de documento |
| `archivo_path` | string | Ruta en storage |
| `nombre_original` | string nullable | Nombre original del archivo |
| `estado` | string | Estado de revision, por defecto `pendiente` |
| `comentario_revision` | text nullable | Comentario administrativo futuro |

## 7. Autenticacion y sesiones

La autenticacion local es manual y vive en `UsuarioController`. No se usa el guard `Auth` de Laravel para las rutas actuales.

Variables de sesion principales:

- `usuario_id`
- `usuario_nombre`
- `usuario_apellido`
- `usuario_correo`
- `usuario_terapeuta`
- `google_registro` durante el alta incompleta con Google

### Registro local

Rutas:

```text
GET  /registro
POST /registro
```

Validaciones principales:

- `nombre`, `apellido`, `sexo`, `edad`, `correo`.
- `password` confirmado, minimo 8 caracteres, una mayuscula, un numero y un simbolo de `@$!%*#?&_-`.

El password se guarda con `Hash::make`. El rol se define por el checkbox `terapeuta`.

### Registro con Google

Rutas:

```text
GET  /registro/google
GET  /registro/google/callback
POST /registro/google/callback
GET  /completar-registro-google
POST /completar-registro-google
```

Flujo:

1. Laravel redirige a Supabase Auth con provider `google`.
2. La vista `registro-google-callback` recibe la sesion de Supabase en frontend.
3. El callback POST valida `supabase_id`, `nombre`, `correo` y `avatar_url`.
4. Si el correo ya existe, actualiza campos Google y crea sesion.
5. Si no existe, guarda `google_registro` en sesion y redirige a completar perfil.
6. `/completar-registro-google` pide edad, sexo y rol.
7. El usuario Google se crea con `password = null`.

### Login local

Rutas:

```text
GET  /login
POST /login
```

Proceso:

1. Valida `email` y `password`.
2. Busca usuario por `users.correo`.
3. Verifica con `Hash::check`.
4. Regenera sesion.
5. Redirige segun rol: terapeuta a `/terapeuta`, paciente a `/dashboard`.

### Login con Google

Rutas:

```text
GET  /login/google/callback
POST /login/google/validar
```

El login valida que exista un usuario por `correo` o `supabase_id`. Si no existe, responde con redireccion a `/registro` y mensaje de error en sesion. Si existe, actualiza datos Google y crea sesion local.

### Logout

```text
GET /logout
```

Ejecuta `session()->flush()` y redirige a `/login`.

## 8. Rutas principales

| Metodo | Ruta | Funcion |
| --- | --- | --- |
| GET | `/` | Landing page |
| GET | `/registro` | Formulario de registro local |
| POST | `/registro` | Crear usuario local |
| GET | `/registro/google` | Iniciar registro Google con Supabase |
| GET | `/registro/google/callback` | Vista callback de registro Google |
| POST | `/registro/google/callback` | Validar datos Google e iniciar/crear sesion |
| GET | `/completar-registro-google` | Completar perfil de usuario Google nuevo |
| POST | `/completar-registro-google` | Crear usuario Google nuevo |
| GET | `/login` | Formulario de login local/Google |
| POST | `/login` | Iniciar sesion local |
| GET | `/login/google/callback` | Vista callback de login Google |
| POST | `/login/google/validar` | Validar usuario Google existente |
| GET | `/logout` | Cerrar sesion |
| GET | `/dashboard` | Dashboard del paciente |
| GET | `/citas` | Listar citas del paciente |
| POST | `/citas/solicitar` | Solicitar cita |
| GET | `/diario` | Listar diario emocional |
| POST | `/diario` | Crear entrada de diario |
| POST | `/diario/{id}/seguimiento` | Agregar seguimiento a una emocion |
| GET | `/ayuda` | Recursos de ayuda |
| GET | `/terapeuta` | Panel principal del terapeuta |
| GET | `/terapeuta/mis-datos` | Perfil profesional y credenciales |
| POST | `/terapeuta/mis-datos` | Actualizar perfil profesional |
| POST | `/terapeuta/mis-datos/foto` | Subir foto de perfil |
| POST | `/terapeuta/mis-datos/foto-google` | Usar avatar de Google en lugar de foto subida |
| POST | `/terapeuta/mis-datos/credenciales` | Subir documento profesional |
| GET | `/terapeuta/mis-datos/credenciales/{id}/ver` | Ver credencial propia |
| DELETE | `/terapeuta/mis-datos/credenciales/{id}` | Eliminar credencial propia |
| GET | `/confirmar` | Solicitudes pendientes del terapeuta |
| POST | `/citas/{id}/aceptar` | Aceptar cita |
| POST | `/citas/{id}/rechazar` | Rechazar cita |
| GET | `/pacientes` | Lista de pacientes vinculados |
| GET | `/expediente/{id}` | Expediente del paciente vinculado |
| POST | `/expediente/{id}/notas` | Crear nota general del terapeuta |
| POST | `/expediente/{pacienteId}/citas/{citaId}/nota` | Crear nota de sesion |
| PUT | `/expediente/{pacienteId}/citas/{citaId}/nota/{notaId}` | Editar nota de sesion |
| DELETE | `/expediente/{pacienteId}/citas/{citaId}/nota/{notaId}` | Eliminar nota de sesion |
| POST | `/generar-pin` | Generar PIN de vinculacion |
| POST | `/vincular-terapeuta` | Vincular paciente con terapeuta |

## 9. Flujos funcionales

### Vinculacion terapeuta-paciente

1. El terapeuta autenticado genera un PIN con `POST /generar-pin`.
2. El PIN se guarda en `users.codigo_vinculacion`.
3. La expiracion se guarda en `users.codigo_expira_en` con 90 dias de vigencia.
4. Si el terapeuta ya tiene PIN activo, no se genera otro.
5. El paciente ingresa PIN y motivo en `/dashboard`.
6. El sistema busca un usuario terapeuta con ese PIN.
7. Si el PIN existe y no expiro, actualiza al paciente con `terapeuta_id` y `motivo_terapia` cifrado.

### Solicitud y confirmacion de citas

1. El paciente solicita cita en `/citas` con fecha, hora y motivo.
2. El backend exige que el paciente tenga `terapeuta_id`.
3. La cita se crea con `estado = pendiente` y `motivo_encrypted`.
4. El terapeuta ve pendientes en `/confirmar`.
5. Al aceptar, `estado` cambia a `aceptada`.
6. Al rechazar, `estado` cambia a `rechazada` y puede guardar `comentario_terapeuta` cifrado.

### Diario emocional

1. El paciente consulta `/diario`.
2. El backend lista sus emociones y seguimientos.
3. Los campos sensibles se descifran para renderizar.
4. Al crear una emocion, se guarda `emocion` e `intensidad` como datos visibles y el resto cifrado.
5. Los seguimientos se validan por propiedad del usuario antes de guardarse.

### Expediente clinico

1. El terapeuta abre `/expediente/{id}`.
2. La ruta exige sesion, rol terapeuta y que el paciente este vinculado con ese terapeuta.
3. El expediente muestra datos reales de:
   - paciente
   - emociones del diario
   - citas
   - notas generales del terapeuta
   - notas de sesion
4. Las notas se guardan cifradas.
5. Las notas de sesion se pueden crear, editar y eliminar si pertenecen al terapeuta y paciente correctos.

### Perfil profesional del terapeuta

1. El terapeuta entra a `/terapeuta/mis-datos`.
2. Puede actualizar telefono, nacionalidad, especialidad, biografia, cedula, formacion, enfoque y modalidad.
3. Para modalidad `presencial` o `hibrida`, la ubicacion de atencion es requerida.
4. Para modalidad `online`, los campos de ubicacion se limpian.
5. El telefono se normaliza a digitos y debe tener 10 digitos si se proporciona.
6. Puede subir foto al disco `public` o volver a usar avatar de Google.
7. Puede subir credenciales profesionales al disco local/private; al hacerlo queda `estado_verificacion = pendiente`.

## 10. Vistas Blade

| Vista | Proposito |
| --- | --- |
| `welcome.blade.php` | Pagina principal |
| `registro.blade.php` | Registro local e inicio de registro Google |
| `registro-google-callback.blade.php` | Callback frontend de registro Google |
| `completar-registro-google.blade.php` | Completar perfil para usuario Google nuevo |
| `login.blade.php` | Login local y acceso Google |
| `login-google-callback.blade.php` | Callback frontend de login Google |
| `dashboard.blade.php` | Panel del paciente y datos del terapeuta vinculado |
| `citas.blade.php` | Consulta y solicitud de citas |
| `diario.blade.php` | Diario emocional persistido |
| `ayuda.blade.php` | Recursos de ayuda |
| `terapeuta.blade.php` | Panel del terapeuta |
| `terapeuta-mis-datos.blade.php` | Perfil profesional y credenciales |
| `confirmar.blade.php` | Confirmacion/rechazo de citas |
| `pacientes.blade.php` | Pacientes vinculados |
| `expediente.blade.php` | Expediente clinico del paciente |

## 11. Seguridad y privacidad

Medidas implementadas:

- Passwords hasheados con `Hash::make`.
- Verificacion de passwords con `Hash::check`.
- Regeneracion de sesion al iniciar sesion.
- CSRF en formularios Blade con `@csrf`.
- Cifrado con `Crypt::encryptString` para motivo de terapia, motivo de cita, comentario de terapeuta, diario emocional y notas.
- Validaciones de propiedad en expediente, notas de sesion y credenciales.
- Documentos de credenciales almacenados por defecto en disco local no publico.
- Fotos de perfil almacenadas en disco `public`.

Limitaciones y riesgos actuales:

- No se usa middleware `auth` ni guards nativos de Laravel; la proteccion depende de checks manuales de `session('usuario_id')`.
- Algunas rutas validan sesion pero no siempre verifican rol con la misma consistencia.
- Los estados (`pendiente`, `aceptada`, `rechazada`) son strings libres, sin enum ni constraint.
- El PIN es numerico de 6 digitos y no verifica colisiones entre terapeutas.
- `users.terapeuta_id` no tiene foreign key declarada.
- La migracion `2026_05_12_211041_add_codigo_expira_en_to_users_table.php` no elimina la columna en `down`.
- Hay dos migraciones que crean `therapist_credentials`: `2026_07_06_210100_create_therapist_credentials_table.php` y `2026_07_07_001350_create_therapist_credentials_table.php`. En una base limpia, la segunda puede fallar porque la tabla ya existe.
- `2026_07_05_151000_make_password_nullable_for_google_users.php` usa SQL `ALTER COLUMN`, que puede no ser portable en SQLite.
- `DatabaseSeeder` usa campos `name` y `email`, pero la tabla actual usa `nombre` y `correo`.
- El modelo `User` solo declara fillable para `nombre`, `correo`, `password` y `terapeuta`; muchas actualizaciones usan Query Builder para evitar asignacion masiva.

## 12. Pruebas

El proyecto incluye PHPUnit en `phpunit.xml`.

Comando:

```bash
composer test
```

Configuracion de testing:

- `APP_ENV=testing`
- SQLite en memoria: `DB_DATABASE=:memory:`
- Sesiones en array: `SESSION_DRIVER=array`
- Colas sincronas: `QUEUE_CONNECTION=sync`

Pruebas existentes:

- `tests/Feature/ExampleTest.php`
- `tests/Unit/ExampleTest.php`

No hay pruebas especificas para registro, login, Supabase/Google, roles, vinculacion por PIN, citas, diario, expediente o credenciales.

## 13. Observaciones tecnicas

- La logica de negocio esta concentrada en closures dentro de `routes/web.php`; conviene moverla a controladores dedicados.
- Hay mezcla de Query Builder y Eloquent.
- El cifrado se maneja manualmente en cada ruta; convendria encapsularlo en casts, accessors o servicios.
- Vite/Tailwind estan configurados, pero las pantallas principales usan CSS desde `public/css`.
- `storage:link` es necesario para servir fotos subidas al disco `public`.
- Los documentos de credenciales se guardan con `store('therapist_credentials')`, por lo que van al disco por defecto (`local`) salvo configuracion distinta.
- `config/filesystems.php` define el disco `local` en `storage/app/private` y el disco `public` en `storage/app/public`.
- La ruta de ver credenciales busca primero en `public` y luego en `local`, aunque la subida actual usa el disco por defecto.

## 14. Recomendaciones de mejora

Prioridad alta:

- Eliminar o fusionar la migracion duplicada de `therapist_credentials`.
- Rehacer la migracion que hace nullable `password` usando una estrategia compatible con SQLite o con el motor objetivo.
- Proteger rutas con middleware de autenticacion y middleware/policies por rol.
- Agregar policies para expediente, citas, diario, notas y credenciales.
- Agregar foreign keys o indices consistentes para `users.terapeuta_id`, `citas.paciente_id` y `citas.terapeuta_id`.
- Corregir `DatabaseSeeder` para usar `nombre`, `correo`, `password` y `terapeuta`.

Prioridad media:

- Mover closures de `routes/web.php` a controladores.
- Crear constantes o enums para estados de cita y verificacion.
- Crear servicios para cifrado/descifrado de datos clinicos.
- Agregar indices para `users.codigo_vinculacion`, `citas.estado`, `therapist_credentials.terapeuta_id` y tablas de notas.
- Reconstruir en login cualquier estado derivado desde base de datos, evitando depender de variables de sesion no persistidas.

Prioridad baja:

- Unificar estilos en `public/css` o migrar progresivamente a Vite/Tailwind.
- Reducir CSS inline en vistas.
- Normalizar uso de Eloquent vs Query Builder.
- Agregar factories y seeders realistas para pacientes, terapeutas, citas y diarios.

## 15. Comandos utiles

```bash
# Instalar dependencias PHP
composer install

# Instalar dependencias frontend
npm install

# Generar APP_KEY
php artisan key:generate

# Ejecutar migraciones
php artisan migrate

# Crear symlink para archivos publicos
php artisan storage:link

# Levantar servidor local
php artisan serve

# Ejecutar Vite
npm run dev

# Compilar assets
npm run build

# Ejecutar pruebas
composer test

# Formatear codigo PHP
vendor/bin/pint
```
