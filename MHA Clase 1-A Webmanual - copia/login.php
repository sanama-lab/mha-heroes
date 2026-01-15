<?php
require 'db.php';
session_start();
include 'escudo.php';
// Línea extra de seguridad para que la "ñ" de la DB no falle
$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificamos que los nombres coincidan con el 'name' de los inputs
    $email = $_POST['email'];
    $pass  = $_POST['pass']; // CAMBIADO: de 'contraseña' a 'pass'

    // 1. Buscamos al usuario por su EMAIL (tu columna es 'mail')
    // Usamos backticks `` para la columna contraseña por la ñ
    $stmt = $conn->prepare("SELECT idusuario, nombre, tipousuario, `contrasena` FROM usuario WHERE mail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($usuario = $resultado->fetch_assoc()) {
        // 2. VERIFICAR CONTRASEÑA
        if (password_verify($pass, $usuario['contrasena'])) {
            $_SESSION['id']   = $usuario['idusuario'];
            $_SESSION['user'] = $usuario['nombre'];
            $_SESSION['rol']  = $usuario['tipousuario'];
            
            // Guardar IP después de verificar credenciales (con prepared statement)
            $ip_actual = $_SERVER['REMOTE_ADDR'];
            $id_user = $usuario['idusuario'];
            $stmt_ip = $conn->prepare("UPDATE usuario SET ultima_ip = ? WHERE idusuario = ?");
            $stmt_ip->bind_param("si", $ip_actual, $id_user);
            $stmt_ip->execute();
            
            header("Location: index.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }   
    } else {
        $error = "El correo no está registrado.";
    }
}
?>
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
        <h1>Inicia sesión en la Clase 1-A</h1>
        <p>Accede a tu cuenta de héroe.</p>
    </header>
<main class="form-container">
    <form action="login.php" method="post" class="login-form">
        <h2>Inicia sesión</h2>

        <label for="email">Ingresa tu Gmail:</label>
        <input type="email" id="email" name="email" placeholder="tu super gmail" required>

        <label for="pass">Contraseña:</label>
        <input type="password" id="pass" name="pass" placeholder="••••••••" required>

        <button type="submit">Entrar</button>

        <p class="alt-link">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>.
        </p>
    </form>

    <?php if (isset($error)): ?>
        <p style="color: red; background: #ffe6e6; padding: 10px; border-radius: 5px;">
            <?php echo $error; ?>
        </p>
    <?php endif; ?>
</main>
</body>
</html>