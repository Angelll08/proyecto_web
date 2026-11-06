<?php
// conexion.php - Soporte optimizado con respaldo para PDO

// Obtener credenciales desde las variables de entorno de Render o valores por defecto
$host     = getenv('DB_HOST') ?: 'localhost';
$user     = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'railway';
$port     = getenv('DB_PORT') ?: 3306;

$conn = null;
$conexion = null;

// 1. Conexión mediante MySQLi (opcional / protegida con try-catch para evitar fatales)
try {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli($host, $user, $password, $database, (int)$port);
    if (!$conn->connect_error) {
        $conn->set_charset("utf8mb4");
        $conexion = $conn;
    }
} catch (Exception $e) {
    // Si falla MySQLi, lo ignoramos para que PDO tome el control principal
}

// 2. Conexión mediante PDO (Requerida y principal para login.php y registro.php)
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Error crítico de conexión PDO: " . $e->getMessage());
}

// Iniciar sesión de forma segura si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función global para escapar texto y evitar XSS
if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>
