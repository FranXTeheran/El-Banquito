<?php
session_start();
require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT monto, estado, fecha, semana, año
    FROM aportes 
    WHERE usuario_id = ?
    ORDER BY fecha DESC
");
$stmt->execute([$_SESSION['usuario_id']]);
$historial = $stmt->fetchAll();