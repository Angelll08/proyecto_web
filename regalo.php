<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>¡Felicidades, aquí está tu regalo!</title>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script>
		$(document).bind("mobileinit", function(){
			$.mobile.ajaxEnabled = false;
		});
	</script>
	<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	<style>
		.profile-img {
			display: block;
			width: 100px;
			height: 100px;
			border-radius: 50%;
			object-fit: cover;
			margin: 0 auto 15px;
			border: 3px solid #0056b3;
			box-shadow: 0 4px 8px rgba(0,0,0,0.15);
		}
		.gift-img {
			display: block;
			width: 100%;
			max-width: 350px;
			height: auto;
			border-radius: 8px;
			box-shadow: 0 4px 10px rgba(0,0,0,0.2);
			margin: 15px auto 25px;
		}
		.contenedor-regalo {
			max-width: 550px;
			margin: 0 auto;
			text-align: center;
		}
	</style>
</head>
<body>
	<div data-role="page" id="pagina-regalo">
		<div data-role="header" data-theme="b">
			<h1>¡Gracias por participar!</h1>
		</div>

		<div role="main" class="ui-content contenedor-regalo">
			<!-- Foto Personal -->
			<img class="profile-img" src="yo edit.jpeg" alt="Mi foto">

			<h2 style="color: #28a745;">¡Muchas gracias por contestar!</h2>
			<p>Tu opinión es muy valiosa para la comunidad UPVM. Como agradecimiento, aquí tienes tu regalo:</p>

			<!-- Imagen de Regalo -->
			<img class="gift-img" src="https://i.pinimg.com/736x/97/72/62/977262e77d2aa3a72bc6846fcb63d89e.jpg" alt="Imagen de Regalo">

			<a href="index.php" rel="external" data-role="button" data-theme="b" data-ajax="false" data-icon="home">🏠 Volver al Inicio</a>
		</div>
	</div>
</body>
</html>