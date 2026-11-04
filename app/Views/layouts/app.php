<?php
/**
 * app/Views/layouts/app.php · Layout principal del módulo MVC nativo.
 * Variables esperadas: $titulo, $descripcion, $contenido.
 */
$titulo = $titulo ?? 'Módulo MVC';
$descripcion = $descripcion ?? 'Catálogo TechFix refactorizado a MVC.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($titulo) ?> · TechFix MVC</title>
    <meta name="description" content="<?= h($descripcion) ?>">
    <link rel="icon" href="<?= h(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= h(url('assets/css/estilos.css')) ?>">
</head>
<body>
<a class="skip-link" href="#contenido">Saltar al contenido principal</a>

<header class="site-header">
    <div class="contenedor site-header__inner">
        <a class="logo" href="<?= h(url('mvc/index.php')) ?>" aria-label="TechFix MVC · Inicio">
            <span class="logo__mark" aria-hidden="true">
                <svg viewBox="0 0 40 40" fill="none">
                    <rect x="2" y="2" width="36" height="36" rx="11" fill="#1A1A2E"/>
                    <path d="M23.8 10.5a6.4 6.4 0 0 0-8.1 8.1L9 25.3v4.4h4.4l6.7-6.7a6.4 6.4 0 0 0 8.1-8.1l-3.4 3.4-3.1-3.1 2.1-4.7Z" fill="#00A8CC"/>
                </svg>
            </span>
            <span class="logo__text">Tech<span>Fix</span> <small style="font-size:.6rem;letter-spacing:.12em;color:#68707f;text-transform:uppercase">MVC</small></span>
        </a>

        <nav class="nav" aria-label="Navegación del módulo MVC">
            <ul class="nav__list">
                <li><a class="nav__link" href="<?= h(url('index.php')) ?>">Sitio principal</a></li>
                <li><a class="nav__link<?= (($_GET['accion'] ?? 'index') === 'index' && empty($_GET['accion'])) ? ' active' : '' ?>" href="<?= h(mvc_url()) ?>">Catálogo</a></li>
                <li><a class="nav__link" href="<?= h(url('tienda.php')) ?>">Tienda original</a></li>
                <li><a class="nav__link" href="<?= h(url('framework/')) ?>">Versión framework</a></li>
            </ul>
        </nav>
    </div>
</header>

<main id="contenido">
<?= $contenido ?>
</main>

<footer class="site-footer">
    <div class="contenedor footer-bottom" style="border:0">
        <p>© <?= date('Y') ?> TechFix · Módulo MVC nativo (TP10)</p>
        <p class="footer-bottom__meta">Modelo · Vista · Controlador · Núñez Marcos Agustín</p>
    </div>
</footer>
</body>
</html>
