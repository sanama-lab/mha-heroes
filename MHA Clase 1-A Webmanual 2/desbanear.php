<?php 
require 'db.php';
session_start();

if (!isset($_SESSION['rol']) || ($_SESSION['rol']) != "admin" && ($_SESSION['rol']) != "moderador" ){
    header("location:index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $query = "SELECT idusuario FROM usuario WHERE idusuario = ?";

    // Modificar el tiempo del ban a la fecha actual para que pierda la restriccion
    $query = "UPDATE baneos SET fecha_fin = NOW() WHERE idusuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("location:admin.php");
        exit();
    } else {
        echo "Error al desbanear el usuario.";
    }
}
?>