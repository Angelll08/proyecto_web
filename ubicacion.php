<?php
include_once('conexion.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

if (!function_exists('e')) {
    function e($texto)
    {
        return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Mi Ubicación - Sistema</title>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script>
		$(document).bind("mobileinit", function(){
			$.mobile.ajaxEnabled = false;
		});
	</script>
	<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	<style>
		[hidden] { display: none !important; }
		.contenedor-ubicacion {
			max-width: 700px;
			margin: 0 auto;
		}
		.btn-espacio {
			margin-bottom: 12px !important;
		}
		#btn-capturar {
			display: block;
			width: 100%;
			padding: .8em;
			margin-bottom: 12px;
			border: 1px solid #1f75b8;
			border-radius: .3125em;
			background: #38c;
			color: #fff;
			font-size: 1rem;
			font-weight: bold;
			text-shadow: 0 1px 0 #059;
			cursor: pointer;
		}
		#btn-capturar:disabled {
			background: #94a3b8;
			border-color: #94a3b8;
			cursor: not-allowed;
		}
		#mensaje, #datos { padding: 10px; border-radius: .3125em; font-size: .9rem; }
		.error { background: #fee2e2; color: #991b1b; }
		.exito { background: #dcfce7; color: #166534; }
		.info  { background: #dbeafe; color: #1e40af; }
		#mapa {
			height: 320px;
			margin-top: 14px;
			border-radius: .3125em;
		}
		.historial { padding-left: 20px; font-size: .85rem; }
		.historial li { margin-bottom: 4px; }
	</style>
</head>
<body>
	<div data-role="page" id="pagina-ubicacion">
		<div data-role="header" data-theme="b">
			<a href="menu.php" rel="external" data-icon="home" data-ajax="false" class="ui-btn-left">Menú</a>
			<h1>Mi Ubicación</h1>
			<a href="logout.php" data-icon="power" data-iconpos="notext" class="ui-btn-right" data-ajax="false">Cerrar Sesión</a>
		</div>

		<div role="main" class="ui-content contenedor-ubicacion">
			<p>Sesión: <strong><?= e($_SESSION['nombre'] ?? 'Usuario') ?></strong></p>

			<button type="button" id="btn-capturar" data-role="none">📍 Capturar mi ubicación</button>
			<p id="mensaje" hidden></p>
			<p id="datos" hidden></p>

			<div id="mapa"></div>

			<h2>Mis últimas ubicaciones</h2>
			<ol id="historial" class="historial"></ol>

			<hr style="margin: 20px 0; border: 0; border-top: 1px solid #ddd;">
			<a href="menu.php" rel="external" data-role="button" data-theme="a" data-icon="arrow-l" data-ajax="false" class="btn-espacio">
				Volver al Menú
			</a>
		</div>

		<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
		<script src="ubicacion.js"></script>
		<script src="tracker.js"></script>
	</div>
</body>
</html>
