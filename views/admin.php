<?php
session_start();
require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Fondo común
$stmt = $pdo->query("SELECT SUM(monto) as total FROM aportes WHERE estado = 'aprobado'");
$fondo = $stmt->fetch();
$total_fondo = $fondo['total'] ?? 0;

// Aportes pendientes
$stmt = $pdo->query("
    SELECT a.id, a.monto, a.fecha, u.nombre, u.telefono
    FROM aportes a
    JOIN usuarios u ON a.usuario_id = u.id
    WHERE a.estado = 'pendiente'
    ORDER BY a.fecha DESC
");
$pendientes = $stmt->fetchAll();

// Estado del grupo esta semana
$semana_actual = date('W');
$año_actual = date('Y');
$fecha_actual = date('d/m/Y');

$stmt = $pdo->prepare("
    SELECT u.nombre, a.monto, a.estado
    FROM usuarios u
    LEFT JOIN aportes a ON u.id = a.usuario_id 
        AND a.semana = ? AND a.año = ?
    WHERE u.rol = 'miembro'
    ORDER BY u.nombre
");
$stmt->execute([$semana_actual, $año_actual]);
$estado_grupo = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | El Banquito</title>
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

    <!-- Navbar -->
    <header class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-700 bg-clip-text text-transparent">El Banquito</span>
                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full font-medium">Admin</span>
            </div>
            <a href="../logout.php" class="text-sm font-medium text-red-600 hover:text-red-700 hover:underline">Cerrar sesión</a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-1">Panel de administrador</h1>
        <p class="text-gray-500 text-sm mb-8"><?php echo $fecha_actual; ?></p>

        <!-- Fondo común -->
        <section class="rounded-2xl shadow-lg bg-gradient-to-br from-green-500 to-teal-600 text-white p-8 mb-8">
            <p class="text-sm font-medium text-white/80 mb-1">Fondo común del grupo</p>
            <p class="text-4xl font-light tracking-tight">$ <?php echo number_format($total_fondo, 2, ',', '.'); ?></p>
        </section>

        <!-- Aportes pendientes -->
        <section class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold">Aportes pendientes de aprobación</h2>
                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full"><?php echo count($pendientes); ?> pendientes</span>
            </div>
            <ul class="divide-y divide-gray-100">
                <?php if (count($pendientes) == 0): ?>
                    <li class="px-6 py-4 text-sm text-gray-500 text-center">No hay aportes pendientes.</li>
                <?php else: ?>
                    <?php foreach ($pendientes as $p): ?>
                        <li class="px-6 py-4 flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <p class="font-medium text-sm"><?php echo htmlspecialchars($p['nombre']); ?></p>
                                <p class="text-xs text-gray-400"><?php echo date('d/m/Y H:i', strtotime($p['fecha'])); ?> — Tel: <?php echo $p['telefono']; ?></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-gray-700">$ <?php echo number_format($p['monto'], 2, ',', '.'); ?></span>
                                <form action="../process/aprobar_aporte.php" method="POST" class="inline">
                                    <input type="hidden" name="aporte_id" value="<?php echo $p['id']; ?>">
                                    <input type="hidden" name="accion" value="aprobar">
                                    <button class="bg-green-500 hover:bg-green-600 text-white text-xs px-4 py-2 rounded-lg transition" type="submit">✅ Aprobar</button>
                                </form>
                                <form action="../process/aprobar_aporte.php" method="POST" class="inline">
                                    <input type="hidden" name="aporte_id" value="<?php echo $p['id']; ?>">
                                    <input type="hidden" name="accion" value="rechazar">
                                    <button class="bg-red-500 hover:bg-red-600 text-white text-xs px-4 py-2 rounded-lg transition" type="submit">❌ Rechazar</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </section>

            <!-- Crear nuevo miembro -->
    <section class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-8">
        <h2 class="font-semibold mb-1">Agregar nuevo miembro</h2>
        <p class="text-sm text-gray-500 mb-4">El miembro recibirá un SMS con sus credenciales</p>

        <?php if (isset($_SESSION['exito'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
                <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="../process/crear_miembro.php" method="POST" class="grid sm:grid-cols-2 gap-4">
            <div class="grid gap-2">
                <label class="font-semibold text-sm">Nombre completo</label>
                <input class="border p-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="nombre" placeholder="Juan García" required>
            </div>
            <div class="grid gap-2">
                <label class="font-semibold text-sm">Email</label>
                <input class="border p-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" type="email" name="email" placeholder="juan@gmail.com" required>
            </div>
            <div class="grid gap-2">
                <label class="font-semibold text-sm">Teléfono</label>
                <input class="border p-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" type="number" name="telefono" placeholder="3017963014" required>
            </div>
            <div class="grid gap-2">
                <label class="font-semibold text-sm">Contraseña temporal</label>
                <input class="border p-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="password" placeholder="Ej: Banco2024" required>
            </div>
            <div class="sm:col-span-2">
                <button class="w-full bg-gradient-to-br from-blue-600 to-purple-700 text-white py-2 px-4 rounded-xl text-sm hover:opacity-90 transition" type="submit">
                    Crear miembro y enviar SMS
                </button>
            </div>
        </form>
    </section>

        <!-- Estado del grupo esta semana -->
        <section class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold">Estado del grupo esta semana</h2>
            </div>
            <ul class="divide-y divide-gray-100">
                <?php foreach ($estado_grupo as $miembro): ?>
                    <li class="px-6 py-4 flex justify-between items-center">
                        <span class="text-sm font-medium"><?php echo htmlspecialchars($miembro['nombre']); ?></span>
                        <?php if ($miembro['monto'] && $miembro['estado'] == 'aprobado'): ?>
                            <span class="text-sm text-green-600 font-semibold">✅ $ <?php echo number_format($miembro['monto'], 2, ',', '.'); ?></span>
                        <?php elseif ($miembro['monto'] && $miembro['estado'] == 'pendiente'): ?>
                            <span class="text-sm text-yellow-500 font-semibold">⏳ Pendiente $ <?php echo number_format($miembro['monto'], 2, ',', '.'); ?></span>
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