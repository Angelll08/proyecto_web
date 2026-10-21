<?php
// Copiar a: api/subir.php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Evita que los errores de PHP salgan como HTML

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
        responder(400, ['error' => 'No llegó ninguna foto.']);
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

    // Ruta segura hacia la carpeta subidas en la raíz del proyecto
    $rutaDir = __DIR__ . '/../subidas/';
    if (!is_dir($rutaDir)) {
        mkdir($rutaDir, 0755, true);
    }

    $ruta = $rutaDir . $archivo;
    if (!move_uploaded_file($foto['tmp_name'], $ruta)) {
        responder(500, ['error' => 'No se pudo guardar la foto en el servidor.']);
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
        unlink($ruta); // Borra el archivo si falla la base de datos
    }
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
    exit;
}
