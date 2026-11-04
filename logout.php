<?php
/**
 * logout.php · Cierre de sesión completo.
 */

require_once __DIR__ . '/includes/bootstrap.php';

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        [
            'expires'  => time() - 42000,
            'path'     => $params['path'],
            'domain'   => $params['domain'],
            'secure'   => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax',
        ]
    );
}

session_destroy();

// Se inicia una sesión nueva solo para poder mostrar el mensaje flash.
session_name('TECHFIXSESSID');
session_start();
flash('info', 'Cerraste sesión correctamente. ¡Hasta la próxima!');

redirigir('login.php');
