<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('conexion.php');

// Detecta automáticamente la variable de conexión ($conexion o $conn)
$db = isset($conexion) ? $conexion : (isset($conn) ? $conn : null);

if ($_SERVER["REQUEST_METHOD"] == "POST" && $db) {
    // Sanitización de datos para evitar inyección SQL
    $p1 = $db->real_escape_string($_POST['p1']);
    $p2 = $db->real_escape_string($_POST['p2']);
    $p3 = $db->real_escape_string($_POST['p3']);
    $p4 = $db->real_escape_string($_POST['p4']);
    $p5 = $db->real_escape_string($_POST['p5']);

    // Inserción en la base de datos MySQL local (AppServ)
    $sql = "INSERT INTO respuestas (p1, p2, p3, p4, p5) VALUES ('$p1', '$p2', '$p3', '$p4', '$p5')";

    if ($db->query($sql) === TRUE) {
        header("Location: regalo.php");
        exit();
    } else {
        echo "Error al guardar los datos en la base de datos: " . $db->error;
    }
} else {
    // Si entran directo a procesar.php sin enviar el formulario, redirigir a la encuesta
    header("Location: encuesta.php");
    exit();
}
?>