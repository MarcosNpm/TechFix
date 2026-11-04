<?php
/**
 * api/buscar_productos.php · Endpoint JSON para la búsqueda asincrónica (TP9).
 *
 * Método: GET
 * Parámetros: q (texto), categoria (slug), limite (1-48)
 * Respuestas:
 *   200 → {"status":"success","total":N,"data":[...]}
 *   400 → parámetro inválido
 *   405 → método no permitido
 *   500 → error interno
 */

define('API_CONTEXT', true);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
header('X-Content-Type-Options: nosniff');

/** Envía una respuesta JSON y termina la ejecución. */
function responder(int $codigo, array $cuerpo): never
{
    http_response_code($codigo);
    echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET');
    responder(405, [
        'status'  => 'error',
        'message' => 'Método no permitido. Usá GET.',
    ]);
}

$busqueda = limpiar($_GET['q'] ?? '');
$categoria = strtolower(limpiar($_GET['categoria'] ?? ''));
$categoria = preg_replace('/[^a-z0-9-]/', '', $categoria) ?? '';

$limite = filter_input(INPUT_GET, 'limite', FILTER_VALIDATE_INT);
$limite = $limite === null || $limite === false ? 24 : $limite;

if ($limite < 1 || $limite > 48) {
    responder(400, [
        'status'  => 'error',
        'message' => 'El parámetro «limite» debe ser un entero entre 1 y 48.',
    ]);
}

if (strlen($busqueda) > 80) {
    responder(400, [
        'status'  => 'error',
        'message' => 'El texto de búsqueda no puede superar los 80 caracteres.',
    ]);
}

try {
    $sql = "SELECT p.id, p.nombre, p.descripcion, p.descripcion_corta, p.precio, p.stock, p.imagen,
                   c.nombre AS categoria, c.slug AS categoria_slug
            FROM productos p
            INNER JOIN categorias_producto c ON c.id = p.categoria_id
            WHERE p.activo = 1";
    $parametros = [];

    if ($busqueda !== '') {
        $sql .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ? OR c.nombre LIKE ?)";
        $like = '%' . $busqueda . '%';
        $parametros[] = $like;
        $parametros[] = $like;
        $parametros[] = $like;
    }
    if ($categoria !== '') {
        $sql .= " AND c.slug = ?";
        $parametros[] = $categoria;
    }

    $sql .= " ORDER BY p.stock > 0 DESC, p.nombre LIMIT ?";
    $parametros[] = $limite;

    $stmt = $pdo->prepare($sql);
    foreach ($parametros as $indice => $valor) {
        $tipo = is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue($indice + 1, $valor, $tipo);
    }
    $stmt->execute();

    $productos = $stmt->fetchAll();

    $datos = array_map(static function (array $producto): array {
        return [
            'id'                => (int) $producto['id'],
            'nombre'            => $producto['nombre'],
            'descripcion_corta' => $producto['descripcion_corta'] ?? resumen((string) ($producto['descripcion'] ?? ''), 70),
            'precio'            => (float) $producto['precio'],
            'precio_formateado' => precio($producto['precio']),
            'stock'             => (int) $producto['stock'],
            'imagen'            => $producto['imagen'],
            'categoria'         => $producto['categoria'],
            'categoria_slug'    => $producto['categoria_slug'],
        ];
    }, $productos);

    responder(200, [
        'status'   => 'success',
        'consulta' => $busqueda,
        'categoria' => $categoria,
        'total'    => count($datos),
        'data'     => $datos,
    ]);
} catch (Throwable $e) {
    error_log('[TechFix] Error en API de productos: ' . $e->getMessage());
    responder(500, [
        'status'  => 'error',
        'message' => 'Ocurrió un error interno al buscar productos. Intentá nuevamente en unos segundos.',
    ]);
}
