<?php
/**
 * tienda.php · Catálogo de repuestos con búsqueda asincrónica (TP9).
 * La primera carga se renderiza en el servidor; luego Fetch API actualiza la grilla.
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/componentes.php';

$titulo_pagina = 'Tienda de repuestos';
$descripcion_pagina = 'Repuestos y accesorios para celulares y computadoras con stock real y precios visibles.';
$pagina_activa = 'tienda';

$busqueda = limpiar($_GET['q'] ?? '');
$categoriaSlug = limpiar($_GET['categoria'] ?? '');
$categoriaSlug = preg_replace('/[^a-z0-9-]/', '', strtolower($categoriaSlug)) ?? '';

$categorias = $pdo->query(
    "SELECT c.id, c.nombre, c.slug, COUNT(p.id) AS total
     FROM categorias_producto c
     LEFT JOIN productos p ON p.categoria_id = c.id AND p.activo = 1
     GROUP BY c.id, c.nombre, c.slug
     ORDER BY c.id"
)->fetchAll();

$sql = "SELECT p.id, p.nombre, p.descripcion, p.descripcion_corta, p.precio, p.stock, p.imagen,
               c.nombre AS categoria, c.slug AS categoria_slug
        FROM productos p
        INNER JOIN categorias_producto c ON c.id = p.categoria_id
        WHERE p.activo = 1";
$parametros = [];

if ($busqueda !== '') {
    $sql .= " AND (p.nombre LIKE :busqueda OR p.descripcion LIKE :busqueda OR c.nombre LIKE :busqueda)";
    $parametros[':busqueda'] = '%' . $busqueda . '%';
}
if ($categoriaSlug !== '') {
    $sql .= " AND c.slug = :categoria";
    $parametros[':categoria'] = $categoriaSlug;
}
$sql .= " ORDER BY p.stock > 0 DESC, p.nombre";

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$productos = $stmt->fetchAll();
$totalProductos = count($productos);

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
            <span>Tienda</span>
        </nav>
        <span class="eyebrow"><?= icono('carrito') ?> Repuestos con stock real</span>
        <h1>Repuestos y accesorios</h1>
        <p>Buscá por nombre, filtrá por categoría y agregá al carrito. Los precios y el stock se actualizan desde la base de datos.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <form class="shop-toolbar" method="get" action="<?= e(url('tienda.php')) ?>" id="formBusquedaProductos" role="search">
            <div class="shop-search">
                <?= icono('buscar') ?>
                <label class="sr-only" for="busquedaProductos">Buscar repuestos</label>
                <input type="search" id="busquedaProductos" name="q" value="<?= e($busqueda) ?>"
                       placeholder="Buscar por repuesto, marca o categoría…" autocomplete="off"
                       aria-controls="resultadosProductos" aria-describedby="estadoBusqueda">
            </div>
            <?php if ($categoriaSlug !== ''): ?>
                <input type="hidden" name="categoria" value="<?= e($categoriaSlug) ?>">
            <?php endif; ?>
            <button class="btn btn--primary" type="submit">Buscar</button>
            <div class="shop-meta">
                <span id="estadoBusqueda" aria-live="polite"></span>
                <span><strong id="contadorProductos"><?= (int) $totalProductos ?></strong> productos</span>
            </div>
        </form>

        <div class="filters" role="navigation" aria-label="Filtrar productos por categoría">
            <a class="filter<?= $categoriaSlug === '' ? ' active' : '' ?>" href="<?= e(url('tienda.php' . ($busqueda !== '' ? '?q=' . urlencode($busqueda) : ''))) ?>"
               data-categoria-producto="">Todos <span class="filter__count"><?= array_sum(array_column($categorias, 'total')) ?></span></a>
            <?php foreach ($categorias as $categoria): ?>
                <a class="filter<?= $categoriaSlug === $categoria['slug'] ? ' active' : '' ?>"
                   href="<?= e(url('tienda.php?categoria=' . urlencode($categoria['slug']) . ($busqueda !== '' ? '&q=' . urlencode($busqueda) : ''))) ?>"
                   data-categoria-producto="<?= e($categoria['slug']) ?>">
                    <?= e($categoria['nombre']) ?> <span class="filter__count"><?= (int) $categoria['total'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="grid-4" id="resultadosProductos"
             data-api="<?= e(url('api/buscar_productos.php')) ?>"
             data-base="<?= e(url('')) ?>"
             data-csrf="<?= e(csrf_token()) ?>"
             data-categoria="<?= e($categoriaSlug) ?>">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <?= tarjeta_producto($producto) ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" style="grid-column:1/-1">
                    <div class="empty-state__icon"><?= icono('buscar') ?></div>
                    <h3>No encontramos productos</h3>
                    <p>Probá con otra palabra o mirá todas las categorías.</p>
                    <a class="btn btn--primary" href="<?= e(url('tienda.php')) ?>">Ver todos</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="alert alert--info" style="margin-top:28px">
            <?= icono('info') ?>
            <div>
                <strong>Búsqueda en tiempo real (TP9)</strong>
                La grilla se consulta con Fetch API al endpoint <code>api/buscar_productos.php</code>. Si JavaScript está deshabilitado, el formulario sigue funcionando con GET y recarga la página.
            </div>
        </div>
    </div>
</section>

<section class="section section--soft">
    <div class="contenedor">
        <div class="section-head">
            <div class="section-head__text">
                <span class="section-kicker">Compra protegida</span>
                <h2>¿Cómo funciona la tienda?</h2>
                <p>Agregás los repuestos al carrito, revisás el total y confirmás tus datos. Después coordinamos el pago y la entrega.</p>
            </div>
            <a class="section-head__link" href="<?= e(url('carrito.php')) ?>">Ver mi carrito <?= icono('flecha') ?></a>
        </div>
        <div class="steps">
            <article class="step"><h3>Elegí tus repuestos</h3><p>Filtrá por categoría y consultá el stock disponible en tiempo real.</p></article>
            <article class="step"><h3>Revisá el carrito</h3><p>Ajustá cantidades y verificá el subtotal antes de confirmar.</p></article>
            <article class="step"><h3>Dejá tus datos</h3><p>Usamos el correo para enviarte el número de pedido y coordinar la entrega.</p></article>
            <article class="step"><h3>Retirá o coordinamos</h3><p>Podés pasar por el local o acordar el envío dentro de la zona.</p></article>
        </div>
    </div>
</section>

<?php
$script_pagina = 'assets/js/tienda.js';
require_once __DIR__ . '/includes/footer.php';
?>
