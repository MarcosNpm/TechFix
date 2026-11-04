<?php
/**
 * dashboard.php · Panel protegido del cliente (TP8).
 */

require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/componentes.php';

$usuario = usuario_actual();
$titulo_pagina = 'Mi panel';
$descripcion_pagina = 'Historial de reparaciones y pedidos de tu cuenta TechFix.';
$pagina_activa = 'dashboard';

$stmt = $pdo->prepare(
    "SELECT r.id, r.codigo, r.equipo_marca, r.equipo_modelo, r.problema, r.costo_final,
            r.created_at, s.nombre AS servicio, e.nombre AS estado, e.slug AS estado_slug
     FROM reparaciones r
     INNER JOIN servicios s ON s.id = r.servicio_id
     INNER JOIN estados_reparacion e ON e.id = r.estado_id
     WHERE r.usuario_id = :id OR r.cliente_email = :email
     ORDER BY r.created_at DESC"
);
$stmt->execute([':id' => $usuario['id'], ':email' => $usuario['email']]);
$reparaciones = $stmt->fetchAll();

$stmtPedidos = $pdo->prepare(
    "SELECT p.*, COUNT(d.id) AS items
     FROM pedidos p
     LEFT JOIN detalle_pedidos d ON d.pedido_id = p.id
     WHERE p.usuario_id = :id OR p.cliente_email = :email
     GROUP BY p.id
     ORDER BY p.created_at DESC"
);
$stmtPedidos->execute([':id' => $usuario['id'], ':email' => $usuario['email']]);
$pedidos = $stmtPedidos->fetchAll();

