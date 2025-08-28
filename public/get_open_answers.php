<?php
session_start();
require_once '../config/database.php';

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

// Verificar que se proporcione el user_id
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de usuario requerido']);
    exit;
}

$user_id = intval($_GET['user_id']);

try {
    // Obtener las respuestas abiertas del usuario
    $stmt = $pdo->prepare("
        SELECT 
            q.question_text,
            r.answer_text
        FROM user_answers r
        INNER JOIN questions q ON r.question_id = q.id
        WHERE r.user_id = ? AND q.type = 'abierta'
        ORDER BY q.id
    ");
    
    $stmt->execute([$user_id]);
    $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear las respuestas para el frontend
    $formatted_answers = [];
    foreach ($answers as $answer) {
        $formatted_answers[] = [
            'question' => htmlspecialchars($answer['question_text'], ENT_QUOTES, 'UTF-8'),
            'answer' => htmlspecialchars($answer['answer_text'] ?? '', ENT_QUOTES, 'UTF-8')
        ];
    }
    
    echo json_encode([
        'success' => true,
        'answers' => $formatted_answers
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error al obtener las respuestas: ' . $e->getMessage()
    ]);
}
?>