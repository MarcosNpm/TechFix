<?php
/**
 * servicios.php · Catálogo de servicios (filtro por categoría vía GET).
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/componentes.php';

$titulo_pagina = 'Servicios';
$descripcion_pagina = 'Catálogo completo de reparaciones de celulares, computadoras, electrónica y recuperación de datos.';
$pagina_activa = 'servicios';

$categoriaSlug = limpiar($_GET['categoria'] ?? '');
$categoriaSlug = preg_replace('/[^a-z0-9-]/', '', strtolower($categoriaSlug)) ?? '';

$categorias = $pdo->query(
    "SELECT c.id, c.nombre, c.slug, c.icono, COUNT(s.id) AS total
     FROM categorias_servicio c
     LEFT JOIN servicios s ON s.categoria_id = c.id AND s.activo = 1
     GROUP BY c.id, c.nombre, c.slug, c.icono
     ORDER BY c.id"
)->fetchAll();

if ($categoriaSlug !== '') {
    $stmt = $pdo->prepare(
        "SELECT s.id, s.nombre, s.descripcion, s.precio_base, s.tiempo_estimado,
                c.nombre AS categoria, c.icono, c.slug AS categoria_slug
         FROM servicios s
         INNER JOIN categorias_servicio c ON c.id = s.categoria_id
         WHERE s.activo = 1 AND c.slug = :slug
         ORDER BY s.destacado DESC, s.nombre"
    );
    $stmt->execute([':slug' => $categoriaSlug]);
    $servicios = $stmt->fetchAll();
} else {
    $servicios = $pdo->query(
        "SELECT s.id, s.nombre, s.descripcion, s.precio_base, s.tiempo_estimado,
                c.nombre AS categoria, c.icono, c.slug AS categoria_slug
         FROM servicios s
         INNER JOIN categorias_servicio c ON c.id = s.categoria_id
         WHERE s.activo = 1
         ORDER BY c.id, s.destacado DESC, s.nombre"
    )->fetchAll();
}

$categoriaActual = null;
foreach ($categorias as $categoria) {
    if ($categoria['slug'] === $categoriaSlug) {
        $categoriaActual = $categoria;
        break;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= e(url('index.php')) ?>">Inicio</a> <?= icono('flecha') ?>
            <span>Servicios</span>
        </nav>
        <span class="eyebrow"><?= icono('herramienta') ?> Taller especializado</span>
        <h1><?= $categoriaActual ? e($categoriaActual['nombre']) : 'Todos nuestros servicios' ?></h1>
        <p>
            <?php if ($categoriaActual): ?>
                <?= e($categoriaActual['nombre']) ?>: diagnósticos claros, repuestos de calidad y garantía escrita.
            <?php else: ?>
                Elegí la categoría que necesitás. Todos los trabajos incluyen diagnóstico previo y presupuesto sin cargo.
            <?php endif; ?>
        </p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="filters" role="navigation" aria-label="Filtrar servicios por categoría">
            <a class="filter<?= $categoriaSlug === '' ? ' active' : '' ?>" href="<?= e(url('servicios.php')) ?>">
                Todos <span class="filter__count"><?= array_sum(array_column($categorias, 'total')) ?></span>
            </a>
            <?php foreach ($categorias as $categoria): ?>
                <a class="filter<?= $categoriaSlug === $categoria['slug'] ? ' active' : '' ?>"
                   href="<?= e(url('servicios.php?categoria=' . urlencode($categoria['slug']))) ?>">
                    <?= icono($categoria['icono']) ?> <?= e($categoria['nombre']) ?>
                    <span class="filter__count"><?= (int) $categoria['total'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($servicios)): ?>
            <div class="grid-3">
                <?php foreach ($servicios as $servicio): ?>
                    <?= tarjeta_servicio($servicio) ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state__icon"><?= icono('buscar') ?></div>
                <h3>No encontramos servicios en esta categoría</h3>
                <p>Probá con otra categoría o pedinos un presupuesto personalizado.</p>
                <a class="btn btn--primary" href="<?= e(url('solicitud.php')) ?>">Solicitar presupuesto</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section--soft">
    <div class="contenedor">
        <div class="cta-band" style="border-radius:var(--radius-xl);padding:52px 44px">
            <div class="cta-band__grid">
                <div>
                    <h2>¿No sabés qué servicio necesitás?</h2>
                    <p>Describinos la falla y te orientamos. El diagnóstico inicial no tiene costo.</p>
                </div>
                <div class="cta-band__actions">
                    <a class="btn btn--light" href="<?= e(url('solicitud.php')) ?>">Contar mi problema</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
