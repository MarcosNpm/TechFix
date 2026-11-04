<?php
/**
 * router.php · Router para el servidor embebido de PHP.
 *
 * Uso:
 *   php -S 127.0.0.1:8000 router.php
 *
 * Permite probar las rutas amigables del módulo Fat-Free
 * (/framework/reportes, /framework/reportes/4, /framework/api/stock)
 * sin necesidad de Apache. En producción se usa framework/.htaccess.
 */

declare(strict_types=1);

$ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$ruta = rawurldecode($ruta);

// Módulo Fat-Free: todo lo que cuelga de /framework pasa por su front controller.
if ($ruta === '/framework' || str_starts_with($ruta, '/framework/')) {
    $_SERVER['SCRIPT_NAME'] = '/framework/index.php';
    $_SERVER['PHP_SELF'] = '/framework/index.php';
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/framework/index.php';
    require __DIR__ . '/framework/index.php';
    return true;
}

// Archivos reales (CSS, JS, imágenes, PHP): los sirve el servidor embebido.
$archivo = __DIR__ . $ruta;
if ($ruta !== '/' && is_file($archivo)) {
    return false;
}

// El resto lo resuelve el servidor normalmente (index.php, mvc/, clase5/, etc.).
return false;
