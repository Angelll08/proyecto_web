<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('conexion.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Video Informativo - Sistema</title>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script>
		// Desactivar AJAX globalmente en jQuery Mobile para evitar bloqueos
		$(document).bind("mobileinit", function(){
			$.mobile.ajaxEnabled = false;
		});
	</script>
	<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	<style>
		.contenedor-video {
			max-width: 700px;
			margin: 0 auto;
			text-align: center;
		}
		.video-responsive {
			position: relative;
			padding-bottom: 56.25%; /* Relación de aspecto 16:9 */
			height: 0;
			overflow: hidden;
			border-radius: 12px;
			box-shadow: 0 4px 10px rgba(0,0,0,0.2);
			margin-bottom: 20px;
		}
		.video-responsive iframe {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			border: 0;
		}
	</style>
</head>
<body>
	<div data-role="page" id="pagina-video">
		<div data-role="header" data-theme="b">
			<h1>Video Informativo</h1>
		</div>

		<div role="main" class="ui-content contenedor-video">
			<h2>Reproductor de Video</h2>
			<p>Visualiza el siguiente contenido de YouTube:</p>

			<!-- Contenedor Responsivo del Video -->
			<div class="video-responsive">
				<iframe 
					src="https://www.youtube.com/embed/B8SjrfosU6o" 
					title="Reproductor de Video YouTube" 
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
					allowfullscreen>
				</iframe>
			</div>

			<!-- Botón para regresar al menú principal -->
			<a href="index.php" rel="external" data-role="button" data-theme="a" data-icon="back" data-ajax="false">
				⬅️ Volver al Menú Principal
			</a>
		</div>
	</div>
</body>
</html>
