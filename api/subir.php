<?php
// Copiar a: C:\xampp\htdocs\foto\api\subir.php
// Servicio que RECIBE una foto:  POST api/subir.php  (multipart/form-data, campo "foto")
<?php
// Copiar a: api/subir.php
require_once __DIR__ . '/../sesion.php';
require_once __DIR__ . '/../conexion.php'; // <-- ESTO FALTABA (Conexión a la base de datos)

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Mexico_City');

function responder($codigo, $datos)
{
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

// Validación de tamaño (5 MB máximo)
if ($foto['size'] > 5 * 1024 * 1024) {
    responder(400, ['error' => 'La foto pesa más de 5 MB.']);
}

// Validación real del tipo de archivo con finfo
$tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
$tipo  = (new finfo(FILEINFO_MIME_TYPE))->file($foto['tmp_name']);
if (!isset($tipos[$tipo])) {
    responder(400, ['error' => 'Eso no es una imagen válida.']);
}

// Generación segura del nombre del archivo
$usuario = preg_replace('/[^A-Za-z0-9]/', '', $_SESSION['nombre'] ?? 'usuario') ?: 'usuario';
$archivo = $usuario . '_' . date('Ymd-His') . '_' . bin2hex(random_bytes(2)) . '.' . $tipos[$tipo];

$ruta = __DIR__ . '/../subidas/' . $archivo;
if (!move_uploaded_file($foto['tmp_name'], $ruta)) {
    responder(500, ['error' => 'No se pudo guardar la foto en el servidor.']);
}

// Registro en la base de datos usando PDO
$sql = 'INSERT INTO fotos (usuario_id, nombre, archivo, tipo, bytes) VALUES (?, ?, ?, ?, ?)';
try {
    $pdo->prepare($sql)->execute([
        $_SESSION['usuario_id'] ?? null, 
        $_SESSION['nombre'] ?? 'Usuario', 
        $archivo, 
        $tipo, 
        $foto['size']
    ]);
} catch (PDOException $error) {
    if (file_exists($ruta)) {
        unlink($ruta); // Borra el archivo físico si falla el registro en la BD
    }
    responder(500, ['error' => 'No se pudo registrar la foto en la base de datos.']);
}

// Respuesta exitosa (201 = Creado) con la ruta correcta desde la raíz
responder(201, ['archivo' => $archivo, 'url' => 'subidas/' . $archivo]);
