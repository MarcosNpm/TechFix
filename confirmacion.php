<?php
/**
 * confirmacion.php · Confirmación de una solicitud de reparación o de una compra.
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/componentes.php';

$codigo = limpiar($_GET['codigo'] ?? '');
$numeroPedido = limpiar($_GET['pedido'] ?? '');

if ($codigo === '' && $numeroPedido === '') {
    flash('info', 'No encontramos una confirmación pendiente.');
    redirigir('index.php');
}

$reparacion = null;
$pedido = null;
$detallePedido = [];

if ($codigo !== '') {
    $stmt = $pdo->prepare(
        "SELECT r.codigo, r.cliente_nombre, r.cliente_email, r.equipo_marca, r.equipo_modelo,
                r.problema, r.created_at, s.nombre AS servicio, s.tiempo_estimado,
                e.nombre AS estado, e.slug AS estado_slug
         FROM reparaciones r
         INNER JOIN servicios s ON s.id = r.servicio_id
         INNER JOIN estados_reparacion e ON e.id = r.estado_id
         WHERE r.codigo = :codigo LIMIT 1"
    );
    $stmt->execute([':codigo' => $codigo]);
    $reparacion = $stmt->fetch() ?: null;
}

if ($numeroPedido !== '') {
    $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE numero = :numero LIMIT 1");
    $stmt->execute([':numero' => $numeroPedido]);
    $pedido = $stmt->fetch() ?: null;

    if ($pedido) {
        $stmtDetalle = $pdo->prepare(
            "SELECT d.cantidad, d.precio_unitario, d.subtotal, p.nombre
             FROM detalle_pedidos d
             INNER JOIN productos p ON p.id = d.producto_id
             WHERE d.pedido_id = :id ORDER BY p.nombre"
        );
        $stmtDetalle->execute([':id' => $pedido['id']]);
        $detallePedido = $stmtDetalle->fetchAll();
    }
}

if (!$reparacion && !$pedido) {
    http_response_code(404);
    $titulo_pagina = 'Confirmación no encontrada';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="error-page">
        <div class="error-page__code">404</div>
        <h1>No encontramos esa confirmación</h1>
        <p>Revisá el código o el número de pedido. Si el problema continúa, escribinos por WhatsApp.</p>
        <a class="btn btn--primary" href="<?= e(url('contacto.php')) ?>">Contactar al taller</a>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$titulo_pagina = 'Solicitud confirmada';
$pagina_activa = 'servicios';

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="background:var(--bg)">
    <div class="contenedor">
        <div class="confirm-card">
            <div class="confirm-icon"><?= icono('check') ?></div>
            <h1><?= $reparacion ? 'Tu reparación quedó registrada' : 'Tu pedido quedó registrado' ?></h1>
            <p>
                <?php if ($reparacion): ?>
                    Gracias, <?= e(explode(' ', $reparacion['cliente_nombre'])[0]) ?>. Ya tenemos los datos de tu equipo.
                    Te contactamos al correo o por WhatsApp para confirmar el diagnóstico.
                <?php else: ?>
                    Gracias, <?= e(explode(' ', $pedido['cliente_nombre'])[0]) ?>. Guardamos tu pedido y te contactamos para coordinar el pago y la entrega.
                <?php endif; ?>
            </p>

            <?php if ($reparacion): ?>
                <div class="confirm-code">
                    <small>Código de seguimiento</small>
                    <strong><?= e($reparacion['codigo']) ?></strong>
                </div>

                <dl class="confirm-summary">
                    <div><dt>Servicio</dt><dd><?= e($reparacion['servicio']) ?></dd></div>
                    <div><dt>Equipo</dt><dd><?= e($reparacion['equipo_marca'] . ' ' . $reparacion['equipo_modelo']) ?></dd></div>
                    <div><dt>Estado actual</dt><dd><?= badge_estado(['slug' => $reparacion['estado_slug'], 'nombre' => $reparacion['estado']]) ?></dd></div>
                    <div><dt>Tiempo estimado</dt><dd><?= e($reparacion['tiempo_estimado']) ?></dd></div>
                    <div><dt>Fecha de ingreso</dt><dd><?= e(fecha($reparacion['created_at'])) ?></dd></div>
                </dl>

                <div class="confirm-actions">
                    <a class="btn btn--primary" href="<?= e(url('seguimiento.php?codigo=' . urlencode($reparacion['codigo']))) ?>">
                        Seguir mi reparación <?= icono('flecha') ?>
                    </a>
                    <a class="btn btn--ghost" href="<?= e(url('index.php')) ?>">Volver al inicio</a>
                </div>
                <p class="muted" style="font-size:.78rem;margin-top:18px">Guardá este código: lo vas a necesitar para consultar el estado sin crear una cuenta.</p>
            <?php else: ?>
                <div class="confirm-code">
                    <small>Número de pedido</small>
                    <strong><?= e($pedido['numero']) ?></strong>
                </div>

                <dl class="confirm-summary">
                    <div><dt>Cliente</dt><dd><?= e($pedido['cliente_nombre']) ?></dd></div>
                    <div><dt>Correo</dt><dd><?= e($pedido['cliente_email']) ?></dd></div>
                    <div><dt>Estado</dt><dd><span class="badge badge--amber"><?= e(ucfirst($pedido['estado'])) ?></span></dd></div>
                    <?php foreach ($detallePedido as $item): ?>
                        <div><dt><?= (int) $item['cantidad'] ?> × <?= e($item['nombre']) ?></dt><dd><?= e(precio($item['subtotal'])) ?></dd></div>
                    <?php endforeach; ?>
                    <div><dt><strong>Total</strong></dt><dd><strong><?= e(precio($pedido['total'])) ?></strong></dd></div>
                </dl>

                <div class="confirm-actions">
                    <a class="btn btn--primary" href="<?= e(url('tienda.php')) ?>">Seguir comprando</a>
                    <a class="btn btn--ghost" href="<?= e(url('contacto.php')) ?>">Consultar por mi pedido</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
