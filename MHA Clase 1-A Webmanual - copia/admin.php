<?php

require 'db.php';
session_start();

if (!isset($_SESSION['rol']) || ($_SESSION['rol']) != "admin"){

    header("location:index.php");
    exit();
}

$query = "SELECT idusuario, nombre, mail, tipousuario FROM usuario ORDER BY idusuario ASC";
$resultado = $conn->query($query);
?>


<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrativo - MHA</title>
    <link rel="stylesheet" href="estilos.css">   
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<header>
    <h1>ADMIN PANEL</h1>
    <div class="auth-buttons">
        <a href="index.php" > < inicio</a>
    </div>
</header>

<main>
    <section>
        <h2>LISTA DE USUARIOS REGISTRADOS</h2>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rango</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['idusuario']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['mail']); ?></td>
                    <td>
                        <span class="<?php echo ($row['tipousuario'] == 'admin') ? 'admin-tag' : ''; ?>">
                            <?php echo strtoupper($row['tipousuario']); ?>
                        </span>
                    </td>
                    <td>
                        <!-- Aquí puedes agregar botones para el cambiar a moderador (viceversa) o eliminar los usuarios -->
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>

<footer>
    <p>PLUS ULTRA - Sistema de Gestión de Héroes</p>
</footer>

</body>
</html>