<?php
// Activar el almacenamiento en búfer para capturar cualquier salida HTML o error inesperado
ob_start();

header('Content-Type: application/json; charset=utf-8');

try {
    error_reporting(E_ALL);
    ini_set('display_errors', 0); // Oculta errores en pantalla para no romper el JSON

    require_once __DIR__ . '/../sesion.php';
    require_once __DIR__ . '/../conexion.php';

    date_default_timezone_set('America/Mexico_City');

    function responder($codigo, $datos) {
        if (ob_get_length()) ob_clean(); // Limpiar cualquier salida previa
        http_response_code($codigo);
        echo json_encode($datos);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responder(405, ['error' => 'Usa POST.']);
    }

    $foto = $_FILES['foto'] ?? null;
    if (!$foto || $foto['error'] !== UPLOAD_ERR_OK) {
        responder(400, ['error' => 'No llegó ninguna foto (Código de error: ' . ($foto['error'] ?? 'desconocido') . ').']);
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
    if (!is_dir($rutaDir)) {
        @mkdir($rutaDir, 0755, true);
    }

    $ruta = $rutaDir . $archivo;
    if (!move_uploaded_file($foto['tmp_name'], $ruta)) {
        $errSrv = error_get_last();
        responder(500, ['error' => 'No se pudo mover el archivo: ' . ($errSrv['message'] ?? 'Error desconocido')]);
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

} catch (Throwable $e) {
    if (isset($ruta) && file_exists($ruta)) {
        @unlink($ruta); // Borra el archivo si falla la base de datos
    }
    if (ob_get_length()) ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Excepción del servidor: ' . $e->getMessage()]);
    exit;
}
