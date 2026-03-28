<?php
    session_start();
    require __DIR__ . '/../config/database.php';

    $nombre   = $_POST['nombre'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $telefono = $_POST['telefono'];

    if (empty($nombre) || empty($email) || empty($password) || empty($telefono)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../views/register.php");
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "El email no es válido.";
        header("Location: ../views/register.php");
        exit();
    } else if (strlen($password) < 6) {
        $_SESSION['error'] = "La contraseña debe tener al menos 6 caracteres.";
        header("Location: ../views/register.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "El email ya está registrado.";
        header("Location: ../views/register.php");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, telefono) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $hashed_password, $telefono]);

        $_SESSION['exito'] = "Cuenta creada exitosamente.";
        header("Location: ../views/login.php");
        exit();
    } catch (PDOException $e) {
        die("Error al registrar: " . $e->getMessage());
    }
?>