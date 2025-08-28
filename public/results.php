<?php
session_start();
include("../config/database.php");

// Si no hay sesión, redirigimos
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Verificar que sea admin
$user_role = $_SESSION['role'] ?? 'estudiante';
if ($user_role !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Obtenemos todos los usuarios que han respondido
$stmt = $pdo->query("
    SELECT DISTINCT u.id, u.nombre, u.email, 
           (SELECT COUNT(*) FROM user_answers WHERE user_id = u.id) as total_respuestas,
           (SELECT MIN(created_at) FROM user_answers WHERE user_id = u.id) as fecha_examen
    FROM users u
    INNER JOIN user_answers ua ON ua.user_id = u.id
    ORDER BY fecha_examen DESC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas generales
$stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as total_candidatos FROM user_answers");
$total_candidatos = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as total_estudiantes FROM users WHERE role = 'estudiante'");
$total_estudiantes = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as total_preguntas FROM questions");
$total_preguntas_db = $stmt->fetchColumn();

function calcularResultados($pdo, $user_id) {
    // Total de preguntas respondidas por el usuario
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_answers WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $total_respondidas = $stmt->fetchColumn();

    // Respuestas correctas (solo opción múltiple)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM user_answers ua
        INNER JOIN options o ON ua.option_id = o.id
        WHERE ua.user_id = :user_id AND o.is_correct = 1
    ");
    $stmt->execute(['user_id' => $user_id]);
    $correctas = $stmt->fetchColumn();

    // Preguntas de opción múltiple respondidas
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM user_answers ua
        INNER JOIN questions q ON ua.question_id = q.id
        WHERE ua.user_id = :user_id AND q.type = 'opcion_multiple'
    ");
    $stmt->execute(['user_id' => $user_id]);
    $opcion_multiple = $stmt->fetchColumn();

    // Preguntas abiertas respondidas
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM user_answers ua
        INNER JOIN questions q ON ua.question_id = q.id
        WHERE ua.user_id = :user_id AND q.type = 'abierta'
    ");
    $stmt->execute(['user_id' => $user_id]);
    $abiertas = $stmt->fetchColumn();

    // Porcentaje de aciertos (solo sobre preguntas de opción múltiple)
    $porcentaje = $opcion_multiple > 0 ? round(($correctas / $opcion_multiple) * 100, 2) : 0;

    return [
        'total_respondidas' => $total_respondidas,
        'correctas' => $correctas,
        'opcion_multiple' => $opcion_multiple,
        'abiertas' => $abiertas,
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-2xl shadow-xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Panel de Resultados</h1>
            <a href="dashboard.php" class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition">
                Volver al Dashboard
            </a>
        </div>
        
        <!-- Estadísticas Generales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl text-center border border-blue-200 shadow-sm">
                <div class="text-3xl mb-2">👥</div>
                <h3 class="text-lg font-semibold text-blue-800">Total Estudiantes</h3>
                <p class="text-2xl font-bold text-blue-600"><?php echo $total_estudiantes; ?></p>
                <p class="text-xs text-blue-500 mt-1">Registrados en el sistema</p>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl text-center border border-green-200 shadow-sm">
                <div class="text-3xl mb-2">📋</div>
                <h3 class="text-lg font-semibold text-green-800">Exámenes Completados</h3>
                <p class="text-2xl font-bold text-green-600"><?php echo $total_candidatos; ?></p>
                <p class="text-xs text-green-500 mt-1">Estudiantes que terminaron</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-xl text-center border border-purple-200 shadow-sm">
                <div class="text-3xl mb-2">📊</div>
                <h3 class="text-lg font-semibold text-purple-800">Tasa de Participación</h3>
                <p class="text-2xl font-bold text-purple-600"><?php echo $total_estudiantes > 0 ? round(($total_candidatos / $total_estudiantes) * 100, 1) : 0; ?>%</p>
                <p class="text-xs text-purple-500 mt-1">Porcentaje de finalización</p>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-xl text-center border border-orange-200 shadow-sm">
                <div class="text-3xl mb-2">❓</div>
                <h3 class="text-lg font-semibold text-orange-800">Total Preguntas</h3>
                <p class="text-2xl font-bold text-orange-600"><?php echo $total_preguntas_db; ?></p>
                <p class="text-xs text-orange-500 mt-1">En la base de datos</p>
            </div>
        </div>
        
        <?php if ($total_candidatos > 0): 
            // Calcular estadísticas adicionales
            $aprobados = 0;
            $excelentes = 0;
            $promedio_general = 0;
            $suma_porcentajes = 0;
            
            foreach ($users as $user) {
                $resultados = calcularResultados($pdo, $user['id']);
                $suma_porcentajes += $resultados['porcentaje'];
                if ($resultados['porcentaje'] >= 60) $aprobados++;
                if ($resultados['porcentaje'] >= 80) $excelentes++;
            }
            
            $promedio_general = $total_candidatos > 0 ? round($suma_porcentajes / $total_candidatos, 1) : 0;
            $tasa_aprobacion = $total_candidatos > 0 ? round(($aprobados / $total_candidatos) * 100, 1) : 0;
            $tasa_excelencia = $total_candidatos > 0 ? round(($excelentes / $total_candidatos) * 100, 1) : 0;
        ?>
        
        <!-- Estadísticas de Rendimiento -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 p-4 rounded-xl text-center border border-emerald-200 shadow-sm">
                <div class="text-3xl mb-2">🎯</div>
                <h3 class="text-lg font-semibold text-emerald-800">Promedio General</h3>
                <p class="text-2xl font-bold text-emerald-600"><?php echo $promedio_general; ?>%</p>
                <p class="text-xs text-emerald-500 mt-1">Calificación promedio</p>
            </div>
            <div class="bg-gradient-to-br from-teal-50 to-teal-100 p-4 rounded-xl text-center border border-teal-200 shadow-sm">
                <div class="text-3xl mb-2">✅</div>
                <h3 class="text-lg font-semibold text-teal-800">Tasa de Aprobación</h3>
                <p class="text-2xl font-bold text-teal-600"><?php echo $tasa_aprobacion; ?>%</p>
                <p class="text-xs text-teal-500 mt-1"><?php echo $aprobados; ?> de <?php echo $total_candidatos; ?> aprobados</p>
            </div>
            <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-4 rounded-xl text-center border border-amber-200 shadow-sm">
                <div class="text-3xl mb-2">🏆</div>
                <h3 class="text-lg font-semibold text-amber-800">Tasa de Excelencia</h3>
                <p class="text-2xl font-bold text-amber-600"><?php echo $tasa_excelencia; ?>%</p>
                <p class="text-xs text-amber-500 mt-1"><?php echo $excelentes; ?> con 80% o más</p>
            </div>
        </div>
        <?php endif; ?>

        <table class="w-full border border-gray-200 rounded-xl overflow-hidden">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3">#</th>
                    <th class="p-3">Candidato</th>
                    <th class="p-3">Correo</th>
                    <th class="p-3 text-center">Fecha Examen</th>
                    <th class="p-3 text-center">Correctas</th>
                    <th class="p-3 text-center">Opción Múltiple</th>
                    <th class="p-3 text-center">Abiertas</th>
                    <th class="p-3 text-center">Porcentaje</th>
                    <th class="p-3 text-center">Estado</th>
                    <th class="p-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $index => $user): 
                    $resultados = calcularResultados($pdo, $user['id']);
                ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3 font-semibold"><?php echo $index + 1; ?></td>
                    <td class="p-3">
                        <div class="font-semibold"><?php echo htmlspecialchars($user['nombre']); ?></div>
                        <div class="text-sm text-gray-500">ID: <?php echo $user['id']; ?></div>
                    </td>
                    <td class="p-3 text-sm"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="p-3 text-center text-sm">
                        <?php echo date('d/m/Y H:i', strtotime($user['fecha_examen'])); ?>
                    </td>
                    <td class="p-3 text-center font-bold text-green-600"><?php echo $resultados['correctas']; ?></td>
                    <td class="p-3 text-center"><?php echo $resultados['opcion_multiple']; ?></td>
                    <td class="p-3 text-center"><?php echo $resultados['abiertas']; ?></td>
                    <td class="p-3 text-center">
                        <span class="px-3 py-1 rounded-full text-sm font-bold <?php echo $resultados['porcentaje'] >= 60 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo $resultados['porcentaje']; ?>%
                        </span>
                        <div class="text-xs text-gray-500 mt-1">
                            <?php echo $resultados['correctas']; ?>/<?php echo $resultados['opcion_multiple']; ?> correctas
                        </div>
                    </td>
                    <td class="p-3 text-center">
                        <?php 
                        $estado_clase = '';
                        $estado_texto = '';
                        $estado_icono = '';
                        
                        if ($resultados['porcentaje'] >= 80) {
                            $estado_clase = 'bg-green-100 text-green-800 border-green-200';
                            $estado_texto = 'Excelente';
                            $estado_icono = '🏆';
                        } elseif ($resultados['porcentaje'] >= 60) {
                            $estado_clase = 'bg-blue-100 text-blue-800 border-blue-200';
                            $estado_texto = 'Aprobado';
                            $estado_icono = '✅';
                        } else {
                            $estado_clase = 'bg-red-100 text-red-800 border-red-200';
                            $estado_texto = 'Reprobado';
                            $estado_icono = '❌';
                        }
                        ?>
                        <span class="px-2 py-1 rounded-lg text-xs font-semibold border <?php echo $estado_clase; ?>">
                            <?php echo $estado_icono; ?> <?php echo $estado_texto; ?>
                        </span>
                        <?php if ($resultados['abiertas'] > 0): ?>
                            <div class="text-xs text-orange-600 mt-1">
                                📝 <?php echo $resultados['abiertas']; ?> pregunta(s) abierta(s)
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="p-3 text-center">
                        <?php if ($resultados['total_respondidas'] > 0): ?>
                            <button onclick="verTodasLasRespuestas(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['nombre']); ?>')" 
                                    class="bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition">
                                Ver Todas las Respuestas
                            </button>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm">Sin respuestas</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($users)): ?>
            <div class="text-center py-8">
                <div class="text-gray-500 text-lg">No hay candidatos que hayan completado el examen aún.</div>
            </div>
        <?php endif; ?>
        
        <div class="mt-6 text-center">
            <a href="dashboard.php"
               class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition">
               Volver al Dashboard
            </a>
        </div>
    </div>

    <script>
        async function verTodasLasRespuestas(userId, nombreCandidato) {
            try {
                const response = await fetch(`get_all_answers.php?user_id=${userId}`);
                const data = await response.json();
                
                if (data.success) {
                    let html = `<div class="text-left max-h-96 overflow-y-auto">`;
                    
                    // Mostrar estadísticas
                    html += `
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                            <h4 class="font-semibold text-blue-800 mb-2">📊 Estadísticas del Examen</h4>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div><strong>Total de preguntas:</strong> ${data.statistics.total_questions}</div>
                                <div><strong>Respuestas correctas:</strong> ${data.statistics.correct_answers}</div>
                                <div><strong>Opción múltiple:</strong> ${data.statistics.multiple_choice_questions}</div>
                                <div><strong>Preguntas abiertas:</strong> ${data.statistics.open_questions}</div>
                            </div>
                            <div class="mt-2 p-2 bg-white rounded border">
                                <strong>Cálculo del porcentaje:</strong><br>
                                <span class="text-sm text-gray-600">${data.statistics.percentage_explanation}</span>
                            </div>
                        </div>
                    `;
                    
                    if (data.answers.length > 0) {
                        data.answers.forEach((answer, index) => {
                            const isCorrect = answer.is_correct;
                            const bgColor = answer.type === 'opcion_multiple' ? 
                                (isCorrect ? 'bg-green-50 border-l-4 border-green-400' : 'bg-red-50 border-l-4 border-red-400') : 
                                'bg-yellow-50 border-l-4 border-yellow-400';
                            
                            const statusIcon = answer.type === 'opcion_multiple' ? 
                                (isCorrect ? '✅' : '❌') : '📝';
                            
                            html += `
                                <div class="mb-4 p-3 ${bgColor} rounded-lg">
                                    <h4 class="font-semibold text-sm text-gray-700 mb-2">
                                        ${statusIcon} Pregunta ${index + 1} (${answer.type === 'opcion_multiple' ? 'Opción Múltiple' : 'Pregunta Abierta'})
                                    </h4>
                                    <p class="text-sm mb-3 font-medium">${answer.question_text}</p>
                            `;
                            
                            if (answer.type === 'opcion_multiple') {
                                html += `<h5 class="font-semibold text-sm mb-2">Opciones disponibles:</h5>`;
                                html += `<div class="mb-2">`;
                                answer.all_options.forEach(option => {
                                    const isSelected = option.text === answer.selected_option;
                                    const optionClass = isSelected ? 
                                        (option.is_correct ? 'bg-green-200 border-green-400' : 'bg-red-200 border-red-400') :
                                        (option.is_correct ? 'bg-green-100 border-green-300' : 'bg-gray-100 border-gray-300');
                                    
                                    const optionIcon = isSelected ? 
                                        (option.is_correct ? '✅ 👆' : '❌ 👆') :
                                        (option.is_correct ? '✅' : '');
                                    
                                    html += `
                                        <div class="p-2 mb-1 border rounded ${optionClass} text-sm">
                                            ${optionIcon} ${option.text}
                                        </div>
                                    `;
                                });
                                html += `</div>`;
                                
                                html += `
                                    <div class="mt-2 p-2 rounded text-sm ${
                                        isCorrect ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                    }">
                                        <strong>Respuesta seleccionada:</strong> ${answer.selected_option}<br>
                                        <strong>Resultado:</strong> ${isCorrect ? 'Correcta' : 'Incorrecta'}
                                    </div>
                                `;
                            } else {
                                html += `
                                    <h5 class="font-semibold text-sm text-orange-700 mb-1">Respuesta del estudiante:</h5>
                                    <div class="bg-white p-2 rounded border text-sm">
                                        ${answer.answer_text || 'Sin respuesta'}
                                    </div>
                                    <div class="mt-2 p-2 bg-yellow-100 text-yellow-800 rounded text-sm">
                                        <strong>Nota:</strong> Las preguntas abiertas requieren revisión manual
                                    </div>
                                `;
                            }
                            
                            html += `</div>`;
                        });
                    } else {
                        html += '<p class="text-center text-gray-500">No hay respuestas para este candidato.</p>';
                    }
                    
                    html += '</div>';
                    
                    Swal.fire({
                        title: `📋 Todas las Respuestas - ${nombreCandidato}`,
                        html: html,
                        width: '900px',
                        confirmButtonText: 'Cerrar',
                        confirmButtonColor: '#3085d6'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar las respuestas.',
                        confirmButtonText: 'Entendido'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al cargar las respuestas.',
                    confirmButtonText: 'Entendido'
                });
            }
        }
    </script>
</body>
</html>
