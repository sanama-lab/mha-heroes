<?php
// Cargar conexión a base de datos
require 'db.php';

// Si no hay sesión, la iniciamos para poder chequear el ID
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Obtener IP y ID del usuario actual
$mi_ip = $_SERVER['REMOTE_ADDR'];
$mi_id = $_SESSION['idusuario'] ?? 0;

// Revisar si los 7 días aún no han pasado
$check = $conn->prepare("SELECT fecha_fin FROM baneos WHERE (ip = ? OR idusuario = ?) AND fecha_fin > NOW() LIMIT 1");

if ($check === false) {
    die("Error en la consulta: " . $conn->error);
}

$check->bind_param("si", $mi_ip, $mi_id);

if (!$check->execute()) {
    die("Error al ejecutar la consulta: " . $check->error);
}

$res_ban = $check->get_result();

if ($row_ban = $res_ban->fetch_assoc()) {
    // Usuario está baneado
    session_destroy();
    
    // Mostrar mensaje de baneo con fecha formateada
    $fecha_deban = date('d/m/Y H:i', strtotime($row_ban['fecha_fin']));
    
    die("<div style='text-align:center; padding:50px; font-family:sans-serif; background:#f0f0f0; min-height:100vh; display:flex; align-items:center; justify-content:center;'>
            <div style='background:white; padding:30px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1);'>
                <h1 style='color:red;'>⚠️ HAS SIDO HECHO POLVO</h1>
                <p style='font-size:16px; color:#333;'>Tu cuenta y conexión están bloqueadas temporalmente.</p>
                <p style='font-size:18px; color:#d9534f;'><b>Podrás volver el: " . htmlspecialchars($fecha_deban) . "</b></p>
                <p style='color:#666; font-size:14px;'>Si crees que es un error, contacta con un administrador.</p>
            </div>
         </div>");
}
?>