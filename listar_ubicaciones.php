<?php
require 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Inicia sesión.']);
    exit;
}

// La base guarda la hora en UTC: aquí se convierte a hora de México
function horaMx($fechaUtc)
{
    $fecha = new DateTime($fechaUtc, new DateTimeZone('UTC'));
    $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
    return $fecha->format('d/m/Y H:i:s');
}

// Solo las ubicaciones del usuario que inició sesión
$consulta = $pdo->prepare(
    'SELECT nombre, latitud, longitud, precision_m, creado_en
     FROM ubicaciones
     WHERE usuario_id = ?
     ORDER BY id DESC LIMIT 50'
);
$consulta->execute([$_SESSION['usuario_id']]);
$filas = $consulta->fetchAll(PDO::FETCH_ASSOC);

$ubicaciones = array_map(fn($f) => [
    'nombre'      => $f['nombre'],
    'latitud'     => (float) $f['latitud'],
    'longitud'    => (float) $f['longitud'],
    'precision_m' => (int) $f['precision_m'],
    'creado_en'   => horaMx($f['creado_en']),
], $filas);

echo json_encode($ubicaciones);
