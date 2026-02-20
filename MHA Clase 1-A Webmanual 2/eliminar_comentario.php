<?php
require 'db.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("location:index.php");
    exit();
}

if (isset($_GET['id'])) {
    $idcomentario = intval($_GET['id']);
    
    // Verificar que el comentario existe y obtener el creador (mail)
    $stmt_check = $conn->prepare("SELECT idcomentario, creadorcom FROM comentario WHERE idcomentario = ?");
    $stmt_check->bind_param("i", $idcomentario);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    
    if ($res_check->num_rows === 0) {
        header("location:foro.php?status=comentario_no_existe");
        exit();
    }

    $row = $res_check->fetch_assoc();
    $creator_mail = $row['creadorcom'];
    $stmt_check->close();

    // Verificar permisos: admin, moderador o creador del comentario
    $is_admin_or_mod = (isset($_SESSION['rol']) && ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'moderador'));
    $is_creator = (isset($_SESSION['mail']) && $_SESSION['mail'] === $creator_mail);

    if (!$is_admin_or_mod && !$is_creator) {
        header("location:index.php");
        exit();
    }

    // modificar 'estado' a 'no permitido'
    $stmt_delete = $conn->prepare("UPDATE comentario SET estado = 'no permitido' WHERE idcomentario = ?");
    $stmt_delete->bind_param("i", $idcomentario);
    
    if ($stmt_delete->execute()) {
        header("location:foro.php?status=comentario_eliminado");
    } else {
        header("location:foro.php?status=error_eliminar");
    }
}

?>