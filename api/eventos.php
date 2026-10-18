<?php
session_start();
include_once '../conexion.php';

$content = trim(file_get_contents("php://input"));
$data = json_decode($content, true);

if (!empty($data)) {
    $sessionId = $data['session_id'] ?? 'desconocido';
    $userId = $_SESSION['usuario_id'] ?? null; // Obtiene el ID del usuario logueado
    $page = $data['page'] ?? '';
    $actionType = $data['action_type'] ?? '';
    $elementDetails = $data['element_details'] ?? null;
    $timeSpent = $data['time_spent'] ?? 0;

    if ($actionType === 'tiempo_pagina') {
        $stmt = $pdo->prepare("INSERT INTO user_metrics (session_id, user_id, page, action_type, time_spent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$sessionId, $userId, $page, $actionType, $timeSpent]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO user_metrics (session_id, user_id, page, action_type, element_details) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$sessionId, $userId, $page, $actionType, $elementDetails]);
    }
    
    http_response_code(200);
    echo json_encode(["status" => "success"]);
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "No data received"]);
}
?>
