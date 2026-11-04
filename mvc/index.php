<?php
/**
 * mvc/index.php · Front controller del módulo MVC nativo (TP10, Parte A).
 *
 * Flujo:
 *   1. Arranque común (sesión, entorno, BASE_URL).
 *   2. Conexión PDO.
 *   3. Autoload de las clases de app/.
 *   4. El controlador procesa la solicitud y elige el modelo y la vista.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/config/conexion.php';
require_once dirname(__DIR__) . '/includes/iconos.php';
require_once __DIR__ . '/../app/Views/helpers.php';

spl_autoload_register(static function (string $clase): void {
    $prefijo = 'App\\';
    if (!str_starts_with($clase, $prefijo)) {
        return;
    }

    $ruta = dirname(__DIR__) . '/app/' . str_replace('\\', '/', substr($clase, strlen($prefijo))) . '.php';
    if (is_file($ruta)) {
        require_once $ruta;
    }
});

use App\Controllers\TiendaController;
use App\Core\View;
use App\Models\Producto;

$vista = new View(dirname(__DIR__) . '/app/Views');
$controlador = new TiendaController($vista, new Producto($pdo));

$accion = $_GET['accion'] ?? 'index';
$accion = is_string($accion) ? strtolower(preg_replace('/[^a-zA-Z]/', '', $accion) ?? '') : 'index';

try {
    switch ($accion) {
        case '':
        case 'index':
            $controlador->index();
            break;
        case 'ver':
            $controlador->ver();
            break;
        default:
            $controlador->noEncontrado('La acción «' . e($accion) . '» no existe en este módulo.');
            break;
    }
} catch (Throwable $e) {
    error_log('[TechFix MVC] ' . $e->getMessage());
    http_response_code(500);
    echo $vista->render('error/404', [
        'titulo'      => 'Error interno',
        'descripcion' => 'Ocurrió un error al procesar la solicitud.',
        'mensaje'     => APP_DEBUG
            ? 'Detalle: ' . $e->getMessage()
            : 'Ocurrió un error interno. Intentá nuevamente en unos segundos.',
    ]);
}
