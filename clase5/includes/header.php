<?php
/**
 * clase5/includes/header.php
 * Cabecera común de la demo de la Clase 5.
 * Debe definirse $titulo antes de incluir este archivo.
 */
$titulo = $titulo ?? 'Clase 5';
$activo = $activo ?? 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> · TechFix Clase 5</title>
    <meta name="description" content="Demo de procesamiento de formularios POST y GET con sanitización y validación en servidor.">
    <link rel="icon" href="../assets/img/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/clase5.css">
    <script defer src="assets/clase5.js"></script>
</head>
<body>
<a class="skip" href="#contenido">Saltar al contenido</a>

<header class="c5-header">
    <div class="c5-wrap c5-header__inner">
        <a class="c5-logo" href="index.php">
            <span class="c5-logo__mark">TF</span>
            <span>Tech<span>Fix</span> <small>Clase 5</small></span>
        </a>
        <nav class="c5-nav" aria-label="Navegación de la demo">
            <a class="<?= $activo === 'inicio' ? 'active' : '' ?>" href="index.php">Formularios</a>
            <a class="<?= $activo === 'mensajes' ? 'active' : '' ?>" href="mensajes.php">Mensajes guardados</a>
            <a href="../index.php">Volver al sitio</a>
        </nav>
    </div>
</header>

<main id="contenido" class="c5-wrap c5-main">
