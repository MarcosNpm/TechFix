<?php
/**
 * seguimiento.php · Consulta pública del estado de una reparación (GET).
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/componentes.php';

$titulo_pagina = 'Seguimiento de reparación';
$descripcion_pagina = 'Consultá el estado de tu reparación con el código de orden.';
$pagina_activa = 'seguimiento';

$codigo = strtoupper(limpiar($_GET['codigo'] ?? ''));
$codigo = preg_replace('/[^A-Z0-9-]/', '', $codigo) ?? '';
$consultaRealizada = $codigo !== '';
$reparacion = null;
$estados = [];
$error = '';

if ($consultaRealizada) {
    if (strlen($codigo) < 6 || strlen($codigo) > 20) {
        $error = 'El código debe tener entre 6 y 20 caracteres.';
    } else {
        $stmt = $pdo->prepare(
            "SELECT r.*, s.nombre AS servicio, s.tiempo_estimado, c.nombre AS categoria,
                    e.nombre AS estado_nombre, e.slug AS estado_slug, e.orden AS estado_orden
             FROM reparaciones r
             INNER JOIN servicios s ON s.id = r.servicio_id
             INNER JOIN categorias_servicio c ON c.id = s.categoria_id
             INNER JOIN estados_reparacion e ON e.id = r.estado_id
             WHERE r.codigo = :codigo LIMIT 1"
        );
        $stmt->execute([':codigo' => $codigo]);
        $reparacion = $stmt->fetch() ?: null;

        if (!$reparacion) {
            $error = 'No encontramos una reparación con ese código. Revisá que esté bien escrito.';
        } else {
            $estados = $pdo->query("SELECT * FROM estados_reparacion ORDER BY orden")->fetchAll();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= e(url('index.php')) ?>">Inicio</a> <?= icono('flecha') ?>
            <span>Seguimiento</span>
        </nav>
        <span class="eyebrow"><?= icono('reloj') ?> Estado en tiempo real</span>
        <h1>Seguí tu reparación</h1>
        <p>Ingresá el código que te entregamos al registrar el equipo. No necesitás crear una cuenta.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor" style="max-width:900px">
        <form class="shop-toolbar" method="get" action="<?= e(url('seguimiento.php')) ?>" style="margin-bottom:26px">
            <div class="shop-search">
                <?= icono('buscar') ?>
                <label class="sr-only" for="codigo">Código de reparación</label>
                <input type="text" id="codigo" name="codigo" value="<?= e($codigo) ?>" placeholder="Ej.: TF-260901-A1B2C3"
                       maxlength="20" autocomplete="off" required>
            </div>
            <button class="btn btn--primary" type="submit">Consultar <?= icono('flecha') ?></button>
        </form>

        <?php if ($error !== ''): ?>
            <div class="alert alert--error">
                <?= icono('info') ?>
                <div><strong>No pudimos encontrar la orden</strong><?= e($error) ?></div>
            </div>
            <p class="muted" style="font-size:.8rem">Código de ejemplo para la demo: <strong>TF-260901-A1B2C3</strong></p>
        <?php endif; ?>

        <?php if ($reparacion): ?>
            <article class="tracking-card">
                <div class="tracking-header">
                    <div>
                        <span class="tracking-code"><?= icono('chip') ?> <?= e($reparacion['codigo']) ?></span>
                        <h2><?= e($reparacion['servicio']) ?></h2>
                        <p><?= e($reparacion['equipo_marca'] . ' ' . $reparacion['equipo_modelo']) ?> · Ingresó el <?= e(fecha($reparacion['created_at'], false)) ?></p>
                    </div>
                    <div style="text-align:right">
                        <?= badge_estado(['slug' => $reparacion['estado_slug'], 'nombre' => $reparacion['estado_nombre']]) ?>
                        <p style="margin:7px 0 0;font-size:.75rem">Tiempo estimado: <?= e($reparacion['tiempo_estimado']) ?></p>
                    </div>
                </div>

                <div class="timeline">
                    <?php foreach ($estados as $estado): ?>
                        <?php
                        $orden = (int) $estado['orden'];
                        $actual = (int) $reparacion['estado_orden'];
                        $clase = $orden < $actual ? ' completado' : ($orden === $actual ? ' actual' : '');
                        ?>
                        <div class="timeline__item<?= $clase ?>">
                            <span class="timeline__dot"><?= $orden < $actual ? icono('check') : $orden ?></span>
                            <div class="timeline__content">
                                <strong><?= e($estado['nombre']) ?></strong>
                                <small><?= e($estado['descripcion']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($reparacion['observaciones'])): ?>
                    <div class="tracking-note">
                        <?= icono('info') ?>
                        <div><strong>Observaciones del técnico</strong><br><?= nl2br(e($reparacion['observaciones'])) ?></div>
                    </div>
                <?php else: ?>
                    <div class="tracking-note">
                        <?= icono('info') ?>
                        <span>El técnico todavía no cargó observaciones. Se actualizan a medida que avanza la reparación.</span>
                    </div>
                <?php endif; ?>

                <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:24px">
                    <a class="btn btn--ghost" href="<?= e(url('seguimiento.php')) ?>">Consultar otro código</a>
                    <a class="btn btn--outline" href="https://wa.me/<?= e(WHATSAPP) ?>?text=<?= rawurlencode('Hola, quiero consultar por la orden ' . $reparacion['codigo']) ?>" target="_blank" rel="noopener">
                        <?= icono('whatsapp') ?> Consultar por WhatsApp
                    </a>
                </div>
            </article>
        <?php endif; ?>

        <?php if (!$reparacion): ?>
            <div class="grid-3" style="margin-top:34px">
                <div class="step">
                    <h3>1. Buscá tu código</h3>
                    <p>Está en el correo de confirmación y en el comprobante que te dimos al recibir el equipo.</p>
                </div>
                <div class="step">
                    <h3>2. Consultá el estado</h3>
                    <p>Vas a ver la línea de avance: recibido, diagnóstico, reparación, control y listo.</p>
                </div>
                <div class="step">
                    <h3>3. Coordiná el retiro</h3>
                    <p>Cuando figure “Listo para retirar”, podés pasar por el local en horario de atención.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
