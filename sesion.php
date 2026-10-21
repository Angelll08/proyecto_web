<?php
// Copiar a: sesion.php
// Conecta a la base de datos, inicia la sesión y le da un nombre al usuario[cite: 5].

require __DIR__ . '/conexion.php'; // trae $pdo, session_start() y e()[cite: 5]

// ----------------------------------------------------------------------------
// 🔗 PISTA PARA CONECTAR CON LOGIN
// Aquí debería "cacharse" la sesión que crea login.php del ejercicio LOGIN[cite: 5].
// Allá, al iniciar sesión, se guarda[cite: 5]:
//     $_SESSION['usuario_id'] = $usuario['id'];[cite: 5]
//     $_SESSION['nombre']     = $usuario['nombre'];[cite: 5]
// Para conectarlo, este bloque se cambiaría por[cite: 5]:
//     if (!isset($_SESSION['usuario_id'])) {[cite: 5]
//         header('Location: login.php');   // sin sesión, primero a iniciar sesión[cite: 5]
//         exit;[cite: 5]
//     }[cite: 5]
// Mientras tanto, cada navegador recibe un nombre de invitado al azar[cite: 5]
// (y en la tabla fotos su usuario_id queda en NULL)[cite: 5].
// ----------------------------------------------------------------------------
if (!isset($_SESSION['nombre'])) {
    $_SESSION['nombre'] = 'invitado' . random_int(100, 999);
}
