<?php
require_once 'config/database.php';

// Crear usuario administrador por defecto
$admin_email = 'admin@test.com';
$admin_password = 'admin123';
$admin_name = 'Administrador';

try {
    // Verificar si ya existe un admin
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR role = 'admin'");
    $stmt->execute([$admin_email]);
    
    if ($stmt->rowCount() > 0) {
        echo "Ya existe un usuario administrador en el sistema.\n";
        echo "Credenciales existentes:\n";
        echo "Email: admin@test.com\n";
        echo "Contraseña: admin123\n";
    } else {
        // Crear el usuario administrador
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (nombre, email, password, role, exam_status, created_at) VALUES (?, ?, ?, 'admin', 'allowed', NOW())");
        $stmt->execute([$admin_name, $admin_email, $hashed_password]);
        
        echo "¡Usuario administrador creado exitosamente!\n";
        echo "Credenciales de acceso:\n";
        echo "Email: " . $admin_email . "\n";
        echo "Contraseña: " . $admin_password . "\n";
        echo "\nAhora puedes iniciar sesión con estas credenciales para acceder al panel de administración.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nAsegúrate de que:\n";
    echo "1. La base de datos 'exam_system' existe\n";
    echo "2. Las tablas están creadas correctamente\n";
    echo "3. El servidor MySQL está ejecutándose\n";
}
?>