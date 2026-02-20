<?php
require 'db.php';
session_start();
session_unset();
session_destroy();

// Salida HTML que limpia localStorage y redirige a index.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0;url=index.php">
    <title>Cerrando sesión</title>
</head>
<body>
    <script>
        
        window.location.href = 'index.php';
    </script>
</body>
</html>