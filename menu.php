<?php
// menu.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('conexion.php');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar que el usuario haya iniciado sesión, si no, regresarlo al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$esAdmin = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin');
$mensajeAdmin = '';
$errorAdmin = '';

// Procesar el registro de un nuevo Administrador desde esta misma vista
if ($esAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_admin'])) {
    $nombre = trim($_POST['nombre_admin'] ?? '');
    $correo = trim($_POST['correo_admin'] ?? '');
    $password = $_POST['password_admin'] ?? '';

    if (!empty($nombre) && !empty($correo) && !empty($password)) {
        // Verificar si el correo ya existe
        $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmtCheck->execute([$correo]);
        
        if ($stmtCheck->fetch()) {
            $errorAdmin = "El correo ya está registrado.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmtIns = $pdo->prepare("INSERT INTO usuarios (nombre, correo, password_hash, rol) VALUES (?, ?, ?, 'admin')");
            if ($stmtIns->execute([$nombre, $correo, $hash])) {
                $mensajeAdmin = "¡Nuevo Administrador creado con éxito!";
            } else {
                $errorAdmin = "Error al crear el administrador.";
            }
        }
    } else {
        $errorAdmin = "Por favor completa todos los campos.";
    }
}

// Consultar las métricas de la base de datos si es administrador
$metricas = [];
if ($esAdmin) {
    try {
        $query = $pdo->query("SELECT session_id, page, action_type, element_details, time_spent, created_at FROM user_metrics ORDER BY created_at DESC LIMIT 100");
        $metricas = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // En caso de que la tabla aún no exista
        $metricas = [];
    }
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
			max-width: 700px;
			margin: 0 auto;
			text-align: center;
		}
		.btn-espacio {
			margin-bottom: 12px !important;
		}
		.panel-admin {
			background: #ffffff;
			border: 2px solid #3388cc;
			border-radius: 10px;
			padding: 15px;
			margin-top: 25px;
			text-align: left;
		}
		.tabla-metricas {
			width: 100%;
			border-collapse: collapse;
			margin-top: 15px;
			font-size: 0.9em;
		}
		.tabla-metricas th, .tabla-metricas td {
			border: 1px solid #ddd;
			padding: 8px;
			text-align: left;
		}
		.tabla-metricas th {
			background-color: #3388cc;
			color: white;
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
			<?php if ($esAdmin): ?>
				<p><span style="background: #ff9800; color: white; padding: 4px 8px; border-radius: 5px; font-weight: bold;">ROL: ADMINISTRADOR</span></p>
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

			<!-- SECCIÓN EXCLUSIVA DE ADMINISTRADOR -->
			<?php if ($esAdmin): ?>
				<div class="panel-admin">
					<h3>👤 Registrar Nuevo Administrador</h3>
					<?php if ($mensajeAdmin): ?>
						<p style="color: green; font-weight: bold;"><?php echo htmlspecialchars($mensajeAdmin); ?></p>
					<?php endif; ?>
					<?php if ($errorAdmin): ?>
						<p style="color: red; font-weight: bold;"><?php echo htmlspecialchars($errorAdmin); ?></p>
					<?php endif; ?>
					
					<form method="post" data-ajax="false">
						<input type="hidden" name="crear_admin" value="1">
						<label for="nombre_admin">Nombre Completo:</label>
						<input type="text" name="nombre_admin" id="nombre_admin" required>
						
						<label for="correo_admin">Correo Electrónico:</label>
						<input type="email" name="correo_admin" id="correo_admin" required>
						
						<label for="password_admin">Contraseña:</label>
						<input type="password" name="password_admin" id="password_admin" required>
						
						<button type="submit" data-theme="b" data-icon="user">Crear Administrador</button>
					</form>
				</div>

				<div class="panel-admin">
					<h3>📊 Estadísticas y Métricas de Usuarios (Cookies & Eventos)</h3>
					<div style="overflow-x: auto;">
						<table class="tabla-metricas">
							<thead>
								<tr>
									<th>Fecha / Hora</th>
									<th>Sesión</th>
									<th>Página</th>
									<th>Acción</th>
									<th>Detalles / Tiempo</th>
								</tr>
							</thead>
							<tbody>
								<?php if (count($metricas) > 0): ?>
									<?php foreach ($metricas as $m): ?>
										<tr>
											<td><?php echo htmlspecialchars($m['created_at']); ?></td>
											<td><small><?php echo htmlspecialchars(substr($m['session_id'], 0, 10)); ?>...</small></td>
											<td><?php echo htmlspecialchars($m['page']); ?></td>
											<td><strong><?php echo htmlspecialchars($m['action_type']); ?></strong></td>
											<td>
												<?php 
												if ($m['action_type'] === 'tiempo_pagina') {
													echo "Tiempo: <strong>{$m['time_spent']}s</strong>";
												} else {
													echo htmlspecialchars($m['element_details']);
												}
												?>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="5" style="text-align: center;">No hay registros de métricas aún.</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			<?php endif; ?>

		</div>
		<script src="tracker.js"></script>
	</div>
</body>
</html>
