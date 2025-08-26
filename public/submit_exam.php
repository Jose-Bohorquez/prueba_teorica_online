<?php
session_start();
include("../config/database.php");

// Verificamos que el usuario haya iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Si no se enviaron respuestas, redirigimos al dashboard
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['answers'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$answers = $_POST['answers'];

// Guardamos cada respuesta en la base de datos
$stmt = $pdo->prepare("INSERT INTO user_answers (user_id, question_id, answer_text, option_id)
                       VALUES (:user_id, :question_id, :answer_text, :option_id)");

foreach ($answers as $question_id => $answer) {
    if (is_numeric($answer)) {
        // Si la respuesta es un ID de opción seleccionada
        $stmt->execute([
            'user_id' => $user_id,
            'question_id' => $question_id,
            'answer_text' => null,
            'option_id' => $answer
        ]);
    } else {
        // Si es una respuesta abierta
        $stmt->execute([
            'user_id' => $user_id,
            'question_id' => $question_id,
            'answer_text' => $answer,
            'option_id' => null
        ]);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Examen Finalizado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl text-center w-96">
        <h1 class="text-2xl font-bold text-green-600 mb-4">¡Examen enviado!</h1>
        <p class="text-gray-700 mb-6">
            Tus respuestas han sido guardadas correctamente.
        </p>
        <a href="logout.php"
           class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition">
           Volver al Dashboard
        </a>
    </div>
</body>
</html>
