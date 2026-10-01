<?php
// Reporte de errores activado para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('conexion.php');

// Normalización de la variable de conexión para evitar incompatibilidades
if (!isset($conn) && isset($conexion)) {
    $conn = $conexion;
}

if (!$conn) {
    die("Error crítico: No se pudo establecer la conexión a la base de datos.");
}

// 1. CREAR PRODUCTO (CREATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $nombre      = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio      = floatval($_POST['precio']);
    $stock       = intval($_POST['stock']);

    if (!empty($nombre)) {
        $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $stock);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: crud.php");
    exit();
}

// 2. ACTUALIZAR PRODUCTO (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $id          = intval($_POST['id_producto']);
    $nombre      = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio      = floatval($_POST['precio']);
    $stock       = intval($_POST['stock']);

    if ($id > 0 && !empty($nombre)) {
        $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id_producto = ?");
        $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: crud.php");
    exit();
}

// 3. ELIMINAR PRODUCTO (DELETE)
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM productos WHERE id_producto = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: crud.php");
    exit();
}

// ID del producto seleccionado para edición inline
$id_editando = isset($_GET['editar_id']) ? intval($_GET['editar_id']) : 0;

// 4. CONSULTAR PRODUCTOS (READ)
$resultado = $conn->query("SELECT * FROM productos ORDER BY id_producto DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Módulo CRUD Unificado</title>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script>
		// Desactivar AJAX globalmente para evitar interferencias de navegación en jQuery Mobile
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
			margin: 0 auto 1.2rem;
			border-radius: 12px;
			box-shadow: 0 4px 10px rgba(0,0,0,0.15);
		}

		.contenedor-bloques {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
			gap: 18px;
			margin-top: 20px;
		}

		.tarjeta-producto {
			background: #ffffff;
			border: 1px solid #ddd;
			border-radius: 10px;
			padding: 16px;
			box-shadow: 0 3px 8px rgba(0,0,0,0.08);
		}

		.tarjeta-producto.editando {
			border: 2px solid #0056b3;
			background-color: #f0f7ff;
		}

		.tarjeta-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			border-bottom: 2px solid #e9ecef;
			padding-bottom: 8px;
			margin-bottom: 12px;
		}

		.tarjeta-header h3 {
			margin: 0;
			color: #0056b3;
			font-size: 1.15rem;
		}

		.badge-id {
			background: #0056b3;
			color: white;
			padding: 3px 8px;
			border-radius: 12px;
			font-size: 0.8rem;
			font-weight: bold;
		}

		.atributo {
			margin-bottom: 8px;
			font-size: 0.95rem;
		}

		.atributo-label {
			font-weight: bold;
			color: #555;
		}

		.acciones-grid {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 8px;
			margin-top: 15px;
		}

		.seccion-titulo {
			margin-top: 30px;
			border-bottom: 2px solid #0056b3;
			padding-bottom: 6px;
			color: #0056b3;
		}
	</style>
