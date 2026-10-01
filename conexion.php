<?php
// Configuración de la conexión a MySQL Local
$host     = "localhost";
$usuario  = "root";                // Usuario por defecto de MySQL
$password = "12345678";                    // Coloca tu contraseña de MySQL local si tienes una
$dbname   = "UPVM";        // Nombre de la base de datos en tu MySQL local

// Crear la conexión
$conn = new mysqli($host, $usuario, $password, $dbname);

// Verificar si hay error en la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos local: " . $conn->connect_error);
}

// Configurar codificación UTF-8 para acentos y caracteres especiales
$conn->set_charset("utf8");
?>