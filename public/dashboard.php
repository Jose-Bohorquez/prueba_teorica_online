<?php
session_start();
include("../config/database.php");

// Comprobamos que haya sesión iniciada
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Definimos el único examen disponible
$exam = [
    'id' => 1,
    'title' => 'Examen General',
    'description' => 'Este examen único evalúa todos los temas disponibles.'
];

// Capturamos el mensaje si viene de exam.php
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema de Exámenes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto py-10">

        <!-- Barra superior -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></h1>
            <button id="btnLogout"
                class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700 transition">
                Cerrar sesión
            </button>
        </div>

        <!-- Recordatorio de normas antes del examen -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-2xl shadow mb-6">
            <h2 class="text-xl font-bold mb-4">Recordatorio antes de iniciar el examen</h2>
            <ul class="list-disc list-inside text-gray-700 space-y-2">
                <li>Antes de iniciar, habilite la cámara del dispositivo y comparta la pantalla durante todo el examen.</li>
                <li>El examen debe presentarse individualmente, sin ayudas externas, notas, dispositivos o personas que apoyen.</li>
                <li>No se permite copiar, pegar, abrir otras pestañas o cambiar de ventana durante el examen.</li>
                <li>Debe mantener la cámara encendida y visible, así como la pantalla compartida si el examen es en línea.</li>
                <li>Si se detecta cualquier infracción a estas normas, el examen podrá ser anulado automáticamente.</li>
                <li>Está prohibido el uso de teléfonos móviles, chats, mensajería o cualquier otro medio de comunicación durante el examen.</li>
                <li>Recuerde que puede estar monitoreado por supervisor remoto o presencial para garantizar la integridad del examen.</li>
                <li>Al dar click en "Iniciar Examen" acepta cumplir con todas estas condiciones y entiende que cualquier incumplimiento será registrado.</li>
            </ul>
            <div class="mt-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" id="acceptTerms" class="form-checkbox h-5 w-5 text-green-600">
                    <span class="ml-2 text-gray-700">Acepto los términos, Condiciones y entiendo las observaciones</span>
                </label>
            </div>
        </div>

        <!-- Tarjeta del examen -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white shadow-lg p-6 rounded-2xl text-center">
                <h2 class="text-xl font-bold"><?php echo $exam['title']; ?></h2>
                <p class="text-gray-600 mb-4"><?php echo $exam['description']; ?></p>
                <a href="exam.php?id=<?php echo $exam['id']; ?>" id="startExamBtn"
                   class="bg-green-600 text-white px-4 py-2 rounded-xl hover:bg-green-700 pointer-events-none opacity-50 transition">
                    Iniciar Examen
                </a>
            </div>
        </div>
    </div>

    <?php if ($msg === 'ya_presento'): ?>
        <script>
            Swal.fire({
                icon: 'warning',
                title: '¡Examen ya presentado!',
                text: 'Ya realizaste este examen y no puedes repetirlo.',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#3085d6'
            });
        </script>
    <?php endif; ?>

    <script>
        // Habilitar botón solo si acepta términos
        const acceptCheckbox = document.getElementById('acceptTerms');
        const startBtn = document.getElementById('startExamBtn');

        acceptCheckbox.addEventListener('change', () => {
            if (acceptCheckbox.checked) {
                startBtn.classList.remove('pointer-events-none', 'opacity-50');
            } else {
                startBtn.classList.add('pointer-events-none', 'opacity-50');
            }
        });

        // SweetAlert para cerrar sesión
        document.getElementById('btnLogout').addEventListener('click', function() {
            Swal.fire({
                title: '¿Cerrar sesión?',
                text: 'Se cerrará tu sesión actual y volverás al login.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        });
    </script>
</body>
</html>
