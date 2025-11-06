<?php

require_once __DIR__ . '/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nick = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare("
            SELECT u.idusuario, u.nick, c.`contraseña` AS pass
            FROM usuario u
            JOIN cuenta c ON u.cuenta = c.idcuenta
            WHERE u.nick = ?
        ");
        $stmt->execute([$nick]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['pass'])) {
            $_SESSION['user_id'] = $user['idusuario'];
            $_SESSION['nick'] = $user['nick'];
            header('Location: index.html');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    } catch (PDOException $e) {
        $error = 'Error al iniciar sesión';
        // opcional: registrar $e->getMessage() en un log, no mostrar en producción
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Clase 1-A</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <img src="download-removebg-preview.png" alt="Logo de la U.A." class="logo">
        <h1>Inicio de Sesión</h1>
        <p>Bienvenido de nuevo, héroe en formación.</p>
    </header>

    <main class="form-container">
        <form action="login.php" method="post" class="login-form">
            <h2>Inicia sesión</h2>

            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" placeholder="Tu nombre de héroe" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>

            <button type="submit">Entrar</button>

            <p class="alt-link">
                ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>.
            </p>
        </form>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
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
