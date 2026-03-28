<?php
session_start();
require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Mi ahorro personal
$stmt = $pdo->prepare("SELECT SUM(monto) as total FROM aportes WHERE usuario_id = ? AND estado = 'aprobado'");
$stmt->execute([$_SESSION['usuario_id']]);
$miAhorro = $stmt->fetch();
$balance = $miAhorro['total'] ?? 0;

// Fondo común
$stmt = $pdo->query("SELECT SUM(monto) as total FROM aportes WHERE estado = 'aprobado'");
$fondo = $stmt->fetch();
$total_fondo = $fondo['total'] ?? 0;

// Aportes de esta semana
$fecha_actual = date('d/m/Y');
$semana_actual = date('W');
$año_actual = date('Y');

$stmt = $pdo->prepare("
    SELECT u.nombre, a.monto, a.fecha, a.estado
    FROM usuarios u
    LEFT JOIN aportes a ON u.id = a.usuario_id 
        AND a.semana = ? AND a.año = ?
    ORDER BY u.nombre
");
$stmt->execute([$semana_actual, $año_actual]);
$estado_aportes = $stmt->fetchAll();

// Verificar si el usuario ya aportó esta semana
$stmt = $pdo->prepare("SELECT id FROM aportes WHERE usuario_id = ? AND semana = ? AND año = ? AND estado = 'aprobado'");
$stmt->execute([$_SESSION['usuario_id'], $semana_actual, $año_actual]);
$ya_aporte = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel | El Banquito</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { poppins: ['Poppins', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen font-poppins text-gray-800">

    <!-- Barra superior -->
    <header class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-700 bg-clip-text text-transparent">El Banquito</span>
                <span class="text-sm text-gray-400 hidden sm:inline">|</span>
                <span class="text-sm text-gray-600">Hola, <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></strong></span>
            </div>
            <a href="../logout.php" class="text-sm font-medium text-red-600 hover:text-red-700 hover:underline">Cerrar sesión</a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-1">Tu panel</h1>
        <p class="text-gray-500 text-sm mb-8">Resumen de tu cuenta familiar</p>

    <!-- Tarjetas superiores -->
    <div class="grid sm:grid-cols-2 gap-6 mb-8">
        
        <!-- Mi ahorro -->
        <section class="rounded-2xl shadow-lg bg-gradient-to-br from-blue-600 to-purple-700 text-white p-8">
            <p class="text-sm font-medium text-white/80 mb-1">Mi ahorro acumulado</p>
            <p class="text-4xl font-light tracking-tight">$ <?php echo number_format($balance, 2, ',', '.'); ?></p>
        </section>

        <!-- Fondo común -->
        <section class="rounded-2xl shadow-lg bg-gradient-to-br from-green-500 to-teal-600 text-white p-8">
            <p class="text-sm font-medium text-white/80 mb-1">Fondo común del grupo</p>
            <p class="text-4xl font-light tracking-tight">$ <?php echo number_format($total_fondo, 2, ',', '.'); ?></p>
        </section>

    </div>

    <!-- Aporte semanal -->
    <section class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-8">
        <h2 class="font-semibold mb-1">Aporte de esta semana</h2>
        <p class="text-sm text-gray-500 mb-4"><?php echo $fecha_actual; ?></p>


        <?php if ($ya_aporte): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                ✅ Ya realizaste tu aporte esta semana. ¡Gracias!
            </div>
        <?php else: ?>
            <form action="../process/aporte_process.php" method="POST" class="flex gap-3">
                <input class="border p-2 rounded-xl text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       type="number" name="monto" placeholder="$ Monto a aportar" min="1" step="0.01" required>
                <button class="bg-gradient-to-br from-blue-600 to-purple-700 text-white px-6 py-2 rounded-xl text-sm hover:opacity-90 transition" 
                        type="submit">Aportar</button>
            </form>
        <?php endif; ?>
    </section>

    <!-- Estado aportes del grupo -->
    <section class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold">Estado del grupo esta semana</h2>
        </div>
        <ul class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
            <?php foreach ($estado_aportes as $aporte): ?>
                <li class="px-6 py-4 flex justify-between items-center">
                    <span class="text-sm font-medium"><?php echo htmlspecialchars($aporte['nombre']); ?></span>
                    <?php if ($aporte['monto'] && $aporte['estado'] == 'aprobado'): ?>
        <span class="text-sm text-green-600 font-semibold">✅ $ <?php echo number_format($aporte['monto'], 2, ',', '.'); ?></span>
            <?php elseif ($aporte['monto'] && $aporte['estado'] == 'pendiente'): ?>
                <span class="text-sm text-yellow-500 font-semibold">⏳ Pendiente</span>
            <?php else: ?>
                <span class="text-sm text-red-400">❌ Sin aportar</span>
            <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
    </section>


</main>

</body>
</html>