<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Verificar el estado del examen del usuario
$stmt = $pdo->prepare("SELECT exam_status FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_exam_status = $stmt->fetchColumn();

// Si el examen está anulado, redirigir al dashboard
if ($user_exam_status === 'annulled') {
    header('Location: dashboard.php?exam_annulled=1');
    exit;
}

// Verificar si ya respondió
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_answers WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$yaRespondio = $stmt->fetchColumn();

if ($yaRespondio > 0) {
    header("Location: dashboard.php?msg=ya_presento");
    exit();
}

// Generar orden de preguntas si no existe (máximo 40 preguntas)
if (!isset($_SESSION['exam_questions'])) {
    $stmt = $pdo->prepare("SELECT id FROM questions");
    $stmt->execute();
    $question_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    shuffle($question_ids);
    
    // Limitar a máximo 40 preguntas
    $max_questions = 40;
    if (count($question_ids) > $max_questions) {
        $question_ids = array_slice($question_ids, 0, $max_questions);
    }
    
    $_SESSION['exam_questions'] = $question_ids;
}

// Obtener preguntas según orden
$placeholders = rtrim(str_repeat('?,', count($_SESSION['exam_questions'])), ',');
$stmt = $pdo->prepare("SELECT * FROM questions WHERE id IN ($placeholders)");
$stmt->execute($_SESSION['exam_questions']);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Reordenar según sesión
$questions_ordered = [];
foreach ($_SESSION['exam_questions'] as $qid) {
    foreach ($questions as $q) {
        if ($q['id'] == $qid) {
            $questions_ordered[] = $q;
            break;
        }
    }
}
$questions = $questions_ordered;

// Opciones aleatorias - se mezclan cada vez que se consultan
$options_stmt = $pdo->prepare("SELECT * FROM options WHERE question_id = :question_id ORDER BY RAND()");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Examen General</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">

<!-- Preámbulo con Términos y Condiciones -->
<div class="container mx-auto py-10 text-center" id="startContainer">
    <h1 class="text-3xl font-bold mb-6">Examen de Conocimientos Técnicos Generales</h1>

    <div class="bg-white p-6 rounded-xl shadow text-left max-w-2xl mx-auto mb-6">
        <h2 class="text-xl font-semibold mb-4">Términos y Condiciones</h2>
        <ul class="list-disc list-inside text-gray-700 space-y-2">
            <li>Antes de iniciar, habilite la cámara del dispositivo y comparta la pantalla durante todo el examen.</li>
            <li>El examen debe presentarse individualmente, sin ayudas externas, notas, dispositivos o personas que apoyen.</li>
            <li>No se permite copiar, pegar, abrir otras pestañas o cambiar de ventana durante el examen.</li>
            <li>Debe mantener la cámara encendida y visible, así como la pantalla compartida si el examen es en línea.</li>
            <li>Está prohibido el uso de teléfonos móviles, chats, mensajería o cualquier otro medio de comunicación durante el examen.</li>
            <li>Si se detecta cualquier infracción a estas normas, el examen podrá ser anulado automáticamente.</li>
            <li>Usted puede ser monitoreado por supervisor remoto o presencial para garantizar la integridad del examen.</li>
            <li>Los datos recabados durante el examen serán usados únicamente con fines de selección y para futuras oportunidades de reclutamiento, de acuerdo con la normativa de privacidad vigente.</li>
            <li>Al dar click en "Iniciar Examen" acepta cumplir con todas estas condiciones, entiende que cualquier incumplimiento será registrado y autoriza el uso de sus datos para los fines mencionados.</li>
        </ul>

        <label class="mt-4 block">
            <input type="checkbox" id="acceptTerms" class="mr-2">
            He leído y acepto los términos y condiciones
        </label>

        <!-- Botón centrado y habilitado solo al aceptar -->
        <div class="mt-6 text-center">
            <button id="startExam"
                class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition"
                disabled>
                Iniciar Examen
            </button>
        </div>
    </div>
</div>



<!-- Contenido del examen -->
<div id="examContent" class="container mx-auto py-10" style="display:none;">
    <!-- Temporizador Flotante -->
    <div id="floating-timer" class="fixed top-4 right-4 bg-white rounded-xl shadow-lg p-4 z-50 border-2 border-blue-200">
        <div class="text-center">
            <div id="timer" class="text-xl font-bold text-blue-600 mb-1">02:00:00</div>
            <div class="text-xs text-gray-600">Tiempo Restante</div>
            <div class="text-xs text-red-500 mt-1">Auto-envío al finalizar</div>
        </div>
    </div>
    
    <form id="examForm" action="submit_exam.php" method="POST" class="space-y-6">
        <?php foreach ($questions as $q): ?>
            <div class="bg-white p-6 rounded-2xl shadow">
                <h2 class="text-lg font-semibold mb-4"><?php echo htmlspecialchars($q['question_text']); ?></h2>

                <?php if ($q['type'] === 'opcion_multiple'): ?>
                    <?php
                        $options_stmt->execute(['question_id' => $q['id']]);
                        $options = $options_stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <?php foreach ($options as $opt): ?>
                        <label class="block mb-2 cursor-pointer">
                            <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="<?php echo $opt['id']; ?>" required>
                            <?php echo htmlspecialchars($opt['option_text']); ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <textarea name="answers[<?php echo $q['id']; ?>]" class="w-full p-2 border rounded-xl" required></textarea>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition">
            Enviar Respuestas
        </button>
    </form>
</div>

<script>
const startBtn = document.getElementById('startExam');
const startContainer = document.getElementById('startContainer');
const examDiv = document.getElementById('examContent');
const acceptTerms = document.getElementById('acceptTerms');
const timerDisplay = document.getElementById('timer');
const examForm = document.getElementById('examForm');

let timeLeft = 2 * 60 * 60; // 2 horas en segundos
let timerInterval;

// Habilitar botón solo si acepta términos
acceptTerms.addEventListener('change', () => {
    startBtn.disabled = !acceptTerms.checked;
});

// Función para formatear tiempo
function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

// Función del temporizador
function startTimer() {
    timerInterval = setInterval(() => {
        timeLeft--;
        timerDisplay.textContent = formatTime(timeLeft);
        
        // Cambiar color cuando queden 30 minutos
        if (timeLeft <= 30 * 60) {
            timerDisplay.classList.add('text-red-800');
            timerDisplay.classList.remove('text-red-600');
        }
        
        // Cambiar color cuando queden 10 minutos
        if (timeLeft <= 10 * 60) {
            timerDisplay.classList.add('animate-pulse');
        }
        
        // Enviar automáticamente cuando se agote el tiempo
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            Swal.fire({
                icon: 'warning',
                title: 'Tiempo Agotado',
                text: 'El tiempo del examen ha terminado. Se enviarán automáticamente tus respuestas.',
                confirmButtonText: 'Entendido',
                allowOutsideClick: false
            }).then(() => {
                examForm.submit();
            });
        }
    }, 1000);
}

