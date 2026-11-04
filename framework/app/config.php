<?php
/**
 * framework/app/config.php · Configuración del módulo Fat-Free.
 * Reutiliza las variables del .env del proyecto principal.
 */

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/env.php';

return [
    'dsn' => sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        env('DB_HOST', '127.0.0.1'),
        env('DB_PORT', '3306'),
        env('DB_NAME', 'techfix_db'),
        env('DB_CHARSET', 'utf8mb4')
    ),
    'user'     => (string) env('DB_USER', 'root'),
    'password' => (string) env('DB_PASS', ''),
];
