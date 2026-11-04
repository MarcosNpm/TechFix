<?php
/**
 * servicio.php · Detalle de un servicio.
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/componentes.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$servicio = null;

if ($id !== false && $id !== null && $id > 0) {
    $stmt = $pdo->prepare(
        "SELECT s.*, c.nombre AS categoria, c.slug AS categoria_slug, c.icono
         FROM servicios s
         INNER JOIN categorias_servicio c ON c.id = s.categoria_id
         WHERE s.id = :id AND s.activo = 1
         LIMIT 1"
    );
    $stmt->execute([':id' => $id]);
    $servicio = $stmt->fetch() ?: null;
}

if (!$servicio) {
    http_response_code(404);
    $titulo_pagina = 'Servicio no encontrado';
    $pagina_activa = 'servicios';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="error-page">
        <div class="error-page__code">404</div>
        <h1>No encontramos ese servicio</h1>
        <p>Puede que el enlace esté vencido o que el servicio ya no esté disponible. Mirá el catálogo completo.</p>
        <a class="btn btn--primary" href="<?= e(url('servicios.php')) ?>">Ver todos los servicios</a>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$titulo_pagina = $servicio['nombre'];
$descripcion_pagina = resumen($servicio['descripcion'], 155);
$pagina_activa = 'servicios';

$stmtTags = $pdo->prepare(
    "SELECT et.nombre FROM etiquetas et
     INNER JOIN servicio_etiqueta se ON se.etiqueta_id = et.id
     WHERE se.servicio_id = :id ORDER BY et.nombre"
);
$stmtTags->execute([':id' => $id]);
$etiquetas = $stmtTags->fetchAll();

$stmtRelacionados = $pdo->prepare(
    "SELECT s.id, s.nombre, s.descripcion, s.precio_base, s.tiempo_estimado,
            c.nombre AS categoria, c.icono
     FROM servicios s
     INNER JOIN categorias_servicio c ON c.id = s.categoria_id
     WHERE s.activo = 1 AND s.categoria_id = :categoria AND s.id <> :id
     ORDER BY s.destacado DESC, s.nombre
     LIMIT 3"
);
$stmtRelacionados->execute([':categoria' => $servicio['categoria_id'], ':id' => $id]);
$relacionados = $stmtRelacionados->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= e(url('index.php')) ?>">Inicio</a> <?= icono('flecha') ?>
            <a href="<?= e(url('servicios.php')) ?>">Servicios</a> <?= icono('flecha') ?>
            <a href="<?= e(url('servicios.php?categoria=' . urlencode($servicio['categoria_slug']))) ?>"><?= e($servicio['categoria']) ?></a>
            <?= icono('flecha') ?>
            <span><?= e($servicio['nombre']) ?></span>
        </nav>
        <span class="eyebrow"><?= icono($servicio['icono']) ?> <?= e($servicio['categoria']) ?></span>
        <h1><?= e($servicio['nombre']) ?></h1>
        <p><?= e(resumen($servicio['descripcion'], 170)) ?></p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="detail-grid">
            <div class="detail-panel">
                <h2>En qué consiste</h2>
                <p><?= nl2br(e($servicio['descripcion'])) ?></p>

                <h2>Qué incluye</h2>
                <ul class="detail-list">
                    <li><?= icono('check-circle') ?><span>Diagnóstico previo y presupuesto sin cargo.</span></li>
                    <li><?= icono('check-circle') ?><span>Mano de obra especializada y herramientas de precisión.</span></li>
                    <li><?= icono('check-circle') ?><span>Pruebas de funcionamiento antes de la entrega.</span></li>
                    <li><?= icono('check-circle') ?><span>Garantía escrita de <?= (int) $servicio['garantia_dias'] ?> días.</span></li>
                    <li><?= icono('check-circle') ?><span>Informe técnico con el trabajo realizado.</span></li>
                    <li><?= icono('check-circle') ?><span>Seguimiento online con código de orden.</span></li>
                </ul>

                <?php if (!empty($etiquetas)): ?>
                    <h2>Etiquetas</h2>
                    <div style="display:flex;flex-wrap:wrap;gap:8px">
                        <?php foreach ($etiquetas as $etiqueta): ?>
                            <span class="badge badge--cyan"><?= e($etiqueta['nombre']) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h2>Antes de dejar tu equipo</h2>
                <p class="muted" style="font-size:.9rem">
                    Hacé un respaldo de tus datos si el equipo enciende. Si no enciende, avisanos qué información
                    te importa recuperar: lo evaluamos junto con la reparación. No realizamos cambios sin tu aprobación.
                </p>
            </div>

            <aside class="buy-box">
                <span class="badge badge--gray"><?= e($servicio['categoria']) ?></span>
                <div class="buy-box__price" style="margin-top:12px">
                    <strong><?= e(precio($servicio['precio_base'])) ?></strong>
                    <small>precio desde</small>
                </div>
                <p class="muted" style="font-size:.8rem;margin:0">El valor final se confirma después del diagnóstico.</p>

                <ul class="buy-box__meta">
                    <li><?= icono('reloj') ?> Tiempo estimado: <strong><?= e($servicio['tiempo_estimado']) ?></strong></li>
                    <li><?= icono('escudo') ?> Garantía de <strong><?= (int) $servicio['garantia_dias'] ?> días</strong></li>
                    <li><?= icono('check-circle') ?> Diagnóstico sin cargo</li>
                    <li><?= icono('camion') ?> Retiro y entrega en el local</li>
                </ul>

                <a class="btn btn--primary btn--block btn--lg" href="<?= e(url('solicitud.php?servicio=' . (int) $servicio['id'])) ?>">
                    Solicitar este servicio <?= icono('flecha') ?>
                </a>
                <a class="btn btn--ghost btn--block" href="https://wa.me/<?= e(WHATSAPP) ?>?text=<?= rawurlencode('Hola, quiero consultar por ' . $servicio['nombre']) ?>" target="_blank" rel="noopener" style="margin-top:10px">
                    <?= icono('whatsapp') ?> Consultar por WhatsApp
                </a>
                <p class="buy-box__note"><?= icono('info') ?> No se cobra el diagnóstico si rechazás el presupuesto.</p>
            </aside>
        </div>
    </div>
</section>

<?php if (!empty($relacionados)): ?>
<section class="section section--soft">
    <div class="contenedor">
        <div class="section-head">
            <div class="section-head__text">
                <span class="section-kicker">Seguir explorando</span>
                <h2>Otros servicios de <?= e($servicio['categoria']) ?></h2>
            </div>
        </div>
        <div class="grid-3">
            <?php foreach ($relacionados as $relacionado): ?>
                <?= tarjeta_servicio($relacionado) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
