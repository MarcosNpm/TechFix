<?php
/**
 * includes/nav.php
 * Navegación principal del sitio. Usa $pagina_activa para resaltar la sección.
 */

$usuario = usuario_actual();
$cantidadCarrito = carrito_cantidad();
?>
<header class="site-header">
    <div class="contenedor site-header__inner">
        <a class="logo" href="<?= e(url('index.php')) ?>" aria-label="<?= e(APP_NAME) ?> · Inicio">
            <span class="logo__mark" aria-hidden="true">
                <svg viewBox="0 0 40 40" fill="none">
                    <rect x="2" y="2" width="36" height="36" rx="11" fill="#1A1A2E"/>
                    <path d="M23.8 10.5a6.4 6.4 0 0 0-8.1 8.1L9 25.3v4.4h4.4l6.7-6.7a6.4 6.4 0 0 0 8.1-8.1l-3.4 3.4-3.1-3.1 2.1-4.7Z" fill="#00A8CC"/>
                    <circle cx="30" cy="30" r="3" fill="#00A8CC" opacity=".55"/>
                </svg>
            </span>
            <span class="logo__text">Tech<span>Fix</span></span>
        </a>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="menuPrincipal" data-nav-toggle>
            <?= icono('menu') ?>
            <span class="sr-only">Abrir menú</span>
        </button>

        <nav class="nav" id="menuPrincipal" aria-label="Navegación principal" data-nav>
            <ul class="nav__list">
                <li><a class="nav__link<?= menu_activo('inicio', $pagina_activa) ?>" href="<?= e(url('index.php')) ?>">Inicio</a></li>
                <li><a class="nav__link<?= menu_activo('servicios', $pagina_activa) ?>" href="<?= e(url('servicios.php')) ?>">Servicios</a></li>
                <li><a class="nav__link<?= menu_activo('tienda', $pagina_activa) ?>" href="<?= e(url('tienda.php')) ?>">Tienda</a></li>
                <li><a class="nav__link<?= menu_activo('seguimiento', $pagina_activa) ?>" href="<?= e(url('seguimiento.php')) ?>">Seguimiento</a></li>
                <li><a class="nav__link<?= menu_activo('taller', $pagina_activa) ?>" href="<?= e(url('sobre-el-taller.php')) ?>">Sobre el taller</a></li>
                <li><a class="nav__link<?= menu_activo('contacto', $pagina_activa) ?>" href="<?= e(url('contacto.php')) ?>">Contacto</a></li>
            </ul>

            <div class="nav__actions">
                <a class="nav-cart" href="<?= e(url('carrito.php')) ?>" aria-label="Carrito: <?= (int) $cantidadCarrito ?> productos">
                    <?= icono('carrito') ?>
                    <?php if ($cantidadCarrito > 0): ?>
                        <span class="nav-cart__badge"><?= (int) $cantidadCarrito ?></span>
                    <?php endif; ?>
                </a>

                <?php if ($usuario): ?>
                    <div class="nav-user">
                        <a class="nav-user__link" href="<?= e(url('dashboard.php')) ?>">
                            <span class="nav-user__avatar"><?= e(strtoupper(substr($usuario['nombre'], 0, 1))) ?></span>
                            <span class="nav-user__name"><?= e(explode(' ', $usuario['nombre'])[0]) ?></span>
                        </a>
                        <?php if ($usuario['rol'] === 'admin'): ?>
                            <a class="nav__link nav__link--admin" href="<?= e(url('admin_panel.php')) ?>"><?= icono('panel') ?> Panel</a>
                        <?php endif; ?>
                        <a class="nav__link nav__link--logout" href="<?= e(url('logout.php')) ?>"><?= icono('salir') ?> Salir</a>
                    </div>
                <?php else: ?>
                    <a class="btn btn--ghost btn--sm" href="<?= e(url('login.php')) ?>">Ingresar</a>
                    <a class="btn btn--primary btn--sm" href="<?= e(url('registro.php')) ?>">Crear cuenta</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
