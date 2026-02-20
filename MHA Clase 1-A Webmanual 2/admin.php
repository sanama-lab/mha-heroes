<?php
require 'db.php';
session_start();

if (!isset($_SESSION['rol']) || ($_SESSION['rol']) != "admin" && ($_SESSION['rol']) != "moderador" ){
    header("location:index.php");
    exit();
}

// Consultamos también la 'ultima_ip' para poder banearla
$query = "SELECT idusuario, nombre, mail, tipousuario, ultima_ip FROM usuario ORDER BY idusuario ASC";
$resultado = $conn->query($query);

// ver que usuarios ya están baneados para mostrar el boton de desbaneos solo a los que realmente estan baneados
$query_baneados = "SELECT idusuario FROM baneos WHERE fecha_fin > NOW()";  
$result_baneados = $conn->query($query_baneados);
$baneados = [];
while ($row_ban = $result_baneados->fetch_assoc()) {
    $baneados[] = $row_ban['idusuario'];
}

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
    <div class="auth-buttons"><a href="index.php"> < inicio</a></div>
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
            <tbody>
                <?php while($row = $resultado->fetch_assoc()): ?>
                    <?php if ($row['tipousuario'] !== 'admin') { ?>
                <tr>
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
                        
                        <a href="banear.php?id=<?php echo $row['idusuario']; ?>" 
                           style="padding: 5px; margin: 5px; background:rgba(135, 135, 135, 0.6); border-radius: 7px; text-decoration: none;"
                           onclick="return confirm('¿Estás seguro de que quieres (banear) a este usuario?'  )">
                           banear 7 dias
                        </a>
                           <!-- El boton de desbaneos se muestra solo si la ip esta registrada y si esta en un estado de ban -->
                        <?php if (!empty($row['ultima_ip']) && in_array($row['idusuario'], $baneados)) { ?>
                        <a href="desbanear.php?id=<?php echo $row['idusuario']; ?>" 
                           style="padding: 5px; margin: 5px; background:rgba(135, 135, 135, 0.6); border-radius: 7px; text-decoration: none;"
                           onclick="return confirm('¿Estás seguro de que quieres desbanear a este usuario?')">
                           desbanear
                        </a>
                        <?php } ?>
                    </td>
                </tr> 
                   <?php } ?>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>