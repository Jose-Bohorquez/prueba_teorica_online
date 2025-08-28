# Prueba Teórica Online

Este proyecto es una aplicación web completa para la gestión y realización de exámenes teóricos en línea, orientada a pruebas técnicas de conocimiento general con funcionalidades avanzadas de administración y control.

## 🚀 Características Principales

### 👥 **Sistema de Usuarios**
- Registro e inicio de sesión seguro
- Roles diferenciados (Estudiante/Administrador)
- Gestión de perfiles de usuario
- Sistema de rehabilitación para usuarios anulados

### 📝 **Sistema de Exámenes**
- Presentación de exámenes con temporizador flotante
- Soporte para preguntas de opción múltiple y abiertas
- Evaluación automática de respuestas
- Prevención de re-intentos después de anulación
- Sistema de anulación automática por comportamiento sospechoso

### 📊 **Dashboard de Resultados Avanzado**
- Visualización completa de todas las respuestas (correctas e incorrectas)
- Indicadores visuales de rendimiento
- Estadísticas detalladas de participación y aprobación
- Cálculo transparente de porcentajes
- Clasificación por niveles de rendimiento (Excelente/Aprobado/Reprobado)

### 🛠️ **Panel de Administración**
- Gestión completa de usuarios
- Administración de preguntas y opciones
- Estadísticas generales del sistema
- Rehabilitación de usuarios anulados
- Importación masiva de preguntas
- Control de acceso y permisos

## 📁 Estructura del Proyecto

### `config/`
- `database.php`: Configuración de conexión a la base de datos
- `add_exam_status_column.sql`: Script para agregar columnas de estado de examen

### `db/`
- `exam_system.sql`: Script principal de creación de la base de datos
- Tablas: `users`, `questions`, `options`, `user_answers`

### `public/` - Archivos PHP principales

#### **🏠 Páginas Principales**
- `index.php`: Página de inicio y login
- `register.php`: Registro de nuevos usuarios
- `dashboard.php`: Panel principal del usuario
- `logout.php`: Cierre de sesión

#### **📝 Sistema de Exámenes**
- `exam.php`: Presentación del examen con temporizador
- `submit_exam.php`: Procesamiento de respuestas
- `get_questions.php`: API para obtener preguntas
- `check_exam_status.php`: Verificación de estado del examen

#### **📊 Resultados y Reportes**
- `results.php`: Dashboard avanzado de resultados
- `get_all_answers.php`: API para obtener todas las respuestas
- `get_open_answers.php`: API para respuestas abiertas

#### **🛠️ Panel de Administración**
- `admin_panel.php`: Panel principal de administración
- `manage_users.php`: Gestión de usuarios
- `manage_questions.php`: Gestión de preguntas
- `rehabilitate_user.php`: Rehabilitación de usuarios anulados
- `create_admin.php`: Script para crear usuario administrador

#### **🔧 Utilidades**
- `import_questions.php`: Importación masiva de preguntas
- `export_questions.php`: Exportación de preguntas

## 🚀 Instalación

### Paso 1: Configuración del Entorno
1. **Clona el repositorio** en tu servidor local
2. **Configura el servidor web** para servir desde la carpeta `public/`
3. **Asegúrate de tener los requisitos** instalados

### Paso 2: Base de Datos
1. **Crea una base de datos** llamada `exam_system`
2. **Importa el esquema principal**:
   ```sql
   mysql -u usuario -p exam_system < db/exam_system.sql
   ```
3. **Agrega las columnas de estado** (si es necesario):
   ```sql
   mysql -u usuario -p exam_system < config/add_exam_status_column.sql
   ```

### Paso 3: Configuración
1. **Edita** `config/database.php` con tus credenciales de base de datos:
   ```php
   $host = 'localhost';
   $dbname = 'exam_system';
   $username = 'tu_usuario';
   $password = 'tu_contraseña';
   ```

### Paso 4: Crear Administrador
1. **Ejecuta el script** para crear el usuario administrador:
   ```bash
   php public/create_admin.php
   ```
2. **Credenciales por defecto**:
   - **Email**: `admin@test.com`
   - **Contraseña**: `admin123`

### Paso 5: Acceso
- **Aplicación**: `http://tu-servidor/public/`
- **Panel Admin**: Inicia sesión con las credenciales de administrador

## 📋 Requisitos

- **PHP 7.4** o superior
- **Servidor web** (Apache, Nginx, etc.)
- **MySQL 5.7** o **MariaDB 10.2** o superior
- **Extensiones PHP**:
  - PDO
  - PDO_MySQL
  - JSON
  - Session

## 🎯 Uso del Sistema

### Para Estudiantes
1. **Registrarse** en el sistema
2. **Iniciar sesión** y acceder al dashboard
3. **Realizar el examen** con temporizador
4. **Ver resultados** una vez completado

### Para Administradores
1. **Acceder al panel de administración**
2. **Gestionar usuarios** (ver, editar, rehabilitar)
3. **Administrar preguntas** (crear, editar, importar)
4. **Revisar estadísticas** y resultados
5. **Rehabilitar usuarios** anulados si es necesario

## 🔧 Funcionalidades Técnicas

### Sistema de Anulación
- **Detección automática** de comportamiento sospechoso
- **Prevención de re-intentos** después de anulación
- **Rehabilitación controlada** por administradores

### Temporizador Flotante
- **Visualización persistente** del tiempo restante
- **Alertas automáticas** cuando queda poco tiempo
- **Envío automático** al finalizar el tiempo

### Dashboard de Resultados
- **Visualización completa** de respuestas correctas e incorrectas
- **Estadísticas detalladas** de rendimiento
- **Cálculo transparente** de porcentajes
- **Clasificación por niveles** de rendimiento

## 📊 Estructura de la Base de Datos

### Tablas Principales
- **`users`**: Información de usuarios y estado de examen
- **`questions`**: Preguntas del examen (opción múltiple y abiertas)
- **`options`**: Opciones para preguntas de opción múltiple
- **`user_answers`**: Respuestas de los usuarios

### Campos Importantes
- **`exam_status`**: Estado del examen (allowed/annulled)
- **`exam_annulled_at`**: Fecha de anulación del examen
- **`rol`**: Rol del usuario (0=estudiante, 1=admin)

## 🛡️ Seguridad

- **Autenticación por sesiones**
- **Control de acceso por roles**
- **Validación de datos de entrada**
- **Prevención de inyección SQL** con PDO
- **Protección contra re-intentos** no autorizados

## 📝 Licencia

Este proyecto está bajo la **Licencia MIT**.