<?php
/**
 * app/Views/tienda/index.php · Vista del catálogo MVC.
 * Solo presenta datos ya preparados por el controlador.
 */
?>
<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= h(url('index.php')) ?>">Inicio</a>
            <?= icono('flecha') ?>
            <span>Catálogo MVC</span>
        </nav>
        <span class="eyebrow"><?= icono('panel') ?> Modelo · Vista · Controlador</span>
        <h1>Catálogo refactorizado</h1>
        <p>
            Este módulo toma el catálogo de productos y separa las responsabilidades:
            <strong>Producto</strong> consulta la base con PDO,
            <strong>TiendaController</strong> procesa la solicitud y
            <strong>esta vista</strong> solo muestra el HTML.
        </p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <form class="shop-toolbar" method="get" action="<?= h(url('mvc/index.php')) ?>" role="search">
            <input type="hidden" name="accion" value="index">
            <div class="shop-search">
                <?= icono('buscar') ?>
                <label class="sr-only" for="q">Buscar repuesto</label>
                <input type="search" id="q" name="q" value="<?= h($busqueda) ?>"
                       maxlength="60" placeholder="Buscar por repuesto, categoría…" autocomplete="off">
            </div>
            <?php if ($categoria !== ''): ?>
                <input type="hidden" name="categoria" value="<?= h($categoria) ?>">
            <?php endif; ?>
            <button class="btn btn--primary" type="submit">Buscar</button>
            <div class="shop-meta">
                <span><strong><?= (int) $total ?></strong> productos</span>
            </div>
        </form>

        <div class="filters" role="navigation" aria-label="Filtrar por categoría">
            <a class="filter<?= $categoria === '' ? ' active' : '' ?>"
               href="<?= h(mvc_url('index', $busqueda !== '' ? ['q' => $busqueda] : [])) ?>">
                Todos <span class="filter__count"><?= array_sum(array_column($categorias, 'total')) ?></span>
            </a>
            <?php foreach ($categorias as $cat): ?>
                <a class="filter<?= $categoria === $cat['slug'] ? ' active' : '' ?>"
                   href="<?= h(mvc_url('index', array_filter(['categoria' => $cat['slug'], 'q' => $busqueda]))) ?>">
                    <?= h($cat['nombre']) ?> <span class="filter__count"><?= (int) $cat['total'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($productos): ?>
            <div class="grid-4">
                <?php foreach ($productos as $producto): ?>
                    <article class="product-card">
                        <div class="product-card__media">
                            <span class="product-card__badge"><?= h($producto['categoria']) ?></span>
                            <img src="<?= h(url('assets/img/' . $producto['imagen'])) ?>"
                                 alt="<?= h($producto['nombre']) ?>" loading="lazy">
                        </div>
                        <div class="product-card__body">
                            <h3><a href="<?= h(mvc_url('ver', ['id' => (int) $producto['id']])) ?>"><?= h($producto['nombre']) ?></a></h3>
                            <p class="product-card__desc"><?= h($producto['descripcion_corta'] ?? '') ?></p>
                            <div class="product-card__foot">
                                <div>
                                    <span class="product-card__price"><?= h(money($producto['precio'])) ?></span>
                                    <span class="product-card__stock <?= h(stock_clase((int) $producto['stock'])) ?>">
                                        <?= h(stock_texto((int) $producto['stock'])) ?>
                                    </span>
                                </div>
                                <a class="btn btn--ghost btn--sm" href="<?= h(mvc_url('ver', ['id' => (int) $producto['id']])) ?>">Ver</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state__icon"><?= icono('buscar') ?></div>
                <h3>Sin resultados</h3>
                <p>No encontramos productos para «<?= h($busqueda) ?>».</p>
                <a class="btn btn--primary" href="<?= h(mvc_url()) ?>">Ver todos</a>
            </div>
        <?php endif; ?>

        <div class="tracking-note" style="margin-top:26px">
            <?= icono('info') ?>
            <span>
                Archivos del módulo: <code>app/Models/Producto.php</code>,
                <code>app/Controllers/TiendaController.php</code>,
                <code>app/Views/tienda/index.php</code> y el front controller
                <code>mvc/index.php</code>. Las consultas usan <code>prepare()</code>/<code>execute()</code>.
            </span>
        </div>
    </div>
</section>
