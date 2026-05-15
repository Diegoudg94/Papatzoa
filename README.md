# Papatzoa

Plataforma web enfocada en el monitoreo y seguimiento de salud mental entre pacientes y terapeutas.

---

## Tecnologías utilizadas

- Laravel
- PHP
- SQLite
- Blade
- HTML
- CSS
- GitHub

---

## Funcionalidades implementadas

### Sistema de autenticación
- Registro de usuarios
- Inicio de sesión
- Roles de paciente y terapeuta
- Control de sesiones

### Dashboard dinámico
- Vista personalizada para pacientes
- Vista personalizada para terapeutas
- Navegación dinámica según el rol

### Vinculación terapeuta-paciente
- Generación de PIN de vinculación
- Asociación automática entre paciente y terapeuta
- Visualización de pacientes vinculados

### Expediente clínico
- Visualización dinámica de expedientes
- Motivo de consulta
- Historial emocional
- Información personalizada por paciente

### Seguridad
- Validaciones de formularios
- Protección de sesiones
- Cifrado de información sensible

---

## Estructura principal del proyecto

app/                -> Lógica principal Laravel  
resources/views/    -> Vistas Blade  
public/css/         -> Estilos CSS  
public/images/      -> Imágenes y recursos gráficos  
routes/             -> Definición de rutas  
database/           -> Migraciones y base de datos SQLite  

---

## Instalación local

```bash
composer install
php artisan migrate
php artisan serve