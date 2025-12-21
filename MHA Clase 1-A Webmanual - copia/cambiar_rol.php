<?php
session_start();
require 'db.php';

// 1. SEGURIDAD: Solo el admin puede ejecutar este archivo
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Acceso denegado.");
}

// 2. RECIBIR DATOS
if (isset($_GET['id']) && isset($_GET['rol'])) {
    $id_usuario = $_GET['id'];
    $nuevo_rol  = $_GET['rol'];

    // 3. ACTUALIZAR EN LA BASE DE DATOS
    // Usamos UPDATE nombre_tabla SET columna = nuevo_valor WHERE condicion
    $stmt = $conn->prepare("UPDATE usuario SET tipousuario = ? WHERE idusuario = ?");
    $stmt->bind_param("si", $nuevo_rol, $id_usuario);

    if ($stmt->execute()) {
        // 4. REDIRIGIR de vuelta al panel con un mensaje de éxito
        header("Location: admin.php");
    } else {
        echo "Error al actualizar el rol: " . $conn->error;
    }
    
    $stmt->close();
} else {
    header("Location: admin.php");
}
?>