<?php
/**
 * includes/funciones.php
 * Funciones auxiliares reutilizables: escape, sanitización, CSRF, flashes,
 * formato de datos y utilidades de navegación.
 */

require_once dirname(__DIR__) . '/config/env.php';

/** Escapa cualquier dato antes de imprimirlo en HTML (anti-XSS). */
function e(mixed $valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Sanitiza una entrada de texto: quita etiquetas, espacios sobrantes y bytes nulos. */
function limpiar(mixed $valor): string
{
    $texto = trim(strip_tags((string) ($valor ?? '')));
    $texto = str_replace("\0", '', $texto);
    return preg_replace('/\s+/u', ' ', $texto) ?? '';
}

/** Igual que limpiar() pero conserva los saltos de línea de los textarea. */
function limpiar_multilinea(mixed $valor): string
{
    $texto = trim(strip_tags((string) ($valor ?? '')));
    $texto = str_replace(["\r\n", "\r"], "\n", $texto);
    $texto = str_replace("\0", '', $texto);
    return preg_replace("/[ \t]+/", ' ', $texto) ?? '';
}

/** Valida un correo electrónico con filter_var. */
function email_valido(mixed $valor): bool
{
    return filter_var((string) $valor, FILTER_VALIDATE_EMAIL) !== false;
}

/** Devuelve un valor anterior de formulario (para no perder lo escrito). */
function old(string $clave, mixed $porDefecto = ''): mixed
{
    return $_SESSION['old'][$clave] ?? $porDefecto;
}

/** Guarda temporalmente los datos de un formulario fallido. */
function guardar_old(array $datos): void
{
    $_SESSION['old'] = $datos;
}

/** Borra los datos temporales de formulario. */
function limpiar_old(): void
{
    unset($_SESSION['old']);
}

/** Agrega un mensaje flash a la sesión. */
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

/** Recupera y elimina los mensajes flash. */
function obtener_flashes(): array
{
    $mensajes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $mensajes;
}

/** Devuelve el token CSRF de la sesión (lo crea si no existe). */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Imprime el campo oculto con el token CSRF. */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

/** Verifica el token CSRF recibido por POST. */
function verificar_csrf(): bool
{
    $enviado = $_POST['_csrf'] ?? '';
    return is_string($enviado) && $enviado !== '' && hash_equals(csrf_token(), $enviado);
}

/** Construye una URL interna respetando la carpeta del proyecto. */
function url(string $ruta = ''): string
{
    $base = defined('BASE_URL') ? BASE_URL : '/';
    return $base . ltrim($ruta, '/');
}

/** Redirige a una ruta interna y detiene la ejecución. */
function redirigir(string $ruta): never
{
    if (!headers_sent()) {
        header('Location: ' . url($ruta));
    }
    exit;
}

/** Indica si un ítem del menú está activo. */
function menu_activo(string $pagina, string $actual): string
{
    return $pagina === $actual ? ' active' : '';
}

/** Formatea un precio en pesos argentinos. */
function precio(float|int|string|null $valor): string
{
    return '$ ' . number_format((float) $valor, 0, ',', '.');
}

/** Formatea una fecha de MySQL para mostrarla en pantalla. */
function fecha(?string $fecha, bool $conHora = true): string
{
    if (empty($fecha)) {
        return '—';
    }
    $ts = strtotime($fecha);
    return $ts ? date($conHora ? 'd/m/Y H:i' : 'd/m/Y', $ts) : '—';
}

/** Genera un código único de reparación, por ejemplo TF-261103-A1B2C3. */
function generar_codigo(string $prefijo = 'TF'): string
{
    return $prefijo . '-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
}

/** Convierte un texto en slug apto para URLs. */
function slug(string $texto): string
{
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto) ?: $texto;
    $texto = strtolower($texto);
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto) ?? '';
    return trim($texto, '-');
}

/** Genera un slug único para una tabla permitida (servicios o productos). */
function slug_unico(PDO $pdo, string $tabla, string $texto, int $excluirId = 0): string
{
    if (!in_array($tabla, ['servicios', 'productos', 'categorias_servicio', 'categorias_producto'], true)) {
        throw new InvalidArgumentException('Tabla no permitida para generar slug.');
    }

    $base = slug($texto);
    if ($base === '') {
        $base = 'registro';
    }

    $candidato = $base;
    $contador = 2;
    do {
        $sql = "SELECT COUNT(*) FROM {$tabla} WHERE slug = :slug" . ($excluirId > 0 ? " AND id <> :id" : "");
        $stmt = $pdo->prepare($sql);
        $parametros = [':slug' => $candidato];
        if ($excluirId > 0) {
            $parametros[':id'] = $excluirId;
        }
        $stmt->execute($parametros);
        if ((int) $stmt->fetchColumn() === 0) {
            return $candidato;
        }
        $candidato = $base . '-' . $contador;
        $contador++;
    } while ($contador < 100);

    return $base . '-' . time();
}

/** Recorta un texto largo para tarjetas y resúmenes. */
function resumen(string $texto, int $largo = 120): string
{
    if (strlen($texto) <= $largo) {
        return $texto;
    }
    $cortado = substr($texto, 0, $largo);
    $ultimoEspacio = strrpos($cortado, ' ');
    return rtrim($ultimoEspacio !== false ? substr($cortado, 0, $ultimoEspacio) : $cortado) . '…';
}

/** Cantidad total de productos del carrito guardado en sesión. */
function carrito_cantidad(): int
{
    $carrito = $_SESSION['carrito'] ?? [];
    return array_sum(array_map('intval', is_array($carrito) ? $carrito : []));
}

/** Devuelve el usuario autenticado o null. */
function usuario_actual(): ?array
{
    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }
    return [
        'id'     => (int) $_SESSION['usuario_id'],
        'nombre' => (string) ($_SESSION['usuario_nombre'] ?? ''),
        'email'  => (string) ($_SESSION['usuario_email'] ?? ''),
        'rol'    => (string) ($_SESSION['rol'] ?? 'usuario'),
    ];
}

/** Indica si el usuario autenticado tiene rol admin. */
function es_admin(): bool
{
    return ($_SESSION['rol'] ?? '') === 'admin';
}

/** Devuelve la clase CSS de una etiqueta de estado. */
function clase_estado(string $slug): string
{
    return match ($slug) {
        'recibido'          => 'estado-recibido',
        'diagnostico'       => 'estado-diagnostico',
        'reparacion'        => 'estado-reparacion',
        'control-calidad'   => 'estado-control',
        'listo'             => 'estado-listo',
        'entregado'         => 'estado-entregado',
        default             => 'estado-neutral',
    };
}

/** Devuelve un mensaje legible para los códigos de error de login. */
function mensaje_error_login(string $codigo): string
{
    return match ($codigo) {
        'acceso_denegado' => 'Tenés que iniciar sesión para acceder a esa sección.',
        'sesion_expirada' => 'Tu sesión expiró. Volvé a ingresar.',
        'credenciales'    => 'El correo o la contraseña no son correctos.',
        default           => '',
    };
}
