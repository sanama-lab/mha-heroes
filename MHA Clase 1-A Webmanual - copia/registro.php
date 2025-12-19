<?php
require 'db.php'; // Conexión a la base de datos

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email  = $_POST['email'];
    $pass   = $_POST['pass'];

    // 1. ENCRIPTAR CONTRASEÑA
    $pass_encriptada = password_hash($pass, PASSWORD_DEFAULT);

    // 2. PREPARAR LA INSERCIÓN 
    // Usamos `` para asegurar que MySQL lea bien los nombres con espacios o ñ
    $query = "INSERT INTO `usuario` (`nombre`, `mail`, `contraseña`) VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sss", $nombre, $email, $pass_encriptada);

        // 3. EJECUTAR Y VERIFICAR
        try {
            if ($stmt->execute()) {
                $mensaje = "✅ Registro exitoso. <a href='login.php'>Ir al Login</a>";
            }
        } catch (Exception $e) {
            // Error común: correo duplicado
            $mensaje = "❌ Error: El correo ya está registrado o hay un problema con la tabla.";
        }
        $stmt->close();
    } else {
        // Este error saldrá si la tabla 'usuario' realmente no existe
        $mensaje = "❌ Error en la base de datos: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Clase 1-A</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>
    <header>
        <img src="download-removebg-preview.png" alt="Logo de la U.A." class="logo">
        <h1>Registro de Héroe</h1>
        <p>Únete a la Clase 1-A y forja tu destino.</p>
        <h1><?php echo $mensaje; ?></h1>
    </header>

    <main class="form-container">
        <form action="registro.php" method="post" class="register-form">
            <h2>Crear una cuenta</h2>

            <label for="nombre">Nombre completo:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej. Izuku Midoriya" required>

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" placeholder="ejemplo@ua.edu" required>

            <label for="pass">Contraseña:</label>
            <input type="password" id="pass" name="pass" placeholder="••••••••" required>

            <button type="submit">Registrarse</button>

            <p class="alt-link">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>.
            </p>
        </form>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <div class="back-btn">
        <a href="index.php">← Regresar a la página principal</a>
        </div>
    </main>

    <footer>
        <p>Hecho por un fan para la Clase 1-A. ¡Plus Ultra!</p>
    </footer>
</body>
</html>
