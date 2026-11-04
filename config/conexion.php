<?php
/**
 * config/conexion.php
 * Conexión PDO segura a MySQL.
 *
 * - Usa excepciones (PDOException) para detectar errores.
 * - Desactiva la emulación de sentencias preparadas.
 * - Devuelve arrays asociativos.
 * - No muestra credenciales ni mensajes internos del motor al usuario.
 */

require_once __DIR__ . '/env.php';
require_once dirname(__DIR__) . '/includes/funciones.php';

$dbHost    = (string) env('DB_HOST', '127.0.0.1');
$dbPuerto  = (string) env('DB_PORT', '3306');
$dbNombre  = (string) env('DB_NAME', 'techfix_db');
$dbUsuario = (string) env('DB_USER', 'root');
$dbClave   = (string) env('DB_PASS', '');
$dbCharset = (string) env('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host={$dbHost};port={$dbPuerto};dbname={$dbNombre};charset={$dbCharset}";

$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_STRINGIFY_FETCHES  => false,
];

try {
    $pdo = new PDO($dsn, $dbUsuario, $dbClave, $opciones);
} catch (PDOException $e) {
    // El detalle queda en el log del servidor, nunca en la pantalla del usuario.
    error_log('[TechFix] Error de conexión PDO: ' . $e->getMessage());

    if (!headers_sent()) {
        http_response_code(500);
    }

    if (defined('API_CONTEXT') && API_CONTEXT === true) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status'  => 'error',
            'message' => 'No se pudo conectar con la base de datos.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $mensajeDebug = APP_DEBUG ? e($e->getMessage()) : 'Revisá la configuración del archivo .env.';

    echo '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">'
        . '<meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<title>Error de conexión · ' . e(APP_NAME) . '</title>'
        . '<style>body{font-family:Arial,sans-serif;background:#F4F7F9;color:#1A1A2E;margin:0;display:grid;place-items:center;min-height:100vh}'
        . '.box{background:#fff;max-width:620px;padding:40px;border-radius:18px;box-shadow:0 18px 50px rgba(26,26,46,.12);border-top:6px solid #00A8CC}'
        . 'h1{margin-top:0}code{background:#F4F7F9;padding:2px 6px;border-radius:6px}a{color:#00A8CC}</style></head><body>'
        . '<main class="box"><h1>No pudimos conectar con la base de datos</h1>'
        . '<p>Verificá que MySQL esté iniciado y que los datos de <code>.env</code> sean correctos.</p>'
        . '<p>Importá el esquema con <code>tp6/schema.sql</code> y los datos con <code>tp6/consultas.sql</code>.</p>'
        . '<p><small>Detalle técnico: ' . $mensajeDebug . '</small></p>'
        . '<p><a href="' . e(defined('BASE_URL') ? BASE_URL : '/') . 'index.php">Volver al inicio</a></p></main></body></html>';
    exit;
}
