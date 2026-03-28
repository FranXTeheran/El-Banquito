<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | El Banquito</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-poppins">

    <div class="flex flex-col md:flex-row w-full md:w-[900px] rounded-2xl shadow-2xl overflow-hidden">
        
        <!-- Columna izquierda - imagen -->
        <div class="hidden md:flex md:w-1/2 relative flex-col justify-end text-white min-h-[500px] overflow-hidden">
            <img src="../img/Imagen-mujer.png" class="absolute inset-0 w-full h-full object-cover" alt="FamilyBank">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-purple-700/60"></div>
            <div class="relative z-10 p-12">
                <h1 class="text-3xl font-light mb-4">¿Sabes cuánto tienes ahorrado?</h1>
                <p class="text-sm font-light text-gray-200 mb-4">FamilyBank te ayuda a controlar las finanzas de tu familia en un solo lugar. Registra ingresos, realiza transferencias y mantén el control de cada peso.</p>
                <p class="text-xs text-gray-300 font-semibold">Más de 10,000 familias ya confían en nosotros.</p>
            </div>
        </div>

        <!-- Columna derecha - formulario -->
        <div class="w-full md:w-1/2 bg-white p-8 md:p-16 text-gray-800">
            <h2 class="text-3xl font-bold mb-2">Regístrate Ya</h2>
            <p class="text-gray-400 text-sm mb-6">Crea tu cuenta totalmente gratis</p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form class="grid gap-3" action="../process/register_process.php" method="POST">
                <label class="font-bold text-sm">Nombre</label>
                <input class="focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none border p-2 rounded-xl text-sm" type="text" name="nombre" placeholder="Ingresa tu nombre">
                
                <label class="font-bold text-sm">Email</label>
                <input class="focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none border p-2 rounded-xl text-sm" type="email" name="email" placeholder="Ejemplo@correo.com">
                
                <label class="font-bold text-sm">Contraseña</label>
                <div class="relative">
                    <input id="password" class="focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none border p-2 rounded-xl text-sm w-full pr-10" type="password" name="password" placeholder="********">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            👁
                        </button>
                </div>
                
                <label class="font-bold text-sm">Teléfono</label>
                <input class="focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none border p-2 rounded-xl text-sm" type="number" name="telefono" placeholder="3211234567">

                <button class="w-full bg-gradient-to-br from-blue-600 to-purple-700 p-2 text-white rounded-xl text-sm mt-2 hover:opacity-90 transition" type="submit"><a href="login.php">Registrarse</a></button>
                
                <p class="text-sm text-center">¿Ya tienes una cuenta? <a href="login.php" class="text-sky-600 font-medium hover:underline">Iniciar sesión</a></p>
            </form>
        </div>

    </div>

</body>
</html>