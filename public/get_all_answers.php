<?php
session_start();
require_once '../config/database.php';

// Verificar que el usuario esté autenticado y sea admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de usuario requerido']);
    exit;
}

$user_id = (int)$_GET['user_id'];

try {
    // Obtener todas las respuestas del usuario con información de las preguntas
    $stmt = $pdo->prepare("
        SELECT 
            q.id as question_id,
            q.question_text,
            q.type,
            ua.option_id,
            ua.answer_text,
            ua.created_at as answered_at,
            o.option_text,
            o.is_correct,
            -- Para preguntas de opción múltiple, obtener todas las opciones
            (SELECT GROUP_CONCAT(
                CONCAT(opt.option_text, '|', opt.is_correct) 
                ORDER BY opt.id SEPARATOR ';;'
            ) FROM options opt WHERE opt.question_id = q.id) as all_options
        FROM user_answers ua
        INNER JOIN questions q ON ua.question_id = q.id
        LEFT JOIN options o ON ua.option_id = o.id
        WHERE ua.user_id = :user_id
        ORDER BY q.id, ua.created_at
    ");
    
    $stmt->execute(['user_id' => $user_id]);
    $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Procesar las respuestas para estructurarlas mejor
    $processed_answers = [];
    
    foreach ($answers as $answer) {
        $question_id = $answer['question_id'];
        
        if (!isset($processed_answers[$question_id])) {
            $processed_answers[$question_id] = [
                'question_id' => $question_id,
                'question_text' => $answer['question_text'],
                'type' => $answer['type'],
                'answered_at' => $answer['answered_at']
            ];
            
            if ($answer['type'] === 'opcion_multiple') {
                // Procesar opciones para preguntas de opción múltiple
                $all_options = [];
                if ($answer['all_options']) {
                    $options_data = explode(';;', $answer['all_options']);
                    foreach ($options_data as $option_data) {
                        list($option_text, $is_correct) = explode('|', $option_data);
                        $all_options[] = [
                            'text' => $option_text,
                            'is_correct' => (bool)$is_correct
                        ];
                    }
                }
                
                $processed_answers[$question_id]['all_options'] = $all_options;
                $processed_answers[$question_id]['selected_option'] = $answer['option_text'];
                $processed_answers[$question_id]['is_correct'] = (bool)$answer['is_correct'];
            } else {
                // Para preguntas abiertas
                $processed_answers[$question_id]['answer_text'] = $answer['answer_text'];
                $processed_answers[$question_id]['is_correct'] = null; // Las preguntas abiertas no tienen corrección automática
            }
        }
    }
    
    // Obtener información del usuario
    $stmt = $pdo->prepare("SELECT nombre, email FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calcular estadísticas
    $total_questions = count($processed_answers);
    $correct_answers = 0;
    $multiple_choice_questions = 0;
    $open_questions = 0;
    
    foreach ($processed_answers as $answer) {
        if ($answer['type'] === 'opcion_multiple') {
            $multiple_choice_questions++;
            if ($answer['is_correct']) {
                $correct_answers++;
            }
        } else {
            $open_questions++;
        }
    }
    
    $percentage = $multiple_choice_questions > 0 ? round(($correct_answers / $multiple_choice_questions) * 100, 2) : 0;
    
    echo json_encode([
        'success' => true,
        'user_info' => $user_info,
        'answers' => array_values($processed_answers),
        'statistics' => [
            'total_questions' => $total_questions,
            'correct_answers' => $correct_answers,
            'multiple_choice_questions' => $multiple_choice_questions,
            'open_questions' => $open_questions,
            'percentage' => $percentage,
            'percentage_explanation' => "El porcentaje se calcula como: ({$correct_answers} respuestas correctas / {$multiple_choice_questions} preguntas de opción múltiple) × 100 = {$percentage}%"
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>