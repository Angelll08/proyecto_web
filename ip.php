<?php
// Obtiene la IP real del visitante (Render usa un proxy).
function obtenerIp()
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Puede traer varias separadas por coma: la primera es la del usuario
        $lista = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($lista[0]);
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    }
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

// Guarda un registro de acceso del usuario con sesión iniciada.
function registrarAcceso($pdo)
{
    try {
        $sql = 'INSERT INTO accesos (usuario_id, nombre, ip, navegador)
                VALUES (?, ?, ?, ?)';
        $pdo->prepare($sql)->execute([
            $_SESSION['usuario_id'] ?? null,
            $_SESSION['nombre'] ?? 'usuario',
            obtenerIp(),
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);
    } catch (Exception $e) {
        // Si falla el registro, no se rompe el inicio de sesión
    }
}