// Iniciar examen
startBtn.addEventListener('click', () => {
    // Solicitar pantalla completa
    const elem = document.documentElement;
    if (elem.requestFullscreen) elem.requestFullscreen();
    else if (elem.mozRequestFullScreen) elem.mozRequestFullScreen();
    else if (elem.webkitRequestFullscreen) elem.webkitRequestFullscreen();
    else if (elem.msRequestFullscreen) elem.msRequestFullscreen();

    // Mostrar examen y ocultar preámbulo
    examDiv.style.display = 'block';
    startContainer.style.display = 'none';
    
    // Iniciar temporizador
    startTimer();
});

// Bloquear teclas peligrosas
document.addEventListener('keydown', function(e) {
    const blockedKeys = ['F12', 'Tab', 'Escape'];
    if (e.ctrlKey || e.altKey || e.metaKey || blockedKeys.includes(e.key)) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Acción no permitida',
            text: 'No se pueden usar atajos de teclado durante el examen.',
            confirmButtonText: 'Entendido'
        });
    }
});

// Sistema de alertas por cambio de ventana
let alertCount = 0;
const maxAlerts = 3;

// Detectar pérdida de foco (Alt+Tab, cambio de ventana)
window.addEventListener('blur', () => {
    // Solo contar si el examen ya comenzó
    if (examDiv.style.display === 'block') {
        alertCount++;
        
        if (alertCount >= maxAlerts) {
            // Marcar el examen como anulado en la base de datos
            fetch('annul_exam.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: <?php echo $_SESSION['user_id']; ?>
                })
            });
            
            // Anular examen después de 3 alertas
            clearInterval(timerInterval);
            Swal.fire({
                icon: 'error',
                title: 'Examen Anulado',
                text: 'Has excedido el número máximo de alertas permitidas (3). El examen ha sido anulado permanentemente.',
                confirmButtonText: 'Entendido',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                // Redirigir al dashboard con mensaje de anulación
                window.location.href = 'dashboard.php?exam_annulled=1';
            });
        } else {
            const remainingAlerts = maxAlerts - alertCount;
            Swal.fire({
                icon: 'warning',
                title: `¡Alerta ${alertCount}/3!`,
                text: `No debes salir de la ventana del examen. Te quedan ${remainingAlerts} alertas antes de que el examen sea anulado automáticamente.`,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#d33'
            });
        }
    }
});

// Bloquear copiar/pegar y clic derecho
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('paste', e => e.preventDefault());
document.addEventListener('contextmenu', e => e.preventDefault());
</script>

</body>
</html>
