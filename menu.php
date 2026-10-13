<?php
// menu.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('conexion.php');

// Validar que el usuario haya iniciado sesión, si no, regresarlo al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Menú Principal - Sistema</title>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script>
		$(document).bind("mobileinit", function(){
			$.mobile.ajaxEnabled = false;
		});
	</script>
	<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	<style>
		.foto-personal {
			display: block;
			width: min(100%, 260px);
			height: auto;
			margin: 0 auto 1.5rem;
			border-radius: 12px;
			box-shadow: 0 4px 10px rgba(0,0,0,0.15);
		}
		.contenedor-menu {
			max-width: 500px;
			margin: 0 auto;
			text-align: center;
		}
		.btn-espacio {
			margin-bottom: 12px !important;
		}
	</style>
</head>
<body>
	<div data-role="page" id="pagina-index">
		<div data-role="header" data-theme="b">
			<h1>Menú Principal</h1>
			<!-- Botón de cerrar sesión integrado en la esquina -->
			<a href="logout.php" data-icon="power" class="ui-btn-right" data-ajax="false">Cerrar Sesión</a>
		</div>

		<div role="main" class="ui-content contenedor-menu">
			<!-- Tu Foto Personal -->
			<img class="foto-personal" src="yo edit.jpeg" alt="Mi foto">
			
			<h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?>!</h2>
			<p>Selecciona la sección a la que deseas acceder:</p>
			<br>

			<!-- 1. Módulo CRUD -->
			<a href="crud.php" rel="external" data-role="button" data-theme="b" data-icon="grid" data-ajax="false" class="btn-espacio">
				📦 Módulo CRUD (Productos)
			</a>

			<!-- 2. Ubicación / Mapas -->
			<a href="mapas.php" rel="external" data-role="button" data-theme="a" data-icon="location" data-ajax="false" class="btn-espacio">
				🗺️ Ver Mapa / GeoUbicación
			</a>

			<!-- 3. Encuesta -->
			<a href="encuesta_inicio.php" rel="external" data-role="button" data-theme="a" data-icon="edit" data-ajax="false" class="btn-espacio">
				📋 Realizar Encuesta
			</a>

			<!-- 4. Chat Grupal En Vivo -->
			<a href="chat.php" rel="external" data-role="button" data-theme="b" data-icon="comment" data-ajax="false" class="btn-espacio">
				💬 Chat Grupal (En Vivo)
			</a>

			<!-- 5. Video de YouTube -->
			<a href="video.php" rel="external" data-role="button" data-theme="a" data-icon="video" data-ajax="false" class="btn-espacio">
				▶️ Ver Video Informativo
			</a>
		</div>
	</div>
</body>
</html>
		</div>
	</div>
</body>
</html>
