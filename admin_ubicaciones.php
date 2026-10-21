<?php
include_once('conexion.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sin sesión → login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Con sesión pero sin ser admin → al menú
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: menu.php');
    exit();
}

function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Últimas 200 ubicaciones de todos los usuarios
$filas = $pdo->query(
    'SELECT id, usuario_id, nombre, latitud, longitud, precision_m, creado_en
     FROM ubicaciones
     ORDER BY id DESC
     LIMIT 200'
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubicaciones de usuarios</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 16px;
            font-family: system-ui, sans-serif;
            background: #eef2f7;
            color: #1f2937;
        }
        .panel {
            max-width: 900px;
            margin: 0 auto;
            padding: 24px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .08);
        }
        h1 { margin-top: 0; font-size: 1.5rem; }
        .volver {
            display: inline-block;
            margin-bottom: 16px;
            color: #2563eb;
            text-decoration: none;
        }
        .tabla-contenedor { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .9rem; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; white-space: nowrap; }
        th { background: #f1f5f9; }
        tr:hover td { background: #f8fafc; }
        a.mapa { color: #2563eb; }
        .vacio { padding: 16px; background: #dbeafe; color: #1e40af; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="panel">
        <a class="volver" href="menu.php">← Volver al menú</a>
        <h1>Ubicaciones de usuarios</h1>
        <p>Total mostrado: <strong><?= count($filas) ?></strong> (máximo 200, las más recientes primero)</p>

        <?php if (!$filas): ?>
            <p class="vacio">Todavía nadie ha guardado una ubicación.</p>
        <?php else: ?>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Usuario</th>
                            <th>Latitud</th>
                            <th>Longitud</th>
                            <th>Precisión</th>
                            <th>Fecha</th>
                            <th>Mapa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filas as $f): ?>
                            <tr>
                                <td><?= (int) $f['id'] ?></td>
                                <td><?= e($f['nombre']) ?></td>
                                <td><?= e($f['latitud']) ?></td>
                                <td><?= e($f['longitud']) ?></td>
                                <td>±<?= (int) $f['precision_m'] ?> m</td>
                                <td><?= e($f['creado_en']) ?></td>
                                <td>
                                    <a class="mapa" target="_blank" rel="noopener"
                                       href="https://www.openstreetmap.org/?mlat=<?= e($f['latitud']) ?>&mlon=<?= e($f['longitud']) ?>#map=17/<?= e($f['latitud']) ?>/<?= e($f['longitud']) ?>">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
