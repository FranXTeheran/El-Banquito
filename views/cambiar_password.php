<?php
session_start();
require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$mensaje_error = '';
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $password_confirmar = $_POST['password_confirmar'];

    // Obtener usuario actual
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $user = $stmt->fetch();

    if (!password_verify($password_actual, $user['password'])) {
        $mensaje_error = "La contraseña actual es incorrecta.";
    } else if (strlen($password_nueva) < 6) {
        $mensaje_error = "La nueva contraseña debe tener al menos 6 caracteres.";
    } else if ($password_nueva !== $password_confirmar) {
        $mensaje_error = "Las contraseñas no coinciden.";
    } else {
        $hashed = password_hash($password_nueva, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ?, password_temporal = 0 WHERE id = ?");
        $stmt->execute([$hashed, $_SESSION['usuario_id']]);
        $mensaje_exito = "Contraseña actualizada correctamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguridad | El Banquito</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-poppins">

    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-2">Cambia tu contraseña</h2>
        <p class="text-gray-400 text-sm mb-6">Por seguridad debes cambiar tu contraseña temporal.</p>

        <?php if ($mensaje_error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                <?php echo $mensaje_error; ?>
            </div>
        <?php endif; ?>

        <?php if ($mensaje_exito): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
                <?php echo $mensaje_exito; ?>
                <a href="dashboard.php" class="font-bold underline ml-2">Ir al dashboard</a>
            </div>
        <?php endif; ?>

        <form method="POST" class="grid gap-4">
            <div class="grid gap-2">
                <label class="font-semibold text-sm">Contraseña actual</label>
                <input class="border p-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" type="password" name="password_actual" required>
            </div>
            <div class="grid gap-2">
                <label class="font-semibold text-sm">Nueva contraseña</label>
                <input class="border p-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" type="password" name="password_nueva" required>
            </div>
            <div class="grid gap-2">
                <label class="font-semibold text-sm">Confirmar nueva contraseña</label>
                <input class="border p-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" type="password" name="password_confirmar" required>
            </div>
            <button class="w-full bg-gradient-to-br from-blue-600 to-purple-700 text-white py-2 px-4 rounded-xl text-sm hover:opacity-90 transition" type="submit">
                Actualizar contraseña
            </button>
        </form>
    </div>

</body>
</html>