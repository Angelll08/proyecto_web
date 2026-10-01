<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ubicación y Mapas</title>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script>
		// Desactivar AJAX globalmente para evitar que jQuery Mobile bloquee la carga de la API de Google Maps
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

		.mapa-container {
			display: block;
			width: 100%;
			max-width: 700px;
			height: 400px;
			margin: 1rem auto 2rem;
			border: 0;
			border-radius: 10px;
			box-shadow: 0 3px 8px rgba(0,0,0,0.12);
		}

		.seccion-titulo {
			margin-top: 20px;
			border-bottom: 2px solid #0056b3;
			padding-bottom: 6px;
			color: #0056b3;
			text-align: center;
		}
	</style>
	<script>
		// Inicialización del Mapa Interactivo con API KEY
		function initMap() {
			const ubicacion = { lat: 19.642373116281597, lng: -99.12706074602605 };
			const mapa = new google.maps.Map(document.getElementById("mapa-api"), {
				zoom: 15,
				center: ubicacion,
			});
			new google.maps.Marker({
				position: ubicacion,
				map: mapa,
				title: "Ubicación UPVM",
			});
		}
	</script>
</head>
<body>
	<div data-role="page" id="pagina-mapa">
		<div data-role="header" data-theme="b">
			<h1>🗺️ Ubicación y Geolocalización</h1>
			<a href="index.php" rel="external" data-ajax="false" data-icon="home" class="ui-btn-left">Menú</a>
		</div>

		<div role="main" class="ui-content">
			<!-- Foto Personal -->
			<img class="foto-personal" src="yo.png" alt="Mi foto">

			<!-- 1. MAPA INCRUSTADO (IFRAME) -->
			<h2 class="seccion-titulo">📍 Mapa Incrustado (Iframe)</h2>
			<iframe class="mapa-container"
				src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d7515.294497430829!2d-99.12706074602605!3d19.642373116281597!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2smx!4v1790001643256!5m2!1ses-419!2smx"
				allowfullscreen=""
				loading="lazy"
				referrerpolicy="strict-origin-when-cross-origin"
				title="Mapa de ubicación iframe"></iframe>

			<!-- 2. MAPA CON API GOOGLE MAPS -->
			<h2 class="seccion-titulo">🌐 Mapa Interactivo (Google Maps API)</h2>
			<div class="mapa-container" id="mapa-api"></div>

			<br>
			<!-- Botón de regreso al Menú -->
			<a href="index.php" rel="external" data-role="button" data-theme="a" data-ajax="false" data-icon="home">🏠 Volver al Menú Principal</a>
		</div>
	</div>

	<!-- Script de API de Google Maps -->
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRHdUFlfmkChy6XYji_3yc5YD9VlKvWKg&callback=initMap&loading=async" async></script>
</body>
</html>