</head>
<body>
	<div data-role="page" id="pagina-crud">
		<div data-role="header" data-theme="b">
			<h1>Módulo CRUD Unificado</h1>
			<a href="index.php" rel="external" data-ajax="false" data-icon="home" class="ui-btn-left">Menú</a>
		</div>

		<div role="main" class="ui-content">
			<!-- Foto Personal -->
			<img class="foto-personal" src="yo.png" alt="Mi foto">

			<!-- FORMULARIO PARA REGISTRAR PRODUCTO (CREATE) -->
			<div data-role="collapsible" data-theme="b" data-content-theme="a" data-collapsed="false">
				<h4>➕ Agregar Nuevo Producto (Create)</h4>
				<form action="crud.php" method="POST" data-ajax="false">
					<label for="nombre">Nombre del Producto:</label>
					<input type="text" name="nombre" id="nombre" placeholder="Ej. Cuaderno Scribe" required>

					<label for="descripcion">Descripción:</label>
					<input type="text" name="descripcion" id="descripcion" placeholder="Ej. Cuaderno profesional 100 hojas">

					<label for="precio">Precio ($):</label>
					<input type="number" step="0.01" name="precio" id="precio" placeholder="0.00" required>

					<label for="stock">Stock / Cantidad:</label>
					<input type="number" name="stock" id="stock" placeholder="0" required>

					<button type="submit" name="crear" data-theme="b" data-icon="check">Guardar Producto</button>
				</form>
			</div>

			<!-- MUESTRA DE PRODUCTOS EN TARJETAS (READ, UPDATE, DELETE) -->
			<h2 class="seccion-titulo">📦 Catálogo de Productos</h2>

			<?php if ($resultado && $resultado->num_rows > 0): ?>
				<div class="contenedor-bloques">
					<?php while ($prod = $resultado->fetch_assoc()): ?>
						
						<?php if ($id_editando === intval($prod['id_producto'])): ?>
							<!-- TARJETA EN MODO EDICIÓN (UPDATE INLINE) -->
							<div class="tarjeta-producto editando">
								<form action="crud.php" method="POST" data-ajax="false">
									<input type="hidden" name="id_producto" value="<?php echo $prod['id_producto']; ?>">

									<div class="tarjeta-header">
										<h3>✏ Editando Producto</h3>
										<span class="badge-id">#<?php echo $prod['id_producto']; ?></span>
									</div>

									<label for="edit-nombre-<?php echo $prod['id_producto']; ?>">Nombre:</label>
									<input type="text" name="nombre" id="edit-nombre-<?php echo $prod['id_producto']; ?>" value="<?php echo htmlspecialchars($prod['nombre']); ?>" required>

									<label for="edit-desc-<?php echo $prod['id_producto']; ?>">Descripción:</label>
									<input type="text" name="descripcion" id="edit-desc-<?php echo $prod['id_producto']; ?>" value="<?php echo htmlspecialchars($prod['descripcion']); ?>">

									<label for="edit-precio-<?php echo $prod['id_producto']; ?>">Precio ($):</label>
									<input type="number" step="0.01" name="precio" id="edit-precio-<?php echo $prod['id_producto']; ?>" value="<?php echo htmlspecialchars($prod['precio']); ?>" required>

									<label for="edit-stock-<?php echo $prod['id_producto']; ?>">Stock:</label>
									<input type="number" name="stock" id="edit-stock-<?php echo $prod['id_producto']; ?>" value="<?php echo htmlspecialchars($prod['stock']); ?>" required>

									<div class="acciones-grid">
										<button type="submit" name="actualizar" data-theme="b" data-mini="true">💾 Guardar</button>
										<a href="crud.php" rel="external" data-role="button" data-theme="a" data-mini="true" data-ajax="false">❌ Cancelar</a>
									</div>
								</form>
							</div>

						<?php else: ?>
							<!-- TARJETA EN MODO LECTURA (READ) -->
							<div class="tarjeta-producto">
								<div class="tarjeta-header">
									<h3><?php echo htmlspecialchars($prod['nombre']); ?></h3>
									<span class="badge-id">ID: #<?php echo $prod['id_producto']; ?></span>
								</div>

								<div class="atributo">
									<span class="atributo-label">📝 Descripción:</span>
									<p><?php echo htmlspecialchars($prod['descripcion'] ?: 'Sin descripción'); ?></p>
								</div>

								<div class="atributo">
									<span class="atributo-label">💵 Precio:</span>
									<span>$<?php echo number_format($prod['precio'], 2); ?></span>
								</div>

								<div class="atributo">
									<span class="atributo-label">📊 Stock Disponible:</span>
									<span><?php echo intval($prod['stock']); ?> piezas</span>
								</div>

								<!-- ACCIONES -->
								<div class="acciones-grid">
									<a href="crud.php?editar_id=<?php echo $prod['id_producto']; ?>" rel="external" data-role="button" data-theme="b" data-mini="true" data-icon="edit" data-ajax="false">Actualizar</a>
									
									<a href="crud.php?eliminar=<?php echo $prod['id_producto']; ?>" onclick="return confirm('¿Deseas eliminar <?php echo htmlspecialchars($prod['nombre']); ?>?');" rel="external" data-role="button" data-theme="a" data-mini="true" data-icon="delete" data-ajax="false">Borrar</a>
								</div>
							</div>
						<?php endif; ?>

					<?php endwhile; ?>
				</div>
			<?php else: ?>
				<p style="text-align: center; color: #777; margin-top: 20px;">No hay productos registrados en la base de datos.</p>
			<?php endif; ?>

			<br>
			<a href="index.php" rel="external" data-role="button" data-theme="a" data-ajax="false" data-icon="home">🏠 Volver al Menú Principal</a>
		</div>
	</div>
</body>
</html>