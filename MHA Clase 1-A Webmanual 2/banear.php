<?php
require 'db.php';
session_start();

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != "admin" && $_SESSION['rol'] != "moderador")) {
    header("location:index.php"); exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Usar prepared statement para evitar SQL Injection
    $stmt = $conn->prepare("SELECT ultima_ip FROM usuario WHERE idusuario = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    // Validar que el usuario existe
    if ($res->num_rows === 0) {
        header("location:admin.php?status=usuario_no_existe");
        exit();
    }
    
    $user = $res->fetch_assoc();
    $ip_to_ban = $user['ultima_ip'] ?? 'desconocida';
    
    // Configurado a 7 días exactos
    $expira = date('Y-m-d H:i:s', strtotime("+7 days"));
    
    // Usar prepared statement
    $stmt_ban = $conn->prepare("INSERT INTO baneos (idusuario, ip, fecha_fin) VALUES (?, ?, ?)");
    $stmt_ban->bind_param("iss", $id, $ip_to_ban, $expira);
    
    if ($stmt_ban->execute()) {
        header("location:admin.php?status=hecho_polvo");
    } else {
        header("location:admin.php?status=error_baneo");
    }
}
?>