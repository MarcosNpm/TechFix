<?php
/**
 * clase5/includes/funciones.php
 * Helpers de sanitización, validación y persistencia para la demo de la Clase 5.
 * Todo lo que llega por $_GET o $_POST pasa por estas funciones antes de usarse.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_name('CLASE5SESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** Escapa texto para HTML. Se usa SIEMPRE al imprimir datos del usuario. */
function e(mixed $valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Limpia una entrada de una línea: trim, sin etiquetas y sin bytes nulos. */
function limpiar(mixed $valor): string
{
    $texto = trim(strip_tags((string) ($valor ?? '')));
    $texto = str_replace("\0", '', $texto);
    return preg_replace('/\s+/u', ' ', $texto) ?? '';
}

/** Limpia un textarea conservando los saltos de línea. */
function limpiar_multilinea(mixed $valor): string
{
    $texto = trim(strip_tags((string) ($valor ?? '')));
    $texto = str_replace(["\r\n", "\r"], "\n", $texto);
    $texto = str_replace("\0", '', $texto);
    return preg_replace("/[ \t]+/", ' ', $texto) ?? '';
}

/** Valida un correo con filter_var. */
function email_valido(mixed $valor): bool
{
    return filter_var((string) $valor, FILTER_VALIDATE_EMAIL) !== false;
}

/** Devuelve el token CSRF de la sesión. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_clase5'])) {
        $_SESSION['csrf_clase5'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_clase5'];
}

/** Verifica el token CSRF enviado por POST. */
function verificar_csrf(): bool
{
    $enviado = $_POST['_csrf'] ?? '';
    return is_string($enviado) && $enviado !== '' && hash_equals(csrf_token(), $enviado);
}

/** Ruta absoluta del archivo JSON de mensajes. */
function archivo_mensajes(): string
{
    return dirname(__DIR__) . '/data/mensajes.json';
}

/** Lee los mensajes guardados. Devuelve [] si el archivo no existe o está roto. */
function leer_mensajes(): array
{
    $archivo = archivo_mensajes();
    if (!is_file($archivo)) {
        return [];
    }

    $contenido = file_get_contents($archivo);
    if ($contenido === false || trim($contenido) === '') {
        return [];
    }

    $datos = json_decode($contenido, true);
    return is_array($datos) ? $datos : [];
}

/**
 * Guarda un mensaje con bloqueo exclusivo para evitar condiciones de carrera.
 * Devuelve true si se pudo escribir.
 */
function guardar_mensaje(array $mensaje): bool
{
    $archivo = archivo_mensajes();
    $directorio = dirname($archivo);

    if (!is_dir($directorio) && !mkdir($directorio, 0775, true) && !is_dir($directorio)) {
        return false;
    }

    $mensajes = leer_mensajes();
    $mensajes[] = $mensaje;

    $json = json_encode($mensajes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    $manejador = fopen($archivo, 'c+');
    if ($manejador === false) {
        return false;
    }

    try {
        if (!flock($manejador, LOCK_EX)) {
            return false;
        }
        ftruncate($manejador, 0);
        rewind($manejador);
        $escrito = fwrite($manejador, $json);
        fflush($manejador);
        flock($manejador, LOCK_UN);
        return $escrito !== false;
    } finally {
        fclose($manejador);
    }
}

/** Devuelve el catálogo de ejemplo para la búsqueda GET. */
function catalogo(): array
{
    $archivo = dirname(__DIR__) . '/data/catalogo.json';
    if (!is_file($archivo)) {
        return [];
    }

    $datos = json_decode((string) file_get_contents($archivo), true);
    return is_array($datos) ? $datos : [];
}

/** Pasa un texto a minúsculas y sin acentos para comparar búsquedas. */
function normalizar_busqueda(string $texto): string
{
    $plano = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
    return strtolower($plano !== false ? $plano : $texto);
}

/**
 * Busca en el catálogo por nombre, categoría o descripción.
 * El texto recibido ya debe venir sanitizado.
 */
function buscar_catalogo(string $texto): array
{
    if ($texto === '') {
        return catalogo();
    }

    $aguja = normalizar_busqueda($texto);
    return array_values(array_filter(catalogo(), static function (array $item) use ($aguja): bool {
        $contenido = normalizar_busqueda(
            ($item['nombre'] ?? '') . ' ' . ($item['categoria'] ?? '') . ' ' . ($item['descripcion'] ?? '')
        );
        return str_contains($contenido, $aguja);
    }));
}

/** Resalta el término buscado dentro de un texto ya escapado. */
function resaltar(string $texto, string $termino): string
{
    $seguro = e($texto);
    if ($termino === '') {
        return $seguro;
    }

    $patron = '/' . preg_quote(e($termino), '/') . '/iu';
    $resultado = preg_replace($patron, '<mark>$0</mark>', $seguro);
    return $resultado ?? $seguro;
}
