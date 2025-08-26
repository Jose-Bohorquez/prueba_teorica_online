<?php
session_start();
include("../config/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST["nombre"]);
    $email    = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        $error = "El correo ya está registrado.";
    } else {
        // Insertar nuevo usuario
        $stmt = $pdo->prepare("
            INSERT INTO users (nombre, email, password, role) 
            VALUES (:nombre, :email, :password, 'estudiante')
        ");
        $stmt->execute([
            'nombre'   => $nombre,
            'email'    => $email,
            'password' => $password
        ]);

        // Guardar datos en la sesión
        $_SESSION['user_id']   = $pdo->lastInsertId();
        $_SESSION['user_name'] = $nombre;
        $_SESSION['role']      = "estudiante";

        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Sistema de Exámenes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-96">
        <h1 class="text-2xl font-bold text-center mb-6">Registrarse</h1>
        <?php if (!empty($error)): ?>
            <p class="bg-red-100 text-red-700 p-3 rounded-lg mb-4"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST" class="space-y-4">
            <input type="text" name="nombre" placeholder="Nombre completo" required
                class="w-full p-3 border rounded-xl focus:ring focus:ring-blue-400">
            <input type="email" name="email" placeholder="Correo electrónico" required
                class="w-full p-3 border rounded-xl focus:ring focus:ring-blue-400">
            <input type="password" name="password" placeholder="Contraseña" required
                class="w-full p-3 border rounded-xl focus:ring focus:ring-blue-400">
            <button type="submit"
                class="bg-green-600 text-white w-full py-3 rounded-xl hover:bg-green-700 transition">
                Registrarse
            </button>
        </form>
        <p class="mt-4 text-center">¿Ya tienes cuenta?
            <a href="index.php" class="text-blue-600 hover:underline">Inicia sesión</a>
        </p>
    </div>
</body>
</html>
