<?php
/**
 * framework/index.php · Módulo independiente con Fat-Free Framework (TP10, Parte B).
 *
 * Demuestra:
 *   - Sistema de enrutamiento del framework.
 *   - Controlador propio (App\Controllers\ReportesController).
 *   - Modelo con el ORM (DB\SQL\Mapper).
 *   - Vista con el motor de plantillas (Template).
 *
 * Rutas:
 *   GET /framework/                  → reporte de stock
 *   GET /framework/reportes          → reporte de stock
 *   GET /framework/reportes/4        → detalle del producto 4
 *   GET /framework/api/stock         → JSON con el stock
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/env.php';
require_once __DIR__ . '/lib/base.php';
require_once __DIR__ . '/lib/magic.php';
require_once __DIR__ . '/lib/db/cursor.php';
require_once __DIR__ . '/lib/db/sql.php';
require_once __DIR__ . '/lib/db/sql/mapper.php';
require_once __DIR__ . '/lib/template.php';
require_once __DIR__ . '/app/models/Producto.php';
require_once __DIR__ . '/app/controllers/ReportesController.php';

$f3 = Base::instance();

$f3->set('DEBUG', APP_DEBUG ? 3 : 0);
$f3->set('UI', __DIR__ . '/app/views/');
$f3->set('TEMP', sys_get_temp_dir() . '/techfix-f3-cache/');
$f3->set('CACHE', false);
$f3->set('ESCAPE', true);

// Rutas del módulo. El router del framework asocia URI → controlador.
$f3->route('GET /', 'App\Controllers\ReportesController->index');
$f3->route('GET /reportes', 'App\Controllers\ReportesController->index');
$f3->route('GET /reportes/@id', 'App\Controllers\ReportesController->detalle');
$f3->route('GET /api/stock', 'App\Controllers\ReportesController->api');

$base = rtrim((string) $f3->get('BASE'), '/');           // /framework
$raiz = rtrim(dirname($base), '/');                       // '' o /techfix

$f3->set('modulo_url', $base);
$f3->set('assets', ($raiz !== '' ? $raiz : '') . '/assets');
$f3->set('sitio_url', ($raiz !== '' ? $raiz : '') . '/index.php');
$f3->set('origen', 'Fat-Free Framework 3.9.3');

$f3->run();
