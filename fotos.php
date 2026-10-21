<?php
// Copiar a: fotos.php
require 'sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería y Subida de Fotos</title>
    <!-- jQuery Mobile y dependencias -->
    <link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script>
        $(document).bind("mobileinit", function(){
            $.mobile.ajaxEnabled = false;
        });
    </script>
    <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div data-role="page" id="pagina-fotos">
        <div data-role="header" data-theme="b">
            <a href="menu.php" data-icon="home" data-ajax="false">Menú Principal</a>
            <h1>Galería y Cámara</h1>
        </div>

        <div role="main" class="ui-content tarjeta" style="margin: 20px auto;">
            <h1>Subir foto</h1>
            <p>Sesión activa: <strong><?= e($_SESSION['nombre']) ?></strong></p>

            <!-- Opción 1: cámara en vivo -->
            <video id="video" autoplay playsinline muted hidden></video>
            <div class="botones">
                <button type="button" id="btn-camara">Activar cámara</button>
                <button type="button" id="btn-tomar" disabled>Tomar foto</button>
            </div>

            <!-- Opción 2: elegir un archivo del equipo -->
            <label>o elige una foto de tu equipo
                <input type="file" id="archivo" accept="image/*">
            </label>

            <img id="vista-previa" alt="Vista previa de la foto" hidden>
            <button type="button" id="btn-subir" disabled>Subir foto</button>
            <p id="mensaje" hidden></p>

            <h2>Galería</h2>
            <div id="galeria" class="galeria"></div>
        </div>
    </div>
    <script src="foto.js"></script>
    <script src="tracker.js"></script>
</body>
</html>
