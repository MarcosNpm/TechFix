<?php
/**
 * includes/auth_guard.php
 * Guardián de rutas: exige sesión iniciada y, opcionalmente, un rol.
 */

require_once __DIR__ . '/bootstrap.php';

if (!isset($_SESSION['usuario_id'])) {
    $destino = url('login.php?error=acceso_denegado');
    if (!headers_sent()) {
        header('Location: ' . $destino);
    }
    exit;
}

/**
 * Restringe el acceso a un rol determinado.
 * Si el usuario no tiene el rol, responde 403 y detiene la ejecución.
 */
function verificar_rol(string $rolRequerido): void
{
    if (($_SESSION['rol'] ?? '') === $rolRequerido) {
        return;
    }

    http_response_code(403);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">'
        . '<meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<title>403 · Acceso denegado</title>'
        . '<style>body{font-family:"Segoe UI",Arial,sans-serif;background:#F4F7F9;color:#1A1A2E;margin:0;min-height:100vh;display:grid;place-items:center}'
        . '.box{max-width:520px;padding:42px;background:#fff;border-radius:20px;box-shadow:0 18px 50px rgba(26,26,46,.12);border-top:5px solid #B42318;text-align:center}'
        . 'h1{margin-top:0;font-size:2rem}code{background:#F4F7F9;padding:2px 6px;border-radius:6px}'
        . 'a{display:inline-block;margin-top:18px;padding:11px 20px;border-radius:999px;background:#00A8CC;color:#fff;text-decoration:none;font-weight:700}</style></head><body>'
        . '<main class="box"><h1>403 · Acceso denegado</h1>'
        . '<p>Tu cuenta no tiene permisos para entrar a esta sección. Esta vista está reservada al rol <code>' . e($rolRequerido) . '</code>.</p>'
        . '<a href="' . e(url('dashboard.php')) . '">Volver a mi panel</a></main></body></html>';
    exit;
}
