<?php
    session_start();
    require __DIR__ . '/../config/database.php';

    
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Validar campos
    if(empty($email) || empty($password)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../views/login.php");
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "El email no es válido.";
        header("Location: ../views/login.php");
        exit();
    }

    // 2. Buscar usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]); 
    $user = $stmt->fetch();

    // 3. Verificar si existe
    if (!$user) {
        $_SESSION['error'] = "El usuario no existe.";
        header("Location: ../views/login.php");
        exit();
    }

    // 4. Verificar contraseña
if (password_verify($password, $user['password'])) {
    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['usuario_nombre'] = $user['nombre'];
    $_SESSION['usuario_rol'] = $user['rol'];

    // Verificar si tiene contraseña temporal
    if ($user['password_temporal'] == 1) {
        header("Location: ../views/cambiar_password.php");
        exit();
    }

    // Redirigir según rol
    if ($user['rol'] == 'admin') {
        header("Location: ../views/admin.php");
    } else {
        header("Location: ../views/dashboard.php");
    }
    exit();
} else {
    $_SESSION['error'] = "Email o contraseña incorrectos.";
    header("Location: ../views/login.php");
    exit();
}