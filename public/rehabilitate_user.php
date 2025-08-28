<?php
session_start();
require_once '../config/database.php';

// Verificar que el usuario esté autenticado y sea admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user_id']) || !is_numeric($input['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de usuario inválido']);
    exit;
}

$user_id = (int)$input['user_id'];

try {
    // Verificar que el usuario existe y tiene examen anulado
    $stmt = $pdo->prepare("SELECT id, nombre, exam_status FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }
    
    if ($user['exam_status'] !== 'annulled') {
        echo json_encode(['success' => false, 'message' => 'El usuario no tiene el examen anulado']);
        exit;
    }
    
    // Rehabilitar al usuario: cambiar exam_status a 'allowed' y limpiar exam_annulled_at
    $stmt = $pdo->prepare("UPDATE users SET exam_status = 'allowed', exam_annulled_at = NULL WHERE id = ?");
    $result = $stmt->execute([$user_id]);
    
    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => 'Usuario rehabilitado correctamente',
            'user_name' => $user['nombre']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al rehabilitar al usuario']);
    }
    
} catch (PDOException $e) {
    error_log("Error en rehabilitate_user.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>