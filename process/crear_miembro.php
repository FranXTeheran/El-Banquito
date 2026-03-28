<?php
session_start();
require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    header('Location: ../views/dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/admin.php');
    exit();
}

$nombre   = trim($_POST['nombre'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telefono = trim((string) ($_POST['telefono'] ?? ''));
$password = $_POST['password'] ?? '';

if ($nombre === '' || $email === '' || $telefono === '' || $password === '') {
    $_SESSION['error'] = 'Todos los campos son obligatorios.';
    header('Location: ../views/admin.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'El email no es válido.';
    header('Location: ../views/admin.php');
    exit();
}

if (strlen($password) < 6) {
    $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
    header('Location: ../views/admin.php');
    exit();
}

$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $_SESSION['error'] = 'Ya existe un usuario con ese email.';
    header('Location: ../views/admin.php');
    exit();
}

$digits = preg_replace('/\D/', '', $telefono);
$e164   = null;
if (strlen($digits) === 10 && isset($digits[0]) && $digits[0] === '3') {
    $e164 = '+57' . $digits;
} elseif (strlen($digits) >= 12 && substr($digits, 0, 2) === '57') {
    $e164 = '+' . $digits;
}

if ($e164 === null) {
    $_SESSION['error'] = 'Teléfono no válido para SMS. Usa 10 dígitos móvil CO (ej: 3012345678) o número completo con código 57.';
    header('Location: ../views/admin.php');
    exit();
}

$hashed = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare(
        'INSERT INTO usuarios (nombre, email, password, telefono, rol, password_temporal) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$nombre, $email, $hashed, $telefono, 'miembro', 1]);
} catch (PDOException $e) {
    $_SESSION['error'] = 'No se pudo crear el miembro. ¿Existen las columnas rol y password_temporal? ' . $e->getMessage();
    header('Location: ../views/admin.php');
    exit();
}

require_once __DIR__ . '/../config/twilio.php';

$mensaje = 'FamilyBank — Credenciales. Email: ' . $email . ' | Contraseña temporal: ' . $password . ' | Cambia tu contraseña al iniciar sesión.';

try {
    enviarSMS($e164, $mensaje);
    $_SESSION['exito'] = 'Miembro creado. SMS enviado correctamente.';
} catch (Throwable $e) {
    $_SESSION['exito'] = 'Miembro guardado en el sistema.';
    $_SESSION['error'] = 'No se pudo enviar el SMS: ' . $e->getMessage();
}

header('Location: ../views/admin.php');
exit();