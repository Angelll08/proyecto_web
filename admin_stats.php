<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('conexion.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar que sea administrador
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: menu.php");
    exit();
}

$mensajeAdmin = '';
$errorAdmin = '';

// Procesar el registro de un nuevo Administrador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_admin'])) {
    $nombre = trim($_POST['nombre_admin'] ?? '');
    $correo = trim($_POST['correo_admin'] ?? '');
    $password = $_POST['password_admin'] ?? '';

    if (!empty($nombre) && !empty($correo) && !empty($password)) {
        $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmtCheck->execute([$correo]);
        
        if ($stmtCheck->fetch()) {
            $errorAdmin = "El correo ya está registrado.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmtIns = $pdo->prepare("INSERT INTO usuarios (nombre, correo, password_hash, rol) VALUES (?, ?, ?, 'admin')");
            if ($stmtIns->execute([$nombre, $correo, $hash])) {
                $mensajeAdmin = "¡Nuevo Administrador creado con éxito!";
            } else {
                $errorAdmin = "Error al crear el administrador.";
            }
        }
    } else {
        $errorAdmin = "Por favor completa todos los campos.";
    }
}

// Consultar métricas haciendo JOIN con la tabla usuarios para obtener Nombre y Correo
try {
    $query = $pdo->query("
        SELECT m.*, u.nombre AS user_nombre, u.correo AS user_correo 
        FROM user_metrics m 
        LEFT JOIN usuarios u ON m.user_id = u.id 
        ORDER BY m.created_at DESC 
        LIMIT 150
    ");
    $metricas = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $metricas = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Administración y Métricas</title>
    <link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script>
        $(document).bind("mobileinit", function(){
            $.mobile.ajaxEnabled = false;
        });
    </script>
    <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <style>
        .contenedor-admin {
            max-width: 900px;
            margin: 0 auto;
            padding: 15px;
        }
        .tabla-metricas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 0.85em;
            background: #fff;
        }
        .tabla-metricas th, .tabla-metricas td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .tabla-metricas th {
            background-color: #3388cc;
            color: white;
        }
        .tarjeta-admin {
            background: #f9f9f9;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div data-role="page" id="pagina-admin">
        <div data-role="header" data-theme="b">
            <a href="menu.php" data-icon="back" data-ajax="false">Volver al Menú</a>
            <h1>Panel de Administración</h1>
            <a href="logout.php" data-icon="power" data-ajax="false">Cerrar Sesión</a>
        </div>

        <div role="main" class="ui-content contenedor-admin">
            
            <!-- Sección para Registrar Nuevo Admin -->
            <div class="tarjeta-admin">
                <h3>👤 Registrar Nuevo Administrador</h3>
                <?php if ($mensajeAdmin): ?>
                    <p style="color: green; font-weight: bold;"><?php echo htmlspecialchars($mensajeAdmin); ?></p>
                <?php endif; ?>
                <?php if ($errorAdmin): ?>
                    <p style="color: red; font-weight: bold;"><?php echo htmlspecialchars($errorAdmin); ?></p>
                <?php endif; ?>
                
                <form method="post" data-ajax="false">
                    <input type="hidden" name="crear_admin" value="1">
                    <label for="nombre_admin">Nombre Completo:</label>
                    <input type="text" name="nombre_admin" id="nombre_admin" required>
                    
                    <label for="correo_admin">Correo Electrónico:</label>
                    <input type="email" name="correo_admin" id="correo_admin" required>
                    
                    <label for="password_admin">Contraseña:</label>
                    <input type="password" name="password_admin" id="password_admin" required>
                    
                    <button type="submit" data-theme="b">Crear Administrador</button>
                </form>
            </div>

            <!-- Sección de Métricas Detalladas -->
            <div class="tarjeta-admin">
                <h3>📊 Historial Detallado de Actividad (Nombre, Correo, Clics y Tiempos)</h3>
                <div style="overflow-x: auto;">
                    <table class="tabla-metricas">
                        <thead>
                            <tr>
                                <th>Fecha / Hora</th>
                                <th>Usuario / Correo</th>
                                <th>Página</th>
                                <th>Acción</th>
                                <th>Detalles de la Acción / Tiempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($metricas) > 0): ?>
                                <?php foreach ($metricas as $m): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($m['created_at']); ?></td>
                                        <td>
                                            <?php if (!empty($m['user_nombre'])): ?>
                                                <strong><?php echo htmlspecialchars($m['user_nombre']); ?></strong><br>
                                                <small style="color: #666;"><?php echo htmlspecialchars($m['user_correo']); ?></small>
                                            <?php else: ?>
                                                <span style="color: #999;">Anónimo / Visitante</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($m['page']); ?></td>
                                        <td><strong><?php echo htmlspecialchars($m['action_type']); ?></strong></td>
                                        <td>
                                            <?php 
                                            if ($m['action_type'] === 'tiempo_pagina') {
                                                echo "Tiempo en página: <strong>{$m['time_spent']} segundos</strong>";
                                            } else {
                                                echo htmlspecialchars($m['element_details']);
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">No hay registros de métricas todavía.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <script src="tracker.js"></script>
    </div>
</body>
</html>
