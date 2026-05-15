# Consultar la base de datos SQLite en Papatzoa

## 1. Entrar al proyecto

Abrir una terminal y posicionarse en la carpeta raíz del proyecto:

```bash
cd /ruta/del/proyecto/papatzoa
```

Ejemplo macOS:

```bash
cd "/Users/diegocovarrubias/Desktop/papatzoa"
```

---

# 2. Entrar a la carpeta database

```bash
cd database
```

---

# 3. Abrir SQLite

```bash
sqlite3 database.sqlite
```

---

# 4. Activar modo legible

Dentro de SQLite ejecutar:

```sql
.headers on
```

```
.mode column
```

Esto mostrará las tablas de manera organizada.

---

# 5. Consultar todos los usuarios

```sql
SELECT
id,
nombre,
apellido,
correo,
terapeuta,
terapeuta_id,
codigo_vinculacion,
motivo_terapia
FROM users;
```

---

# 6. Consultar solo terapeutas

```sql
SELECT
id,
nombre,
apellido,
codigo_vinculacion
FROM users
WHERE terapeuta = 1;
```

---

# 7. Consultar pacientes vinculados

```sql
SELECT
id,
nombre,
apellido,
terapeuta_id,
motivo_terapia
FROM users
WHERE terapeuta = 0;
```

---

# 8. Ver estructura de la tabla users

```sql
PRAGMA table_info(users);
```

---

# 9. Salir de SQLite

```sql
.exit
```

---

# Interpretación de datos

## Columna terapeuta

* 1 = terapeuta
* 0 = paciente

---

## Columna terapeuta_id

* NULL = paciente no vinculado
* número = ID del terapeuta asociado

Ejemplo:

```text
1 | Victor | Frank | terapeuta=1
2 | Harry | Potter | terapeuta_id=1
```

Significa que Harry está vinculado al terapeuta Victor.
