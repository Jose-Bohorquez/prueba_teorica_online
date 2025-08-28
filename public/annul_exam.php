<?php
session_start();
require_once '../config/database.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener los datos JSON
$input = json_decode(file_get_contents('php://input'), true);

// Verificar que el user_id coincida con la sesión
if (!isset($input['user_id']) || $input['user_id'] != $_SESSION['user_id']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

try {
    // Marcar el examen como anulado
    $stmt = $pdo->prepare("UPDATE users SET exam_status = 'annulled', exam_annulled_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$_SESSION['user_id']]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Examen anulado correctamente']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al anular el examen']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>