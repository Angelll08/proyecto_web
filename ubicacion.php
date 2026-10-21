<?php
require 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('e')) {
    function e($texto)
    {
        return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    }
}

// Sin sesión, primero a iniciar sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi ubicación</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <main class="tarjeta tarjeta-mapa">
        <h1>Mi ubicación</h1>
        <p>Sesión: <strong><?= e($_SESSION['nombre'] ?? 'usuario') ?></strong></p>

        <button type="button" id="btn-capturar">Capturar mi ubicación</button>
        <p id="mensaje" hidden></p>
        <p id="datos" hidden></p>

        <div id="mapa"></div>

        <h2>Mis últimas ubicaciones</h2>
        <ol id="historial" class="historial"></ol>
    </main>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="ubicacion.js"></script>
</body>
</html>
