<?php
/**
 * admin_panel.php · Panel principal protegido para el rol admin (TP8).
 */

require_once __DIR__ . '/includes/auth_guard.php';
verificar_rol('admin');

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/componentes.php';
require_once __DIR__ . '/includes/iconos.php';

$titulo_pagina = 'Panel de administración';
$descripcion_pagina = 'Resumen general de TechFix.';
$pagina_activa = 'admin';
$admin_activo = 'panel';

$totales = $pdo->query(
    "SELECT
        (SELECT COUNT(*) FROM usuarios WHERE activo = 1) AS usuarios,
        (SELECT COUNT(*) FROM reparaciones WHERE estado_id NOT IN (SELECT id FROM estados_reparacion WHERE slug = 'entregado')) AS reparaciones_activas,
        (SELECT COUNT(*) FROM servicios WHERE activo = 1) AS servicios,
        (SELECT COUNT(*) FROM productos WHERE activo = 1) AS productos,
        (SELECT COALESCE(SUM(total), 0) FROM pedidos WHERE estado <> 'cancelado') AS ventas,
        (SELECT COUNT(*) FROM consultas WHERE leido = 0) AS consultas"
)->fetch();

$reparaciones = $pdo->query(
    "SELECT r.codigo, r.cliente_nombre, r.equipo_marca, r.equipo_modelo, r.created_at,
            s.nombre AS servicio, e.nombre AS estado, e.slug AS estado_slug
     FROM reparaciones r
     INNER JOIN servicios s ON s.id = r.servicio_id
     INNER JOIN estados_reparacion e ON e.id = r.estado_id
     ORDER BY r.created_at DESC LIMIT 6"
)->fetchAll();

$stockBajo = $pdo->query(
    "SELECT id, nombre, stock, precio FROM productos
     WHERE activo = 1 AND stock <= 5
     ORDER BY stock ASC, nombre LIMIT 6"
)->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= e(url('index.php')) ?>">Inicio</a> <?= icono('flecha') ?>
            <span>Panel de administración</span>
        </nav>
        <span class="eyebrow"><?= icono('panel') ?> Rol administrador</span>
        <h1>Panel de administración</h1>
        <p>Resumen del taller, el catálogo y los pedidos. Desde acá podés gestionar los servicios y el estado de las reparaciones.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="admin-layout">
            <?php require __DIR__ . '/includes/admin_sidebar.php'; ?>

            <div>
                <div class="stat-cards">
                    <article class="stat-card">
                        <span class="stat-card__label"><?= icono('herramienta') ?> Reparaciones activas</span>
                        <strong class="stat-card__value"><?= (int) $totales['reparaciones_activas'] ?></strong>
                        <span class="stat-card__hint">en el taller</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-card__label"><?= icono('carrito') ?> Ventas</span>
                        <strong class="stat-card__value" style="font-size:1.25rem"><?= e(precio($totales['ventas'])) ?></strong>
                        <span class="stat-card__hint">pedidos no cancelados</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-card__label"><?= icono('usuario') ?> Usuarios</span>
                        <strong class="stat-card__value"><?= (int) $totales['usuarios'] ?></strong>
                        <span class="stat-card__hint">cuentas activas</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-card__label"><?= icono('mail') ?> Consultas</span>
                        <strong class="stat-card__value"><?= (int) $totales['consultas'] ?></strong>
                        <span class="stat-card__hint">sin leer</span>
                    </article>
                </div>

                <div class="panel">
                    <div class="panel__head">
                        <div><h2>Últimas reparaciones</h2><p>Seguimiento rápido de los ingresos más recientes.</p></div>
                        <a class="btn btn--primary btn--sm" href="<?= e(url('admin/reparaciones.php')) ?>">Gestionar</a>
                    </div>
                    <?php if ($reparaciones): ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead><tr><th>Código</th><th>Cliente</th><th>Equipo</th><th>Servicio</th><th>Estado</th><th>Ingreso</th></tr></thead>
                                <tbody>
                                    <?php foreach ($reparaciones as $reparacion): ?>
                                        <tr>
                                            <td><strong><?= e($reparacion['codigo']) ?></strong></td>
                                            <td><?= e($reparacion['cliente_nombre']) ?></td>
                                            <td><?= e($reparacion['equipo_marca'] . ' ' . $reparacion['equipo_modelo']) ?></td>
                                            <td><?= e($reparacion['servicio']) ?></td>
                                            <td><?= badge_estado($reparacion) ?></td>
                                            <td><?= e(fecha($reparacion['created_at'], false)) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="muted">No hay reparaciones registradas.</p>
                    <?php endif; ?>
                </div>

                <div class="grid-2">
                    <div class="panel">
                        <div class="panel__head">
                            <div><h2>Stock bajo</h2><p>Productos con 5 unidades o menos.</p></div>
                            <a class="btn btn--ghost btn--sm" href="<?= e(url('tienda.php')) ?>">Ver tienda</a>
                        </div>
                        <?php if ($stockBajo): ?>
                            <ul style="display:grid;gap:10px">
                                <?php foreach ($stockBajo as $producto): ?>
                                    <li style="display:flex;justify-content:space-between;gap:10px;padding-bottom:9px;border-bottom:1px solid var(--border);font-size:.84rem">
                                        <span><?= e($producto['nombre']) ?></span>
                                        <span class="badge <?= (int) $producto['stock'] === 0 ? 'badge--red' : 'badge--amber' ?>"><?= (int) $producto['stock'] ?> u.</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="muted" style="font-size:.85rem">Todo el catálogo tiene stock suficiente.</p>
                        <?php endif; ?>
                    </div>

                    <div class="panel">
                        <div class="panel__head">
                            <div><h2>Catálogo</h2><p>Servicios disponibles en el sitio.</p></div>
                            <a class="btn btn--primary btn--sm" href="<?= e(url('admin/crear.php')) ?>">Nuevo servicio</a>
                        </div>
                        <div style="display:grid;gap:12px">
                            <div style="display:flex;justify-content:space-between;font-size:.86rem"><span class="muted">Servicios activos</span><strong><?= (int) $totales['servicios'] ?></strong></div>
                            <div style="display:flex;justify-content:space-between;font-size:.86rem"><span class="muted">Productos activos</span><strong><?= (int) $totales['productos'] ?></strong></div>
                        </div>
                        <div class="tracking-note" style="margin-top:18px">
                            <?= icono('info') ?>
                            <span>El CRUD de servicios usa PDO con sentencias preparadas. Podés crear, editar y eliminar registros desde el menú lateral.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
