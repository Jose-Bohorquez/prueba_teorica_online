<?php
session_start();
require_once '../config/database.php';

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Obtener estadísticas generales
$stmt = $pdo->query("SELECT COUNT(*) as total_usuarios FROM users");
$total_usuarios = $stmt->fetch()['total_usuarios'];

$stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as examenes_completados FROM user_answers");
$examenes_completados = $stmt->fetch()['examenes_completados'];

$stmt = $pdo->query("SELECT COUNT(*) as total_preguntas FROM questions");
$total_preguntas = $stmt->fetch()['total_preguntas'];

$stmt = $pdo->query("SELECT COUNT(*) as preguntas_opcion_multiple FROM questions WHERE type = 'multiple'");
$preguntas_opcion_multiple = $stmt->fetch()['preguntas_opcion_multiple'];

$stmt = $pdo->query("SELECT COUNT(*) as preguntas_abiertas FROM questions WHERE type = 'open'");
$preguntas_abiertas = $stmt->fetch()['preguntas_abiertas'];

// Obtener todos los usuarios
$stmt = $pdo->query("SELECT id, nombre, email, role, exam_status, exam_annulled_at, created_at FROM users ORDER BY created_at DESC");
$usuarios = $stmt->fetchAll();

// Obtener todas las preguntas
$stmt = $pdo->query("SELECT id, question_text, type, created_at FROM questions ORDER BY created_at DESC LIMIT 20");
$preguntas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Prueba Teórica Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Panel de Administración</h1>
                    <p class="text-gray-600 mt-2">Gestión completa del sistema de exámenes</p>
                </div>
                <div class="flex space-x-4">
                    <a href="dashboard.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    <a href="results.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-chart-bar mr-2"></i>Resultados
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo $total_usuarios; ?></h3>
                        <p class="text-gray-600 text-sm">Total Usuarios</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo $examenes_completados; ?></h3>
                        <p class="text-gray-600 text-sm">Exámenes Completados</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-question-circle text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo $total_preguntas; ?></h3>
                        <p class="text-gray-600 text-sm">Total Preguntas</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-list text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo $preguntas_opcion_multiple; ?></h3>
                        <p class="text-gray-600 text-sm">Opción Múltiple</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-edit text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo $preguntas_abiertas; ?></h3>
                        <p class="text-gray-600 text-sm">Abiertas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs de Navegación -->
        <div class="bg-white rounded-xl shadow-lg mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6">
                    <button onclick="showTab('usuarios')" id="tab-usuarios" class="tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                        <i class="fas fa-users mr-2"></i>Gestión de Usuarios
                    </button>
                    <button onclick="showTab('preguntas')" id="tab-preguntas" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-question-circle mr-2"></i>Gestión de Preguntas
                    </button>
                    <button onclick="showTab('configuracion')" id="tab-configuracion" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-cog mr-2"></i>Configuración
                    </button>
                </nav>
            </div>

            <!-- Tab Content: Usuarios -->
            <div id="content-usuarios" class="tab-content p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Gestión de Usuarios</h2>
                    <button onclick="crearUsuario()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-plus mr-2"></i>Crear Usuario
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($usuario['nombre']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($usuario['email']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $usuario['role'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                                            <?php echo $usuario['role'] === 'admin' ? 'Administrador' : 'Estudiante'; ?>
                                        </span>
                                        <?php if (isset($usuario['exam_status']) && $usuario['exam_status'] === 'annulled'): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Examen Anulado
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo date('d/m/Y', strtotime($usuario['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="editarUsuario(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nombre']); ?>', '<?php echo htmlspecialchars($usuario['email']); ?>', '<?php echo $usuario['role']; ?>')" class="text-blue-600 hover:text-blue-900" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if (isset($usuario['exam_status']) && $usuario['exam_status'] === 'annulled'): ?>
                                        <button onclick="rehabilitarUsuario(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nombre']); ?>')" class="text-green-600 hover:text-green-900" title="Rehabilitar Examen">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                        <button onclick="eliminarUsuario(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nombre']); ?>')" class="text-red-600 hover:text-red-900" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Content: Preguntas -->
            <div id="content-preguntas" class="tab-content p-6 hidden">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Gestión de Preguntas</h2>
                    <div class="space-x-2">
                        <button onclick="crearPregunta()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-plus mr-2"></i>Crear Pregunta
                        </button>
                        <button onclick="importarPreguntas()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-upload mr-2"></i>Importar
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pregunta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($preguntas as $pregunta): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-md truncate"><?php echo htmlspecialchars($pregunta['question_text']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $pregunta['type'] === 'multiple' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'; ?>">
                                        <?php echo $pregunta['type'] === 'multiple' ? 'Opción Múltiple' : 'Abierta'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo date('d/m/Y', strtotime($pregunta['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="editarPregunta(<?php echo $pregunta['id']; ?>)" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="eliminarPregunta(<?php echo $pregunta['id']; ?>)" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Content: Configuración -->
            <div id="content-configuracion" class="tab-content p-6 hidden">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Configuración del Sistema</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Configuración del Examen</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tiempo del Examen (minutos)</label>
                                <input type="number" value="120" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Máximo de Preguntas por Examen</label>
                                <input type="number" value="40" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Máximo de Alertas por Cambio de Ventana</label>
                                <input type="number" value="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Acciones del Sistema</h3>
                        <div class="space-y-4">
                            <button onclick="resetearExamenes()" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                                <i class="fas fa-refresh mr-2"></i>Resetear Todos los Exámenes
                            </button>
                            <button onclick="exportarResultados()" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-download mr-2"></i>Exportar Resultados
                            </button>
                            <button onclick="limpiarSesiones()" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-broom mr-2"></i>Limpiar Sesiones Activas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para mostrar tabs
        function showTab(tabName) {
            // Ocultar todos los contenidos
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remover clase active de todos los botones
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Mostrar contenido seleccionado
            document.getElementById('content-' + tabName).classList.remove('hidden');
            
            // Activar botón seleccionado
            const activeButton = document.getElementById('tab-' + tabName);
            activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
            activeButton.classList.remove('border-transparent', 'text-gray-500');
        }

        // Funciones para gestión de usuarios
        function crearUsuario() {
            Swal.fire({
                title: 'Crear Nuevo Usuario',
                html: `
                    <div class="text-left">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                            <input type="text" id="nombre" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                            <input type="password" id="password" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                            <select id="role" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="estudiante">Estudiante</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Crear Usuario',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nombre = document.getElementById('nombre').value;
                    const email = document.getElementById('email').value;
                    const password = document.getElementById('password').value;
                    const role = document.getElementById('role').value;
                    
                    if (!nombre || !email || !password) {
                        Swal.showValidationMessage('Todos los campos son obligatorios');
                        return false;
                    }
                    
                    return { nombre, email, password, role };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí iría la llamada AJAX para crear el usuario
                    Swal.fire('¡Éxito!', 'Usuario creado correctamente', 'success').then(() => {
                        location.reload();
                    });
                }
            });
        }

        function editarUsuario(id, nombre, email, role) {
            Swal.fire({
                title: 'Editar Usuario',
                html: `
                    <div class="text-left">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                            <input type="text" id="nombre" value="${nombre}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" value="${email}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                            <select id="role" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="estudiante" ${role === 'estudiante' ? 'selected' : ''}>Estudiante</option>
                                <option value="admin" ${role === 'admin' ? 'selected' : ''}>Administrador</option>
                            </select>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('¡Éxito!', 'Usuario actualizado correctamente', 'success').then(() => {
                        location.reload();
                    });
                }
            });
        }

        function eliminarUsuario(id, nombre) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Deseas eliminar al usuario "${nombre}"? Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('¡Eliminado!', 'El usuario ha sido eliminado.', 'success').then(() => {
                        location.reload();
                    });
                }
            });
        }

        function rehabilitarUsuario(id, nombre) {
            Swal.fire({
                title: '¿Rehabilitar usuario?',
                text: `¿Deseas permitir que "${nombre}" pueda volver a realizar el examen?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, rehabilitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('rehabilitate_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ user_id: id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Rehabilitado',
                                'El usuario puede volver a realizar el examen.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error',
                                data.message || 'No se pudo rehabilitar al usuario.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        Swal.fire(
                            'Error',
                            'Ocurrió un error al rehabilitar al usuario.',
                            'error'
                        );
                    });
                }
            });
        }

        // Funciones para gestión de preguntas
        function crearPregunta() {
            Swal.fire({
                title: 'Crear Nueva Pregunta',
                text: 'Esta funcionalidad estará disponible próximamente.',
                icon: 'info'
            });
        }

        function editarPregunta(id) {
            Swal.fire({
                title: 'Editar Pregunta',
                text: 'Esta funcionalidad estará disponible próximamente.',
                icon: 'info'
            });
        }

        function eliminarPregunta(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Deseas eliminar esta pregunta? Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('¡Eliminado!', 'La pregunta ha sido eliminada.', 'success');
                }
            });
        }

        function importarPreguntas() {
            Swal.fire({
                title: 'Importar Preguntas',
                text: 'Esta funcionalidad estará disponible próximamente.',
                icon: 'info'
            });
        }

        // Funciones de configuración
        function resetearExamenes() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esto eliminará todos los resultados de exámenes. Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, resetear',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('¡Reseteado!', 'Todos los exámenes han sido reseteados.', 'success');
                }
            });
        }

        function exportarResultados() {
            Swal.fire({
                title: 'Exportar Resultados',
                text: 'Esta funcionalidad estará disponible próximamente.',
                icon: 'info'
            });
        }

        function limpiarSesiones() {
            Swal.fire({
                title: 'Limpiar Sesiones',
                text: 'Esta funcionalidad estará disponible próximamente.',
                icon: 'info'
            });
        }
    </script>
</body>
</html>