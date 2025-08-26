# Prueba Teórica Online

Este proyecto es una aplicación web sencilla para la gestión y realización de exámenes teóricos en línea, orientada a pruebas técnicas de conocimiento general.

## Características

- Registro de usuarios.
- Inicio de sesión y cierre de sesión.
- Presentación de exámenes teóricos.
- Envío y evaluación automática de respuestas.
- Visualización de resultados.
- Panel de control básico para administración.

## Estructura del Proyecto

- `config/`: Archivos de configuración, como la conexión a la base de datos.
- `db/`: Scripts SQL para la creación y carga de la base de datos del sistema de exámenes.
- `public/`: Archivos PHP principales de la aplicación:
  - `index.php`: Página principal.
  - `register.php`: Registro de usuarios.
  - `dashboard.php`: Panel de usuario.
  - `exam.php`: Presentación del examen.
  - `submit_exam.php`: Envío de respuestas.
  - `results.php`: Resultados del examen.
  - `logout.php`: Cierre de sesión.

## Instalación

1. Clona el repositorio en tu servidor local.
2. Importa el archivo SQL correspondiente desde la carpeta `db/` a tu gestor de base de datos (por ejemplo, MySQL).
3. Configura los parámetros de conexión en `config/database.php`.
4. Accede a la aplicación desde el navegador a través de la carpeta `public/`.

## Requisitos

- PHP 7.x o superior.
- Servidor web (Apache, Nginx, etc.).
- MySQL o MariaDB.

## Notas

- La idea es presentar este proyecto sin ayudas externas.
- Se actualizará más adelante para hacerlo más completo y funcional.

## Licencia

Este proyecto está bajo la licencia MIT.