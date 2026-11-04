<?php
/**
 * config/env.php
 * Carga las variables de entorno desde el archivo .env de la raíz.
 * Si el archivo no existe, usa valores por defecto para desarrollo local.
 */

if (!function_exists('cargar_env')) {
    function cargar_env(string $ruta): void
    {
        if (!is_file($ruta) || !is_readable($ruta)) {
            return;
        }

        $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lineas === false) {
            return;
        }

        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if ($linea === '' || str_starts_with($linea, '#') || !str_contains($linea, '=')) {
                continue;
            }

            [$clave, $valor] = explode('=', $linea, 2);
            $clave = trim($clave);
            $valor = trim($valor);

            if (str_starts_with($clave, 'export ')) {
                $clave = trim(substr($clave, 7));
            }

            // Quitar comillas simples o dobles que envuelven al valor.
            if (strlen($valor) >= 2) {
                $primero = $valor[0];
                $ultimo = $valor[strlen($valor) - 1];
                if (($primero === '"' && $ultimo === '"') || ($primero === "'" && $ultimo === "'")) {
                    $valor = substr($valor, 1, -1);
                }
            }

            $GLOBALS['__techfix_env'][$clave] = $valor;
        }
    }
}

if (!function_exists('env')) {
    function env(string $clave, mixed $porDefecto = null): mixed
    {
        if (isset($GLOBALS['__techfix_env'][$clave])) {
            return $GLOBALS['__techfix_env'][$clave];
        }

        $valor = getenv($clave);
        return ($valor === false || $valor === '') ? $porDefecto : $valor;
    }
}

if (!isset($GLOBALS['__techfix_env'])) {
    $GLOBALS['__techfix_env'] = [];
    cargar_env(dirname(__DIR__) . '/.env');
}

if (!defined('APP_NAME')) {
    define('APP_NAME', (string) env('APP_NAME', 'TechFix'));
    define('APP_ENV', (string) env('APP_ENV', 'local'));
    define('APP_DEBUG', filter_var(env('APP_DEBUG', 'true'), FILTER_VALIDATE_BOOLEAN));
    define('APP_URL', (string) env('APP_URL', 'http://localhost/techfix'));
    define('APP_TIMEZONE', (string) env('APP_TIMEZONE', 'America/Argentina/Buenos_Aires'));
    define('SUPPORT_EMAIL', (string) env('SUPPORT_EMAIL', 'soporte@techfix.com.ar'));
    define('SUPPORT_PHONE', (string) env('SUPPORT_PHONE', '+54 11 5555-0101'));
    define('WHATSAPP', (string) env('WHATSAPP', '5491155550101'));
    define('SHOP_ADDRESS', (string) env('SHOP_ADDRESS', 'Av. Rivadavia 1234, CABA'));
    define('APP_KEY', (string) env('APP_KEY', 'techfix-clave-local-de-desarrollo'));
}
