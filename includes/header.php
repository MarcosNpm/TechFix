<?php
/**
 * includes/header.php
 * Plantilla modular: <head>, metadatos, apertura de <body> y cabecera.
 * La variable $titulo_pagina debe definirse antes de incluir este archivo.
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/iconos.php';

$titulo_pagina      = $titulo_pagina ?? 'Inicio';
$descripcion_pagina = $descripcion_pagina ?? 'TechFix: taller de reparación de celulares, computadoras y electrónica. Presupuestos, seguimiento de reparaciones y venta de repuestos.';
$pagina_activa      = $pagina_activa ?? 'inicio';
$body_class         = $body_class ?? '';
$flashes            = obtener_flashes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo_pagina) ?> · <?= e(APP_NAME) ?></title>
    <meta name="description" content="<?= e($descripcion_pagina) ?>">
    <meta name="theme-color" content="#1A1A2E">
    <meta property="og:title" content="<?= e($titulo_pagina) ?> · <?= e(APP_NAME) ?>">
    <meta property="og:description" content="<?= e($descripcion_pagina) ?>">
    <meta property="og:type" content="website">
    <link rel="icon" href="<?= e(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(url('assets/css/estilos.css')) ?>">
    <noscript><style>.reveal{opacity:1;transform:none}</style></noscript>
    <script defer src="<?= e(url('assets/js/main.js')) ?>"></script>
</head>
<body class="<?= e(trim($body_class)) ?>">
<a class="skip-link" href="#contenido">Saltar al contenido principal</a>

<div class="topbar">
    <div class="contenedor topbar__inner">
        <span class="topbar__item"><?= icono('pin') ?> <?= e(SHOP_ADDRESS) ?></span>
        <span class="topbar__item"><?= icono('reloj') ?> Lun a Vie · 9 a 19 h · Sáb · 9 a 13 h</span>
        <a class="topbar__item topbar__item--link" href="tel:<?= e(preg_replace('/\s+/', '', SUPPORT_PHONE)) ?>">
            <?= icono('telefono') ?> <?= e(SUPPORT_PHONE) ?>
        </a>
    </div>
</div>

<?php require __DIR__ . '/nav.php'; ?>

<?php if (!empty($flashes)): ?>
    <div class="flash-stack" role="status" aria-live="polite">
        <?php foreach ($flashes as $flash): ?>
            <div class="flash flash--<?= e($flash['tipo']) ?>" data-flash>
                <span class="flash__icon"><?= icono($flash['tipo'] === 'success' ? 'check-circle' : ($flash['tipo'] === 'error' ? 'info' : 'info')) ?></span>
                <span><?= e($flash['mensaje']) ?></span>
                <button type="button" class="flash__close" data-cerrar-flash aria-label="Cerrar mensaje"><?= icono('cerrar') ?></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<main id="contenido">
