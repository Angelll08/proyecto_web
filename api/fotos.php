<?php
// Copiar a: C:\xampp\htdocs\foto\api\fotos.php
// Servicio que ENTREGA la lista de fotos:  GET api/fotos.php  → JSON

require __DIR__ . '/../conexion.php';
header('Content-Type: application/json; charset=utf-8');

// La lista sale de la tabla fotos (las más nuevas primero).
$fotos = $pdo->query('SELECT nombre, archivo FROM fotos ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(array_map(fn($foto) => [
    'url'     => '../subidas/' . $foto['archivo'], 
    'usuario' => $foto['nombre'],
], $fotos));
