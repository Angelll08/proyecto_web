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

// Resumen
$resumen = $pdo->query(
    'SELECT COUNT(*) AS total,
            COUNT(DISTINCT ip) AS ips,
            COUNT(DISTINCT usuario_id) AS usuarios
     FROM accesos'
)->fetch(PDO::FETCH_ASSOC);

// Últimos 200 accesos
$filas = $pdo->query(
    'SELECT id, usuario_id, nombre, ip, navegador, creado_en
     FROM accesos
     ORDER BY id DESC
     LIMIT 200'
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Accesos de Usuarios - Administración</title>
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
			max-width: 1000px;
			margin: 0 auto;
		}
		.btn-espacio {
			margin-bottom: 12px !important;
		}
		.resumen {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
			margin-bottom: 16px;
		}
		.resumen div {
			flex: 1 1 140px;
			padding: 12px;
			text-align: center;
			background: #f1f5f9;
			border-radius: .3125em;
		}
		.resumen strong {
			display: block;
			font-size: 1.6rem;
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
			vertical-align: top;
		}
		.tabla-admin thead th { background: #f1f5f9; }
		.navegador {
			max-width: 280px;
			word-break: break-word;
			font-size: .8rem;
			color: #555;
		}
		.vacio {
			padding: 10px;
			border-radius: .3125em;
			background: #dbeafe;
			color: #1e40af;
		}
	</style>
</head>
<body>
	<div data-role="page" id="pagina-admin-accesos">
		<div data-role="header" data-theme="b">
			<a href="menu.php" rel="external" data-icon="home" data-ajax="false" class="ui-btn-left">Menú</a>
			<h1>Accesos</h1>
			<a href="logout.php" data-icon="power" data-iconpos="notext" class="ui-btn-right" data-ajax="false">Cerrar Sesión</a>
		</div>

		<div role="main" class="ui-content contenedor-admin">
			<p><span style="background: #ff9800; color: white; padding: 4px 8px; border-radius: 5px; font-weight: bold; font-size: 0.85em;">ROL: ADMINISTRADOR</span></p>
			<h2>Accesos de usuarios</h2>

			<div class="resumen">
				<div><strong><?= (int) $resumen['total'] ?></strong>Accesos totales</div>
				<div><strong><?= (int) $resumen['usuarios'] ?></strong>Usuarios distintos</div>
				<div><strong><?= (int) $resumen['ips'] ?></strong>IPs distintas</div>
			</div>

			<p>Mostrando los últimos <strong><?= count($filas) ?></strong> (máximo 200, los más recientes primero)</p>

			<?php if (!$filas): ?>
				<p class="vacio">Todavía no hay accesos registrados. Cierra sesión y vuelve a iniciar para generar el primero.</p>
			<?php else: ?>
				<table data-role="table" data-mode="reflow" class="ui-responsive tabla-admin">
					<thead>
						<tr>
							<th>#</th>
							<th>Usuario</th>
							<th>IP</th>
							<th>Navegador / Dispositivo</th>
							<th>Fecha</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($filas as $f): ?>
							<tr>
								<td><?= (int) $f['id'] ?></td>
								<td><?= e($f['nombre']) ?></td>
								<td><?= e($f['ip']) ?></td>
								<td class="navegador"><?= e($f['navegador']) ?></td>
								<td><?= e($f['creado_en']) ?></td>
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
