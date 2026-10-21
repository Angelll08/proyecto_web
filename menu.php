<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('conexion.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$esAdmin = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin');
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
			<a href="logout.php" data-icon="power" class="ui-btn-right" data-ajax="false">Cerrar Sesión</a>
		</div>

		<div role="main" class="ui-content contenedor-menu">
			<img class="foto-personal" src="yo edit.jpeg" alt="Mi foto">
			
			<h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?>!</h2>
			<?php if ($esAdmin): ?>
				<p><span style="background: #ff9800; color: white; padding: 4px 8px; border-radius: 5px; font-weight: bold; font-size: 0.85em;">ROL: ADMINISTRADOR</span></p>
			<?php endif; ?>
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

			<!-- 6. Galería y Subida de Fotos -->
			<a href="fotos.php" rel="external" data-role="button" data-theme="a" data-icon="camera" data-ajax="false" class="btn-espacio">
   				 📷 Ver Galería y Subir Fotos
			</a>

			<!-- 7. Capturar ubicación -->
			<a href="ubicacion.php" rel="external" data-role="button" data-theme="b" data-icon="location" data-ajax="false" class="btn-espacio">
				📍 Capturar Ubicación
			</a>

			<!-- BOTÓN EXCLUSIVO DE ADMINISTRADOR -->
			<?php if ($esAdmin): ?>
				<hr style="margin: 20px 0; border: 0; border-top: 1px solid #ddd;">
				<a href="admin_stats.php" rel="external" data-role="button" data-theme="delete" data-icon="gear" data-ajax="false" class="btn-espacio">
					⚙️ Panel de Administración (Métricas y Admins)
				</a>
			<?php endif; ?>

			<?php if ($esAdmin): ?>
				<hr style="margin: 20px 0; border: 0; border-top: 1px solid #ddd;">
				<a href="admin_stats.php" rel="external" data-role="button" data-theme="delete" data-icon="gear" data-ajax="false" class="btn-espacio">
					⚙️ Panel de Administración (Métricas y Admins)
				</a>
				<a href="admin_ubicaciones.php" rel="external" data-role="button" data-theme="delete" data-icon="location" data-ajax="false" class="btn-espacio">
					🗺️ Ubicaciones de Usuarios (Admin)
				</a>
			<?php endif; ?>

		</div>
		<script src="tracker.js"></script>
	</div>
</body>
</html>
