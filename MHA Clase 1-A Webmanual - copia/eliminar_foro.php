<?php
session_start();
require 'db.php';

// 1. SEGURIDAD: Solo el admin puede ejecutar este archivo
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'moderador') {
    die("Acceso denegado.");
}

// 2. RECIBIR DATOS
if (isset($_GET['id'])) {
    $id_contenido = $_GET['id'];

    // 3. ELIMINAR EN LA BASE DE DATOS
    // Usamos DELETE FROM nombre_tabla WHERE condicion
    $stmt = $conn->prepare("DELETE FROM contenido WHERE idcontenido = ?");
    $stmt->bind_param("i", $id_contenido);

    if ($stmt->execute()) {
        // 4. REDIRIGIR de vuelta al panel 
        header("Location: foro.php");
    } else {
        echo "Error al actualizar el rol: " . $conn->error;
    }
    
    $stmt->close();
} else {
    header("Location: foro.php");
}
?>
