# Papatzoa Care

Sistema web enfocado en la gestión y seguimiento de información en el área de la salud mental, permitiendo la comunicación y organización de datos entre pacientes y terapeutas.

---

# Integrantes del equipo

- Deomiro Contreras Martínez
- Diego Covarrubias Ladrón de Guevara
- Elvia Edith Sánchez Santillán

---

# Descripción del proyecto

Papatzoa Care es una plataforma web desarrollada como parte del proyecto académico de Proyecto VI en UDGVirtual.

El sistema tiene como objetivo centralizar la información clínica y administrativa relacionada con pacientes y terapeutas, facilitando:
- gestión de expedientes
- seguimiento emocional
- administración de citas
- acceso rápido a información
- continuidad del tratamiento
- comunicación entre usuarios

La plataforma busca mejorar la organización de la información clínica y disminuir problemas derivados de expedientes incompletos, dispersos o inaccesibles.

---

# Problemática

Actualmente muchas clínicas y profesionales de salud mental manejan información en distintos medios:
- notas físicas
- documentos separados
- registros no centralizados

Esto provoca:
- pérdida de información
- retrasos en atención
- expedientes incompletos
- dificultades cuando cambia el terapeuta
- problemas en situaciones de emergencia

Papatzoa Care busca solucionar esta problemática mediante una plataforma web centralizada y accesible.

---

# Objetivo general

Desarrollar una plataforma web que permita centralizar y administrar información relacionada con pacientes y terapeutas, facilitando el seguimiento clínico y mejorando la disponibilidad de los expedientes.

---

# Tecnologías utilizadas

## Frontend
- HTML5
- CSS3

## Backend
- PHP
- Laravel

## Base de datos
- SQLite

## Herramientas de desarrollo
- Visual Studio Code
- Git
- GitHub
- Trello

---

# Arquitectura del sistema

El sistema fue desarrollado utilizando el patrón MVC (Modelo - Vista - Controlador) implementado mediante Laravel.

Esta arquitectura permite:
- separar responsabilidades
- organizar el código
- facilitar mantenimiento
- escalar funcionalidades futuras

---

# Estructura principal del proyecto

```plaintext
app/               -> Lógica del sistema
app/Http/Controllers -> Controladores
app/Models/        -> Modelos de base de datos

resources/views/   -> Vistas Blade del frontend

public/css/        -> Archivos CSS
public/images/     -> Imágenes y recursos gráficos

routes/            -> Definición de rutas

database/          -> Migraciones y base de datos SQLite
