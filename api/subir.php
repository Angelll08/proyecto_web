<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../sesion.php';
    require_once __DIR__ . '/../conexion.php';

    date_default_timezone_set('America/Mexico_City');

    function responder($codigo, $datos) {
        http_response_code($codigo);
        echo json_encode($datos);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responder(405, ['error' => 'Usa POST.']);
    }

    $foto = $_FILES['foto'] ?? null;
    if (!$foto || $foto['error'] !== UPLOAD_ERR_OK) {
        responder(400, ['error' => 'No llegó ninguna foto o hubo un error de subida (Código: ' . ($foto['error'] ?? 'desconocido') . ').']);
    }

    if ($foto['size'] > 5 * 1024 * 1024) {
        responder(400, ['error' => 'La foto pesa más de 5 MB.']);
    }

    $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $tipo  = (new finfo(FILEINFO_MIME_TYPE))->file($foto['tmp_name']);
    if (!isset($tipos[$tipo])) {
        responder(400, ['error' => 'Eso no es una imagen válida.']);
    }

    $usuario = preg_replace('/[^A-Za-z0-9]/', '', $_SESSION['nombre'] ?? 'usuario') ?: 'usuario';
    $archivo = $usuario . '_' . date('Ymd-His') . '_' . bin2hex(random_bytes(2)) . '.' . $tipos[$tipo];

    $rutaDir = __DIR__ . '/../subidas/';
    
    // Verificar si la carpeta existe y tiene permisos de escritura
    if (!is_dir($rutaDir)) {
        @mkdir($rutaDir, 0777, true);
    }

    if (!is_writable($rutaDir)) {
        responder(500, ['error' => 'La carpeta subidas/ no tiene permisos de escritura en el servidor.']);
    }

    $ruta = $rutaDir . $archivo;
    
    // Intentar mover el archivo y capturar el error exacto si falla
    if (!move_uploaded_file($foto['tmp_name'], $ruta)) {
        $errorSrv = error_get_last();
        responder(500, ['error' => 'Fallo al mover archivo: ' . ($errorSrv['message'] ?? 'Error desconocido en disco')]);
    }

    // Registro en la base de datos de Railway
    $sql = 'INSERT INTO fotos (usuario_id, nombre, archivo, tipo, bytes) VALUES (?, ?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_SESSION['usuario_id'] ?? null, 
        $_SESSION['nombre'] ?? 'Usuario', 
        $archivo, 
        $tipo, 
        $foto['size']
    ]);

    responder(201, ['archivo' => $archivo, 'url' => 'subidas/' . $archivo]);

} catch (Exception $e) {
    if (isset($ruta) && file_exists($ruta)) {
        unlink($ruta);
    }
    http_response_code(500);
    echo json_encode(['error' => 'Excepción: ' . $e->getMessage()]);
    exit;
}
