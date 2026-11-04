<?php
/**
 * includes/admin_sidebar.php
 * Navegación lateral del panel de administración.
 * La variable $admin_activo define la sección actual.
 */
$admin_activo = $admin_activo ?? 'panel';
?>
<aside class="admin-sidebar">
    <p class="admin-sidebar__title">Administración</p>
    <nav aria-label="Secciones del panel administrativo">
        <a class="admin-sidebar__link<?= $admin_activo === 'panel' ? ' active' : '' ?>" href="<?= e(url('admin_panel.php')) ?>"><?= icono('panel') ?> Resumen</a>
        <a class="admin-sidebar__link<?= $admin_activo === 'servicios' ? ' active' : '' ?>" href="<?= e(url('admin/index.php')) ?>"><?= icono('herramienta') ?> Servicios</a>
        <a class="admin-sidebar__link<?= $admin_activo === 'reparaciones' ? ' active' : '' ?>" href="<?= e(url('admin/reparaciones.php')) ?>"><?= icono('lista') ?> Reparaciones</a>
        <a class="admin-sidebar__link<?= $admin_activo === 'usuarios' ? ' active' : '' ?>" href="<?= e(url('admin/usuarios.php')) ?>"><?= icono('usuario') ?> Usuarios</a>
        <a class="admin-sidebar__link" href="<?= e(url('tienda.php')) ?>"><?= icono('carrito') ?> Ver tienda</a>
        <a class="admin-sidebar__link" href="<?= e(url('index.php')) ?>"><?= icono('flecha') ?> Ir al sitio</a>
        <a class="admin-sidebar__link" href="<?= e(url('logout.php')) ?>"><?= icono('salir') ?> Cerrar sesión</a>
    </nav>
</aside>
