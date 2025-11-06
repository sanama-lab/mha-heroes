<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nick = $_POST['usuario'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $nombre = $_POST['nombre'];
    
    try {
        // Insertar en tabla cuenta
        $stmt = $pdo->prepare("INSERT INTO cuenta (nusuario, contraseña, gmail) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $password, $email]);
        $idcuenta = $pdo->lastInsertId();
        
        // Insertar en tabla usuario
        $stmt = $pdo->prepare("INSERT INTO usuario (nick, cuenta) VALUES (?, ?)");
        $stmt->execute([$nick, $idcuenta]);
        
        header("Location: login.php?registro=exitoso");
        exit();
    } catch(PDOException $e) {
        $error = "Error al registrar: " . $e->getMessage();
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
    </header>

    <main class="form-container">
        <form action="registro.php" method="post" class="register-form">
            <h2>Crear una cuenta</h2>

            <label for="nombre">Nombre completo:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej. Izuku Midoriya" required>

            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" placeholder="Tu nombre de héroe" required>

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" placeholder="ejemplo@ua.edu" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>

            <button type="submit">Registrarse</button>

            <p class="alt-link">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>.
            </p>
        </form>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <div class="back-btn">
        <a href="index.html">← Regresar a la página principal</a>
        </div>
    </main>

    <footer>
        <p>Hecho por un fan para la Clase 1-A. ¡Plus Ultra!</p>
    </footer>
</body>
</html>
