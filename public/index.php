<?php
session_start();
include("../config/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role']      = $user['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Exámenes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-96">
        <h1 class="text-2xl font-bold text-center mb-6">Iniciar Sesión</h1>
        <?php if (!empty($error)): ?>
            <p class="bg-red-100 text-red-700 p-3 rounded-lg mb-4"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="index.php" method="POST" class="space-y-4">
            <input type="email" name="email" placeholder="Correo electrónico" required
                class="w-full p-3 border rounded-xl focus:ring focus:ring-blue-400">
            <input type="password" name="password" placeholder="Contraseña" required
                class="w-full p-3 border rounded-xl focus:ring focus:ring-blue-400">
            <button type="submit"
                class="bg-blue-600 text-white w-full py-3 rounded-xl hover:bg-blue-700 transition">
                Ingresar
            </button>
        </form>
        <p class="mt-4 text-center">¿No tienes cuenta?
            <a href="register.php" class="text-green-600 hover:underline">Regístrate</a>
        </p>
    </div>
</body>
</html>
