<?php
require 'db.php';
session_start();


if (!isset($_SESSION['id'])){

    header("location:index.php");
    exit();
}

$query = "SELECT c.*, u.nombre as autor, cat.categ as categoria_nombre, i.img 
          FROM contenido c
          JOIN usuario u ON c.creadorpost = u.idusuario
          JOIN categoria cat ON c.categoria = cat.idcategoria
          LEFT JOIN imagen i ON c.idcontenido = i.cont
          ORDER BY c.fechapublicacion DESC";
$resultado = $conn->query($query);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_SESSION['user'];
    $idpost  = $_POST['idpost'];
    $mensaje   = $_POST['msgusuario'];

    // 2. PREPARAR LA INSERCIÓN 
    // Usamos `` para asegurar que MySQL lea bien los nombres con espacios o ñ
    $query = "INSERT INTO `comentario` (`idpost`, `mensaje`,`creadorcom`, `fecha`) VALUES (?, ?, ?, now())";
    
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("iss", $idpost, $mensaje, $nombre);

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


<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Foro Multimedia</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>


    <header>
        <div class="header-content">
            <h1>Foro Multimedia</h1>
            <form id="search-form">
                <input type="text" placeholder="Buscar contenido..." aria-label="Buscar">
                <button type="submit">Buscar</button>
            </form>
    </header>


    <main class="main-container">
        <section id="publish-section">
            <?php while($post = $resultado->fetch_assoc()): ?>
            <article class="post-card">
                <div class="post-meta">
                    Por <strong><?php echo htmlspecialchars($post['autor']); ?></strong> 
                    el <?php echo date("d/m/Y H:i", strtotime($post['fechapublicacion'])); ?> | 
                    <span class="categoria-badge"><?php echo htmlspecialchars($post['categoria_nombre']); ?></span>
                </div>

                <h2><?php echo htmlspecialchars($post['titulo']); ?></h2>

                <?php if ($post['img']): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($post['img']); ?>" class="post-img">
                <?php endif; ?>

                <p style="margin-top: 15px;">
                    <?php echo nl2br(htmlspecialchars($post['informacion'])); ?>
                </p>

                <div class="post-meta" style="font-style: italic;">
                    Palabras clave: <?php echo htmlspecialchars($post['palabraskey']); ?>
                </div>
                <?php if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'moderador'): ?>
                <div class="post-actions">
                    <a href="eliminar_foro.php?id=<?php echo $post['idcontenido']; ?>" class="delete-btn" 
                       onclick="return confirm('¿Estás seguro de que deseas eliminar este post?');">
                       Eliminar Post
                    </a>
                </div>

                <?php endif; ?>
                <div class="comment-section">
                    <h3>Deja un comentario</h3>
                    <form method="POST" class="comment-form" action="foro.php">
                        <input type="hidden" name="idpost" value="<?php echo $post['idcontenido']; ?>">
                        <textarea name="msgusuario" placeholder="Escribe tu comentario aquí..." required></textarea>
                        <button type="submit">Publicar Comentario</button>
                    </form>
            </article>
            <?php endwhile; ?>

    </main>
    </main>
    <footer>
        <p>© 2025 Foro Multimedia</p>
    </footer>

</body>
</html>
