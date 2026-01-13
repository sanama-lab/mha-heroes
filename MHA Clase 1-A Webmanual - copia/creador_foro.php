<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";
$query_cat = "SELECT idcategoria, categ FROM categoria ORDER BY categ ASC";
$res_cat = $conn->query($query_cat);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger datos normales del post
    $titulo = $_POST['titulo'];
    $info = $_POST['informacion'];
    $creador = $_SESSION['id'];
    $palabras_clave = $_POST['palabras_clave'];
    $categoria = $_POST['categoria'];

    // 2. Primero insertamos el POST para obtener su ID
    // NOTA: Ajusta esta consulta a todas tus columnas reales
    $query_post = "INSERT INTO contenido (titulo, informacion, creadorpost, palabraskey, categoria, fechaact, fechapublicacion) VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($query_post);
    $stmt->bind_param("ssiss", $titulo, $info, $creador, $palabras_clave, $categoria);

    //  Consultar las categorías disponibles
    $query_cat = "SELECT idcategoria, categ FROM categoria ORDER BY categ ASC";
    $res_cat = $conn->query($query_cat);

    if ($stmt->execute()) {
        //  Obtenemos el ID del post recién creado
        $id_nuevo_post = $conn->insert_id; 

        // 3. PROCESAR LA IMAGEN (Si se subió una)
        // Verificamos si existe el archivo y no hubo errores de subida
        if (isset($_FILES['imagen_subida']) && $_FILES['imagen_subida']['error'] === 0) {
            
            $nombre_foto = basename($_FILES['imagen_subida']['name']);
            $tipo_archivo = $_FILES['imagen_subida']['type'];
            $tamano_archivo = $_FILES['imagen_subida']['size'];
            
            // A) Validaciones de seguridad básicas
            $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
            $tamano_maximo = 2 * 1024 * 1024; // 2MB máximo (ajustalo según tu servidor)

            if (!in_array($tipo_archivo, $tipos_permitidos)) {
                 $mensaje = "Error: Solo se permiten imágenes JPG, PNG o GIF.";
            } elseif ($tamano_archivo > $tamano_maximo) {
                 $mensaje = "Error: La imagen es muy pesada (Máx 2MB).";
            } else {
                // B) Leer el contenido binario del archivo temporal
                // file_get_contents lee el archivo y lo convierte en una cadena de datos crudos
                $datos_binarios = file_get_contents($_FILES['imagen_subida']['tmp_name']);

                // C) Insertar en la tabla 'imagen'
                // Usamos 'b' para indicar que enviamos un BLOB (datos binarios)
                // Usamos $id_nuevo_post para relacionarlo con el contenido
                $query_img = "INSERT INTO imagen (nomfoto, cont, img) VALUES (?, ?, ?)";
                $stmt_img = $conn->prepare($query_img);
                // La 's' es para el nombre (string), la 'i' para el ID (integer), la 'b' para el BLOB
                $stmt_img->bind_param("sib", $nombre_foto, $id_nuevo_post, $datos_binarios);
                
                if (!$stmt_img->execute()) {
                     $mensaje .= " Post creado, pero error al guardar la imagen.";
                }
                $stmt_img->close();
            }
        }

        if ($mensaje == "") {
             header("Location: index.php");
             exit();
        }

    } else {
        $mensaje = "Error al crear el post: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nuevo Post</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h2>Crear Publicación</h2>
        <?php if($mensaje) echo "<p style='color:red'>$mensaje</p>"; ?>
        <div class="auth-buttons">
            <a href="index.php" > < inicio</a>
        </div>
    </header>
    <div class="form-container">
        
        <form method="POST" enctype="multipart/form-data">
            
            <label>Título:</label>
            <input type="text" name="titulo" required>
            
            <label>Palabras Clave:</label>
            <input type="text" name="palabras_clave" required>


            <label for="categoria">Selecciona una Categoría:</label>
            <select name="categoria" id="categoria" required style="width: 100%; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            <option value="">-- Elige una categoría --</option>
            
            <?php while($cat = $res_cat->fetch_assoc()): ?>
            <option value="<?php echo $cat['idcategoria']; ?>" 
            <?php 
                // Si estamos EDITANDO, esto marcará la categoría que ya tenía el post
                if(isset($post) && $post['categoria'] == $cat['idcategoria']) echo "selected"; 
            ?>>
            <?php echo htmlspecialchars($cat['categ']); ?>
            </option>
                <?php endwhile; ?>
            </select>


            <label>Contenido:</label>
            <textarea name="informacion" required></textarea>
            
            <label for="file-upload" style="cursor: pointer; padding: 10px; display: block; margin-top: 10px;">
                📷 Seleccionar Imagen de portada (Opcional)
            </label>
            <input type="file" id="file-upload" name="imagen_subida" accept="image/png, image/jpeg, image/gif">
            <br><br>

            <button type="submit">Publicar</button>
        </form>
    </div>
</body>
</html>