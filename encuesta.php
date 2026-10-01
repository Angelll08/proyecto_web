<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Encuesta UPVM</title>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script>
		// Desactivar AJAX globalmente para evitar conflictos con la redirección en PHP
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
		.contenedor-form {
			max-width: 650px;
			margin: 0 auto;
		}
	</style>
</head>
<body>
	<div data-role="page" id="pagina-encuesta-preguntas">
		<div data-role="header" data-theme="b">
			<h1>Encuesta UPVM</h1>
			<a href="encuesta_inicio.php" rel="external" data-ajax="false" data-icon="back" class="ui-btn-left">Volver</a>
		</div>

		<div role="main" class="ui-content contenedor-form">
			<!-- Foto Personal -->
			<img class="profile-img" src="yo edit.jpeg" alt="Mi foto">
			<h2 style="text-align:center; color:#0056b3;">Encuesta sobre la UPVM</h2>

			<form action="procesar.php" method="POST" data-ajax="false">
				
				<!-- Pregunta 1 -->
				<label for="p1"><strong>1. ¿Cómo calificas el estado actual de los laboratorios y equipos de cómputo en la UPVM?</strong></label>
				<select name="p1" id="p1" required data-native-menu="false">
					<option value="">Selecciona una opción</option>
					<option value="Excelente">Excelente</option>
					<option value="Bueno">Bueno</option>
					<option value="Regular">Regular</option>
					<option value="Malo">Malo</option>
				</select>
				<br>

				<!-- Pregunta 2 -->
				<fieldset data-role="controlgroup">
					<legend><strong>2. ¿Consideras que los profesores dominan los temas de sus asignaturas?</strong></legend>
					<input type="radio" name="p2" id="p2-siempre" value="Siempre" required>
					<label for="p2-siempre">Siempre</label>
					<input type="radio" name="p2" id="p2-casi" value="Casi siempre">
					<label for="p2-casi">Casi siempre</label>
					<input type="radio" name="p2" id="p2-aveces" value="A veces">
					<label for="p2-aveces">A veces</label>
					<input type="radio" name="p2" id="p2-nunca" value="Nunca">
					<label for="p2-nunca">Nunca</label>
				</fieldset>
				<br>

				<!-- Pregunta 3 -->
				<label for="p3"><strong>3. ¿Las instalaciones generales de la universidad (áreas verdes, cafetería, edificios) están limpias y cuidadas?</strong></label>
				<select name="p3" id="p3" required data-native-menu="false">
					<option value="">Selecciona una opción</option>
					<option value="Totalmente de acuerdo">Totalmente de acuerdo</option>
					<option value="De acuerdo">De acuerdo</option>
					<option value="En desacuerdo">En desacuerdo</option>
				</select>
				<br>

				<!-- Pregunta 4 -->
				<label for="p4"><strong>4. ¿Qué tan satisfecho estás con los horarios y la atención brindada en servicios escolares?</strong></label>
				<select name="p4" id="p4" required data-native-menu="false">
					<option value="">Selecciona una opción</option>
					<option value="Muy satisfecho">Muy satisfecho</option>
					<option value="Satisfecho">Satisfecho</option>
					<option value="Insatisfecho">Insatisfecho</option>
				</select>
				<br>

				<!-- Pregunta 5 -->
				<fieldset data-role="controlgroup">
					<legend><strong>5. ¿Recomendarías estudiar en la Universidad Politécnica del Valle de México (UPVM) a tus conocidos?</strong></legend>
					<input type="radio" name="p5" id="p5-si" value="Sí" required>
					<label for="p5-si">Sí</label>
					<input type="radio" name="p5" id="p5-no" value="No">
					<label for="p5-no">No</label>
				</fieldset>
				<br>

				<button type="submit" data-theme="b" data-icon="check">Enviar Encuesta y Reclamar Regalo</button>
			</form>
		</div>
	</div>
</body>
</html>