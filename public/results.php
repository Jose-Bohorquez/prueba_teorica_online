<?php
session_start();
include("../config/database.php");

// Si no hay sesión, redirigimos
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Obtenemos todos los usuarios que han respondido
$stmt = $pdo->query("
    SELECT DISTINCT u.id, u.nombre, u.email
    FROM users u
    INNER JOIN user_answers ua ON ua.user_id = u.id
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

function calcularResultados($pdo, $user_id) {
    // Total de preguntas
    $stmt = $pdo->query("SELECT COUNT(*) FROM questions");
    $total_preguntas = $stmt->fetchColumn();

    // Respuestas correctas
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM user_answers ua
        INNER JOIN options o ON ua.option_id = o.id
        WHERE ua.user_id = :user_id AND o.is_correct = 1
    ");
    $stmt->execute(['user_id' => $user_id]);
    $correctas = $stmt->fetchColumn();

    // Porcentaje de aciertos
    $porcentaje = $total_preguntas > 0 ? round(($correctas / $total_preguntas) * 100, 2) : 0;

    return [
        'total' => $total_preguntas,
        'correctas' => $correctas,
        'porcentaje' => $porcentaje
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de Exámenes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-xl">
        <h1 class="text-3xl font-bold mb-6 text-center">Resultados de los Candidatos</h1>

        <table class="w-full border border-gray-200 rounded-xl overflow-hidden">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3">#</th>
                    <th class="p-3">Nombre</th>
                    <th class="p-3">Correo</th>
                    <th class="p-3 text-center">Correctas</th>
                    <th class="p-3 text-center">Total</th>
                    <th class="p-3 text-center">Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $index => $user): 
                    $resultados = calcularResultados($pdo, $user['id']);
                ?>
                <tr class="border-t">
                    <td class="p-3"><?php echo $index + 1; ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($user['nombre']); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="p-3 text-center"><?php echo $resultados['correctas']; ?></td>
                    <td class="p-3 text-center"><?php echo $resultados['total']; ?></td>
                    <td class="p-3 text-center font-bold <?php echo $resultados['porcentaje'] >= 60 ? 'text-green-600' : 'text-red-600'; ?>">
                        <?php echo $resultados['porcentaje']; ?>%
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-6 text-center">
            <a href="dashboard.php"
               class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition">
               Volver al Dashboard
            </a>
        </div>
    </div>
</body>
</html>
