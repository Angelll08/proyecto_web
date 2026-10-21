<?php
require 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

function responder($codigo, $datos)
{
    http_response_code($codigo);
    echo json_encode($datos);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['error' => 'Usa POST.']);
}

if (!isset($_SESSION['usuario_id'])) {
    responder(401, ['error' => 'Inicia sesión.']);
}

// fetch manda JSON, por eso se lee el cuerpo crudo y no $_POST
$datos = json_decode(file_get_contents('php://input'), true);
$lat   = $datos['latitud'] ?? null;
$lng   = $datos['longitud'] ?? null;

if (!is_numeric($lat) || !is_numeric($lng)
    || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    responder(400, ['error' => 'Coordenadas inválidas.']);
}

$precision = max(0, min(100000, (int) round((float) ($datos['precision'] ?? 0))));

// Quién es lo decide el servidor con la sesión, no el navegador
$sql = 'INSERT INTO ubicaciones (usuario_id, nombre, latitud, longitud, precision_m)
        VALUES (?, ?, ?, ?, ?)';
$pdo->prepare($sql)->execute([
    $_SESSION['usuario_id'],
    $_SESSION['nombre'] ?? 'usuario',
    $lat,
    $lng,
    $precision,
]);

responder(201, ['id' => (int) $pdo->lastInsertId()]);
