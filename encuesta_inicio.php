<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Encuesta UPVM - Inicio</title>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script>
		// Desactivar AJAX globalmente para evitar interferencias
		$(document).bind("mobileinit", function(){
			$.mobile.ajaxEnabled = false;
		});
	</script>
	<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	<style>
		.contenedor-encuesta {
			max-width: 500px;
			margin: 0 auto;
			text-align: center;
		}
		.profile-img {
			display: block;
			width: 150px;
			height: 150px;
			border-radius: 50%;
			object-fit: cover;
			margin: 0 auto 20px;
			border: 4px solid #0056b3;
			box-shadow: 0 4px 10px rgba(0,0,0,0.15);
		}
		.btn-espacio {
			margin-bottom: 12px !important;
		}
	</style>
</head>
<body>
	<div data-role="page" id="pagina-encuesta-inicio">
		<div data-role="header" data-theme="b">
			<h1>Encuesta UPVM</h1>
			<a href="index.php" rel="external" data-ajax="false" data-icon="home" class="ui-btn-left">Menú</a>
		</div>

		<div role="main" class="ui-content contenedor-encuesta">
			<!-- Foto Personal -->
			<img class="profile-img" src="yo edit.jpeg" alt="Mi foto">

			<h2>Evaluación de Experiencia Estudiantil - UPVM</h2>
			<p>Ayúdanos a mejorar nuestra universidad respondiendo esta breve encuesta de 5 preguntas sobre instalaciones y profesores.</p>
			<br>

			<!-- Botones de Acción -->
			<a href="encuesta.php" rel="external" data-role="button" data-theme="b" data-icon="edit" data-ajax="false" class="btn-espacio">
				📝 Responder Encuesta
			</a>

			<a href="index.php" rel="external" data-role="button" data-theme="a" data-icon="home" data-ajax="false" class="btn-espacio">
				🏠 Menú Principal
			</a>
		</div>
	</div>
</body>
</html>