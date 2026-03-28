<?php
    session_start();
    require __DIR__ . '/../config/database.php';

    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../views/login.php");
        exit();
    }
            
    $monto = $_POST['monto'];
    
    // Validar monto
    if (empty($monto) || $monto <= 0) {
        $_SESSION['error'] = "El monto debe ser mayor a 0.";
        header("Location: ../views/dashboard.php");
        exit();
    }

    // Verificar si ya aportó esta semana
    $semana_actual = date('W');
    $año_actual = date('Y');

    $stmt = $pdo->prepare("SELECT id FROM aportes WHERE usuario_id = ? AND semana = ? AND año = ? AND estado IN ('pendiente', 'aprobado')");
    $stmt->execute([$_SESSION['usuario_id'], $semana_actual, $año_actual]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Ya realizaste tu aporte esta semana.";
        header("Location: ../views/dashboard.php");
        exit();
    }

    // Insertar aporte con estado pendiente
    $stmt = $pdo->prepare("INSERT INTO aportes (usuario_id, monto, semana, año, estado) VALUES (?, ?, ?, ?, 'pendiente')");
    $stmt->execute([$_SESSION['usuario_id'], $monto, $semana_actual, $año_actual]);

    $_SESSION['exito'] = "Aporte registrado. Pendiente de aprobación por el administrador.";
    header("Location: ../views/dashboard.php");
    exit();
    ?>