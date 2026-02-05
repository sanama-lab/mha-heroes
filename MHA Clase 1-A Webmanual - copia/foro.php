<?php
require 'db.php';
session_start();

if (!isset($_SESSION['id'])){
    header("location:index.php");
    exit();
}

// --- LÓGICA DE FILTRADO ---
$categoria_id = isset($_GET['cat']) ? $_GET['cat'] : null;
$post_especifico_id = isset($_GET['idpost']) ? $_GET['idpost'] : null;

// Base de la consulta
$query_base = "SELECT c.*, u.nombre as autor, cat.categ as categoria_nombre, i.img 
               FROM contenido c
               JOIN usuario u ON c.creadorpost = u.idusuario
               JOIN categoria cat ON c.categoria = cat.idcategoria
               LEFT JOIN imagen i ON c.idcontenido = i.cont";

// 1. Si hay un post específico seleccionado
if ($post_especifico_id) {
    $query = $query_base . " WHERE c.idcontenido = " . intval($post_especifico_id);
} 
// 2. Si hay una categoría seleccionada
elseif ($categoria_id) {
    $query = $query_base . " WHERE c.categoria = " . intval($categoria_id);
} 
// 3. Vista general (todos los posts)
else {
    $query = $query_base . " ORDER BY c.fechapublicacion DESC";
}

$resultado = $conn->query($query);

// --- LÓGICA DE INSERCIÓN DE COMENTARIOS (Tu código original) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['msgusuario'])) {
    $nombre = $_SESSION['user'];
    $idpost  = $_POST['idpost'];
    $mensaje_txt = $_POST['msgusuario'];

    $query_ins = "INSERT INTO comentario (idpost, mensaje,creadorcom, fecha) VALUES (?, ?, ?, now())";
    $stmt = $conn->prepare($query_ins);

    if ($stmt) {
        $stmt->bind_param("iss", $idpost, $mensaje_txt, $nombre);
        try {
            if ($stmt->execute()) {
                $status_msg = "✅ Comentario publicado.";
            }
        } catch (Exception $e) {
            $status_msg = "❌ Error al publicar.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Foro Multimedia</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <div class="header-content">
            <h1><a href="foro.php" style="color:white; text-decoration:none;">Foro Multimedia</a></h1>
            <div class="auth-buttons"><a href="index.php"> < inicio</a></div>
            <form id="search-form">
                <input type="text" placeholder="Buscar contenido...">
                <button type="submit">Buscar</button>
            </form>
        </div>
    </header>

    <main class="main-container">
        
        <?php if (isset($status_msg)) echo "<p>$status_msg</p>"; ?>

        <section id="publish-section">
            <?php while($post = $resultado->fetch_assoc()): ?>
                
                <article class="post-card">
                    <div class="post-meta">
                        Por <strong><?php echo htmlspecialchars($post['autor']); ?></strong> 
                        el <?php echo date("d/m/Y H:i", strtotime($post['fechapublicacion'])); ?> | 
                        <a href="?cat=<?php echo $post['categoria']; ?>" class="categoria-badge">
                            <?php echo htmlspecialchars($post['categoria_nombre']); ?>
                        </a>
                    </div>

                    <h2>
                        <?php if (!$post_especifico_id): ?>
                            <a href="?idpost=<?php echo $post['idcontenido']; ?>">
                                <?php echo htmlspecialchars($post['titulo']); ?>
                            </a>
                        <?php else: ?>
                            <?php echo htmlspecialchars($post['titulo']); ?>
                        <?php endif; ?>
                    </h2>

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
                               onclick="return confirm('¿Estás seguro?');">Eliminar Post</a>
                        </div>
                    <?php endif; ?>

                    <?php if ($post_especifico_id): ?>
                        <div class="comment-section">
                            <h3>Comentarios</h3>
                            <form method="POST" class="comment-form">
                                <input type="hidden" name="idpost" value="<?php echo $post['idcontenido']; ?>">
                                <textarea name="msgusuario" placeholder="Escribe tu comentario aquí..." required></textarea>
                                <button type="submit">Publicar Comentario</button>
                            </form>
                            <br>
                            <a href="foro.php">← Volver al inicio</a>
                        </div>
                    <?php endif; ?>
                </article>

            <?php endwhile; ?>
        </section>
    </main>

    <footer>
        <p>© 2025 Foro Multimedia</p>
    </footer>
</body>
</html>