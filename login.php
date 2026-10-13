<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';

// Si el usuario ya inició sesión previamente, mandarlo directamente al menú
if (isset($_SESSION['usuario_id'])) {
    header('Location: menu.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo   = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, nombre, password_hash FROM usuarios WHERE correo = ?');
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre']     = $usuario['nombre'];
        
        // Redirige al menú principal integrado
        header('Location: menu.php');
        exit;
    }
    $error = 'Correo o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Estilo limpio para que la foto quede dentro de la tarjeta respetando tu CSS original */
        .foto-login {
            display: block;
            width: 85px;
            height: 85px;
            object-fit: cover;
            margin: 0 auto 15px auto;
            border-radius: 50%;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <main class="tarjeta">
        <!-- Foto colocada justo dentro de la tarjeta, arriba del título -->
        <img class="foto-login" src="yo edit.jpeg" alt="Mi foto">

        <h1>Iniciar sesión</h1>

        <?php if (isset($_GET['registrado'])): ?>
            <p class="exito">Cuenta creada. Ya puedes iniciar sesión.</p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="error"><?= e($error) ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Correo
                <input type="email" name="correo" required value="<?= e($_POST['correo'] ?? '') ?>">
            </label>
            <label>Contraseña
                <input type="password" name="password" required>
            </label>
            <button type="submit">Entrar</button>
        </form>

        <p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
    </main>
</body>
</html>
