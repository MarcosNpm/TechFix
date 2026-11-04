<?php
/**
 * includes/bootstrap.php
 * Punto de arranque común de todas las vistas del sitio:
 * variables de entorno, helpers, URL base y sesión segura.
 */

require_once dirname(__DIR__) . '/config/env.php';
require_once __DIR__ . '/funciones.php';

date_default_timezone_set(APP_TIMEZONE);

if (!defined('BASE_URL')) {
    if (PHP_SAPI === 'cli' || empty($_SERVER['SCRIPT_NAME'])) {
        $base = '/';
    } else {
        $directorio = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $directorio = rtrim($directorio, '/');
        // Las carpetas admin/, api/ y mvc/ comparten la raíz del proyecto.
        $directorio = preg_replace('#/(admin|api|mvc)$#', '', $directorio) ?? $directorio;
        $base = ($directorio === '' || $directorio === '.') ? '/' : $directorio . '/';
    }
    define('BASE_URL', $base);
}

if (session_status() === PHP_SESSION_NONE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    if ($https) {
        ini_set('session.cookie_secure', '1');
    }

    session_name('TECHFIXSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Cabeceras básicas de seguridad para las respuestas HTML.
if (PHP_SAPI !== 'cli' && !headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
