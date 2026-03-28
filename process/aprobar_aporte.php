<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../config/twilio.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 'admin') {
    header("Location: ../views/login.php");
    exit();
}

$aporte_id = $_POST['aporte_id'];
$accion = $_POST['accion'];

$stmt = $pdo->prepare("
    SELECT a.*, u.nombre, u.telefono 
    FROM aportes a
    JOIN usuarios u ON a.usuario_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$aporte_id]);
$aporte = $stmt->fetch();

if (!$aporte) {
    $_SESSION['error'] = "Aporte no encontrado.";
    header("Location: ../views/admin.php");
    exit();
}

if ($accion == 'aprobar') {
    $stmt = $pdo->prepare("UPDATE aportes SET estado = 'aprobado' WHERE id = ?");
    $stmt->execute([$aporte_id]);

    try {
        $telefono = '+57' . $aporte['telefono'];
        $mensaje = "✅ FamilyBank: Hola {$aporte['nombre']}, tu aporte de $" . number_format($aporte['monto'], 2, ',', '.') . " fue aprobado. ¡Gracias!";
        enviarSMS($telefono, $mensaje);
        $_SESSION['exito'] = "Aporte aprobado y SMS enviado.";
    } catch (Exception $e) {
        $_SESSION['exito'] = "Aporte aprobado. (SMS no enviado)";
    }

} else if ($accion == 'rechazar') {
    $stmt = $pdo->prepare("UPDATE aportes SET estado = 'rechazado' WHERE id = ?");
    $stmt->execute([$aporte_id]);

    try {
        $telefono = '+57' . $aporte['telefono'];
        $mensaje = "❌ FamilyBank: Hola {$aporte['nombre']}, tu aporte de $" . number_format($aporte['monto'], 2, ',', '.') . " fue rechazado. Contáctanos para más info.";
        enviarSMS($telefono, $mensaje);
        $_SESSION['exito'] = "Aporte rechazado y SMS enviado.";
    } catch (Exception $e) {
        $_SESSION['exito'] = "Aporte rechazado. (SMS no enviado)";
    }
}

header("Location: ../views/admin.php");
exit();
?>