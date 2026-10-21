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

if (!function_exists('e')) {
    function e($texto)
    {
        return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('horaMx')) {
    // La base guarda la hora en UTC: aquí se convierte a hora de México
    function horaMx($fechaUtc)
    {
        $fecha = new DateTime($fechaUtc, new DateTimeZone('UTC'));
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        return $fecha->format('d/m/Y H:i:s');
    }
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
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ubicaciones de Usuarios - Administración</title>
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
		}
		.btn-espacio {
			margin-bottom: 12px !important;
		}
		.tabla-admin {
			width: 100%;
			border-collapse: collapse;
			font-size: .9rem;
		}
		.tabla-admin th, .tabla-admin td {
			padding: .6em;
			text-align: left;
			border-bottom: 1px solid #ddd;
		}
		.tabla-admin thead th { background: #f1f5f9; }
		.vacio {
			padding: 10px;
			border-radius: .3125em;
			background: #dbeafe;
			color: #1e40af;
		}
	</style>
</head>
<body>
	<div data-role="page" id="pagina-admin-ubicaciones">
		<div data-role="header" data-theme="b">
			<a href="menu.php" rel="external" data-icon="home" data-ajax="false" class="ui-btn-left">Menú</a>
			<h1>Ubicaciones</h1>
			<a href="logout.php" data-icon="power" data-iconpos="notext" class="ui-btn-right" data-ajax="false">Cerrar Sesión</a>
		</div>

		<div role="main" class="ui-content contenedor-admin">
			<p><span style="background: #ff9800; color: white; padding: 4px 8px; border-radius: 5px; font-weight: bold; font-size: 0.85em;">ROL: ADMINISTRADOR</span></p>
			<h2>Ubicaciones de usuarios</h2>
			<p>Total mostrado: <strong><?= count($filas) ?></strong> (máximo 200, las más recientes primero)</p>

			<?php if (!$filas): ?>
				<p class="vacio">Todavía nadie ha guardado una ubicación.</p>
			<?php else: ?>
				<table data-role="table" data-mode="reflow" class="ui-responsive tabla-admin">
					<thead>
						<tr>
							<th>#</th>
							<th>Usuario</th>
							<th>Latitud</th>
							<th>Longitud</th>
							<th>Precisión</th>
							<th>Fecha (hora de México)</th>
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
								<td><?= e(horaMx($f['creado_en'])) ?></td>
								<td>
									<a target="_blank" rel="external noopener" data-ajax="false"
									   href="https://www.openstreetmap.org/?mlat=<?= e($f['latitud']) ?>&mlon=<?= e($f['longitud']) ?>#map=17/<?= e($f['latitud']) ?>/<?= e($f['longitud']) ?>">
										Ver
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<hr style="margin: 20px 0; border: 0; border-top: 1px solid #ddd;">
			<a href="menu.php" rel="external" data-role="button" data-theme="a" data-icon="arrow-l" data-ajax="false" class="btn-espacio">
				Volver al Menú
			</a>
		</div>
		<script src="tracker.js"></script>
	</div>
</body>
</html>
