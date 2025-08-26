<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Verificar si ya respondió
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_answers WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$yaRespondio = $stmt->fetchColumn();

if ($yaRespondio > 0) {
    header("Location: dashboard.php?msg=ya_presento");
    exit();
}

// Generar orden de preguntas si no existe
if (!isset($_SESSION['exam_questions'])) {
    $stmt = $pdo->prepare("SELECT id FROM questions");
    $stmt->execute();
    $question_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    shuffle($question_ids);
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

// Opciones aleatorias
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
    <form action="submit_exam.php" method="POST" class="space-y-6">
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

// Habilitar botón solo si acepta términos
acceptTerms.addEventListener('change', () => {
    startBtn.disabled = !acceptTerms.checked;
});

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

// Detectar pérdida de foco (Alt+Tab, cambio de ventana)
window.addEventListener('blur', () => {
    Swal.fire({
        icon: 'warning',
        title: '¡Atención!',
        text: 'No debes salir de la ventana del examen. Esta acción ha sido detectada.',
        confirmButtonText: 'Entendido'
    });
});

// Bloquear copiar/pegar y clic derecho
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('paste', e => e.preventDefault());
document.addEventListener('contextmenu', e => e.preventDefault());
</script>

</body>
</html>
