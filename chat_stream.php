<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

$chat_file = sys_get_temp_dir() . '/temp_chat_messages.json';
$last_time = isset($_GET['last']) ? (float)$_GET['last'] : 0;

$response = [];

if (file_exists($chat_file)) {
    $content = file_get_contents($chat_file);
    $data = json_decode($content, true);
    
    if (is_array($data)) {
        foreach ($data as $msg) {
            if (isset($msg['timestamp']) && $msg['timestamp'] > $last_time) {
                $response[] = $msg;
            }
        }
    }
}

echo json_encode($response);
