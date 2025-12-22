<?php

require 'db.php';
session_start();


if (!isset($_SESSION['rol']) || ($_SESSION['rol']) != "admin" && ($_SESSION['rol']) != "moderador" ){

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
                    <th style="padding: 5px;">Acciones</th>
                </tr>
            </thead>
            <tbody >
                <?php while($row = $resultado->fetch_assoc()): ?>
                    <?php if ($row['tipousuario'] !== 'admin') { ?>
                <tr >
                    <td style="padding: 5px;"><?php echo $row['idusuario']; ?></td>
                    <td style="padding: 5px;"><?php echo htmlspecialchars($row['nombre']); ?></td> 
                    <td style="padding: 5px;"><?php echo htmlspecialchars($row['mail']); ?></td>
                    <td>
                        <span class="<?php echo ($row['tipousuario'] == 'admin') ? 'admin-tag' : ''; ?>">
                            <?php echo strtoupper($row['tipousuario']); ?>
                        </span>
                    </td>
                    <td style="padding: 5px;">
                        <?php if ($_SESSION['rol'] !== 'moderador') { ?>
                        <a href="cambiar_rol.php?id=<?php echo $row['idusuario']; ?>&rol=moderador" 
                           style="padding: 5px; margin: 5px; background:rgba(135, 135, 135, 0.5); border-radius: 7px; text-decoration: none;">
                           Hacer Moderador
                        </a>
                        <a href="cambiar_rol.php?id=<?php echo $row['idusuario']; ?>&rol=comun" 
                           style="padding: 5px; margin: 5px; background:rgba(135, 135, 135, 0.6); border-radius: 7px; text-decoration: none;">
                           Hacer Usuario
                        </a>
                        <?php } ?>
                        <a href="eliminar_usuario.php?id=<?php echo $row['idusuario']; ?>" 
                           style="padding: 5px; margin: 5px; background:rgba(135, 135, 135, 0.6); border-radius: 7px; text-decoration: none;">
                           hacer polvo
                        </a>
                </tr> 
                   <?php } ?>
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