$activas = 0;
$listas = 0;
foreach ($reparaciones as $reparacion) {
    if ($reparacion['estado_slug'] !== 'entregado') {
        $activas++;
    }
    if ($reparacion['estado_slug'] === 'listo') {
        $listas++;
    }
}
$totalPedidos = 0;
foreach ($pedidos as $pedido) {
    if ($pedido['estado'] !== 'cancelado') {
        $totalPedidos += (float) $pedido['total'];
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= e(url('index.php')) ?>">Inicio</a> <?= icono('flecha') ?>
            <span>Mi panel</span>
        </nav>
        <span class="eyebrow"><?= icono('usuario') ?> Área personal</span>
        <h1>Hola, <?= e(explode(' ', $usuario['nombre'])[0]) ?></h1>
        <p>Desde acá podés seguir tus reparaciones, revisar tus pedidos y consultar tus datos de contacto.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="dashboard-layout">
            <aside class="account-nav">
                <div class="account-nav__user">
                    <span class="account-nav__avatar"><?= e(strtoupper(substr($usuario['nombre'], 0, 1))) ?></span>
                    <div>
                        <strong><?= e($usuario['nombre']) ?></strong>
                        <small><?= e($usuario['email']) ?></small>
                    </div>
                </div>
                <nav class="account-nav__list" aria-label="Secciones de mi cuenta">
                    <a class="account-nav__link active" href="<?= e(url('dashboard.php')) ?>"><?= icono('panel') ?> Resumen</a>
                    <a class="account-nav__link" href="#reparaciones"><?= icono('herramienta') ?> Mis reparaciones</a>
                    <a class="account-nav__link" href="#pedidos"><?= icono('carrito') ?> Mis pedidos</a>
                    <a class="account-nav__link" href="<?= e(url('seguimiento.php')) ?>"><?= icono('buscar') ?> Seguimiento</a>
                    <?php if (es_admin()): ?>
                        <a class="account-nav__link" href="<?= e(url('admin_panel.php')) ?>"><?= icono('grafico') ?> Panel admin</a>
                    <?php endif; ?>
                    <a class="account-nav__link" href="<?= e(url('logout.php')) ?>"><?= icono('salir') ?> Cerrar sesión</a>
                </nav>
            </aside>

            <div class="dashboard-main">
                <div class="stat-cards">
                    <article class="stat-card">
                        <span class="stat-card__label"><?= icono('herramienta') ?> Reparaciones</span>
                        <strong class="stat-card__value"><?= count($reparaciones) ?></strong>
                        <span class="stat-card__hint"><?= $activas ?> activas</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-card__label"><?= icono('check-circle') ?> Listas</span>
                        <strong class="stat-card__value"><?= $listas ?></strong>
                        <span class="stat-card__hint">para retirar</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-card__label"><?= icono('carrito') ?> Pedidos</span>
                        <strong class="stat-card__value"><?= count($pedidos) ?></strong>
                        <span class="stat-card__hint">en la tienda</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-card__label"><?= icono('tarjeta') ?> Compras</span>
                        <strong class="stat-card__value" style="font-size:1.15rem"><?= e(precio($totalPedidos)) ?></strong>
                        <span class="stat-card__hint">total histórico</span>
                    </article>
                </div>

                <section class="panel" id="reparaciones">
                    <div class="panel__head">
                        <div>
                            <h2>Mis reparaciones</h2>
                            <p>Estado actualizado de cada equipo que pasó por el taller.</p>
                        </div>
                        <a class="btn btn--primary btn--sm" href="<?= e(url('solicitud.php')) ?>">Nueva solicitud</a>
                    </div>

                    <?php if (empty($reparaciones)): ?>
                        <div class="empty-state" style="padding:36px 20px">
                            <div class="empty-state__icon"><?= icono('herramienta') ?></div>
                            <h3>Todavía no tenés reparaciones</h3>
                            <p>Cuando registres un equipo, va a aparecer acá con su código de seguimiento.</p>
                            <a class="btn btn--primary" href="<?= e(url('servicios.php')) ?>">Ver servicios</a>
                        </div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr><th>Código</th><th>Equipo</th><th>Servicio</th><th>Estado</th><th>Ingreso</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reparaciones as $reparacion): ?>
                                        <tr>
                                            <td><strong><?= e($reparacion['codigo']) ?></strong></td>
                                            <td><?= e($reparacion['equipo_marca'] . ' ' . $reparacion['equipo_modelo']) ?></td>
                                            <td><?= e($reparacion['servicio']) ?></td>
                                            <td><?= badge_estado($reparacion) ?></td>
                                            <td><?= e(fecha($reparacion['created_at'], false)) ?></td>
                                            <td><a class="btn btn--ghost btn--sm" href="<?= e(url('seguimiento.php?codigo=' . urlencode($reparacion['codigo']))) ?>">Ver</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="panel" id="pedidos">
                    <div class="panel__head">
                        <div>
                            <h2>Mis pedidos</h2>
                            <p>Compras de repuestos y accesorios realizadas en la tienda.</p>
                        </div>
                        <a class="btn btn--ghost btn--sm" href="<?= e(url('tienda.php')) ?>">Ir a la tienda</a>
                    </div>

                    <?php if (empty($pedidos)): ?>
                        <div class="empty-state" style="padding:36px 20px">
                            <div class="empty-state__icon"><?= icono('carrito') ?></div>
                            <h3>No hay pedidos todavía</h3>
                            <p>Los repuestos que compres van a quedar registrados en tu historial.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead><tr><th>Pedido</th><th>Fecha</th><th>Productos</th><th>Total</th><th>Estado</th></tr></thead>
                                <tbody>
                                    <?php foreach ($pedidos as $pedido): ?>
                                        <tr>
                                            <td><strong><?= e($pedido['numero']) ?></strong></td>
                                            <td><?= e(fecha($pedido['created_at'], false)) ?></td>
                                            <td><?= (int) $pedido['items'] ?></td>
                                            <td><?= e(precio($pedido['total'])) ?></td>
                                            <td><span class="badge badge--cyan"><?= e(ucfirst($pedido['estado'])) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="panel">
                    <div class="panel__head">
                        <div><h2>Mis datos</h2><p>Información asociada a tu cuenta.</p></div>
                    </div>
                    <div class="form-grid">
                        <div><span class="form-help">Nombre</span><p style="margin:2px 0 0;color:var(--navy);font-weight:700"><?= e($usuario['nombre']) ?></p></div>
                        <div><span class="form-help">Correo</span><p style="margin:2px 0 0;color:var(--navy);font-weight:700"><?= e($usuario['email']) ?></p></div>
                        <div><span class="form-help">Rol</span><p style="margin:2px 0 0;color:var(--navy);font-weight:700"><?= e(ucfirst($usuario['rol'])) ?></p></div>
                        <div><span class="form-help">Sesión iniciada</span><p style="margin:2px 0 0;color:var(--navy);font-weight:700"><?= e(fecha(date('Y-m-d H:i:s', $_SESSION['login_en'] ?? time()))) ?></p></div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
