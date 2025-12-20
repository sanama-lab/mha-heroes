<?php
require 'db.php';
session_start();

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
        <h1>Foro Multimedia</h1>
        <form id="search-form">
            <input type="text" placeholder="Buscar contenido..." aria-label="Buscar">
            <button type="submit">Buscar</button>
        </form>
    </header>

    <hr>

    <section id="publish-section">
        <h3>Publicar nuevo post</h3>
        <form id="post-form">
            <textarea placeholder="¿Qué quieres compartir?" rows="3" cols="50"></textarea>
            <br>
            
            <label for="file-upload">Adjuntar audio o video:</label>
            <input type="file" id="file-upload" accept="audio/mp3, video/mp4">
            
            <br><br>
            <button type="submit">Publicar</button>
        </form>
    </section>

    <hr>

    <main id="feed">
        <h2>Feed</h2>

        <article class="post">
            <header>
                <strong>@usuario_cine</strong> • <small>hace 2 minutos</small>
            </header>
            <p>Miren este video que grabé hoy:</p>
            
            <video width="320" height="240" controls>
                <source src="video-ejemplo.mp4" type="video/mp4">
                Tu navegador no soporta la reproducción de videos.
            </video>

            <nav>
                <button>Editar</button>
                <button>Eliminar</button>
            </nav>
        </article>

        <br>

        <article class="post">
            <header>
                <strong>@melomano_99</strong> • <small>hace 1 hora</small>
            </header>
            <p>Escuchen este podcast:</p>
            
            <audio controls>
                <source src="audio-ejemplo.mp3" type="audio/mpeg">
                Tu navegador no soporta la reproducción de audio.
            </audio>

            <nav>
                <button>Editar</button>
                <button>Eliminar</button>
            </nav>
        </article>
    </main>

    <hr>
    
    <footer>
        <p>© 2025 Foro Multimedia</p>
    </footer>

</body>
</html>