<?php
include_once '../conexion.php'; // Asegúrate de que la ruta a tu conexión sea correcta

// Permitir solicitudes JSON y de Beacon
$content = trim(file_get_contents("php://input"));
$data = json_decode($content, true);

if (!empty($data)) {
    $sessionId = $data['session_id'] ?? 'desconocido';
    $page = $data['page'] ?? '';
    $actionType = $data['action_type'] ?? '';
    $elementDetails = $data['element_details'] ?? null;
    $timeSpent = $data['time_spent'] ?? 0;

    // Si ya existe un registro de tiempo para esta sesión y página, actualizarlo, de lo contrario insertarlo
    if ($actionType === 'tiempo_pagina') {
        $stmt = $conn->prepare("INSERT INTO user_metrics (session_id, page, action_type, time_spent) VALUES (?, ?, ?, ?)");
        $stmt->execute([$sessionId, $page, $actionType, $timeSpent]);
    } else {
        $stmt = $conn->prepare("INSERT INTO user_metrics (session_id, page, action_type, element_details) VALUES (?, ?, ?, ?)");
        $stmt->execute([$sessionId, $page, $actionType, $elementDetails]);
    }
    
    http_response_code(200);
    echo json_encode(["status" => "success"]);
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "No data received"]);
}
?>
