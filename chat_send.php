<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : 'Anónimo';
    $mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

    if (!empty($mensaje)) {
        if (empty($nombre)) {
            $nombre = 'Anónimo';
        }

        // Usa el directorio temporal del sistema operativo
        $chat_file = sys_get_temp_dir() . '/temp_chat_messages.json';
        
        $data = [];
        if (file_exists($chat_file)) {
            $content = file_get_contents($chat_file);
            $data = json_decode($content, true);
            if (!is_array($data)) {
                $data = [];
            }
        }

        $nuevo_mensaje = [
            'id' => 'msg_' . microtime(true) . '_' . mt_rand(1000, 9999),
            'nombre' => htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'),
            'mensaje' => htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'),
            'hora' => date('h:i a'),
            'timestamp' => microtime(true)
        ];

        $data[] = $nuevo_mensaje;

        // Limita a los últimos 40 mensajes volátiles en memoria
        if (count($data) > 40) {
            $data = array_slice($data, -40);
        }

        file_put_contents($chat_file, json_encode($data), LOCK_EX);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

echo json_encode(['status' => 'error']);
