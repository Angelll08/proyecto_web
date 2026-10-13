<?php
// index.php - Punto de entrada principal
include_once('conexion.php');

// Si ya hay una sesión activa, mandarlo directo al menú principal con la foto
if (isset($_SESSION['usuario_id'])) {
    header("Location: menu.php");
    exit();
} else {
    // Si no ha iniciado sesión, mandarlo al login
    header("Location: login.php");
    exit();
}
?>
