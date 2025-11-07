<?php
    // Este archivo es llamado por el formulario usando el método POST.
    // Asi que, vamos a recibir los datos desde el arreglo de ese método
    $nombre = $_POST['usuario'];
    $gmail = $_POST['gmail'];
    $contraseña = $_POST['password'];

    // Vamos a guardar estos datos en una cookie que dure 1 minuto (solo es una prueba)
    setcookie("nombre", $nombre, time() + 60);
    setcookie("color", $gmail, time() + 60);
  
    // Imprimir un mensaje para el usuario
    echo "Datos guardados con éxito";
?>