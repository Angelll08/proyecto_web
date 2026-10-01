<?php
// Obtener credenciales desde las variables de entorno de Render
$host     = getenv('DB_HOST') ?: 'localhost';
$user     = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'railway';
$port     = getenv('DB_PORT') ?: 3306;

// Crear la conexión especificando el puerto
$conn = new mysqli($host, $user, $password, $database, (int)$port);

// Crear un alias por si otros scripts usan $conexion
$conexion = $conn;

// Verificar si hay errores
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
?>
