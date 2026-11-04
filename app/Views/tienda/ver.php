<?php
/**
 * app/Views/tienda/ver.php · Vista de detalle de un producto (MVC).
 */
?>
<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= h(url('index.php')) ?>">Inicio</a>
            <?= icono('flecha') ?>
            <a href="<?= h(mvc_url()) ?>">Catálogo MVC</a>
            <?= icono('flecha') ?>
            <span><?= h($producto['nombre']) ?></span>
        </nav>
        <span class="eyebrow"><?= icono('carrito') ?> <?= h($producto['categoria']) ?></span>
        <h1><?= h($producto['nombre']) ?></h1>
        <p><?= h($producto['descripcion_corta'] ?? '') ?></p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="grid-2" style="align-items:start">
            <div class="panel" style="text-align:center">
                <img src="<?= h(url('assets/img/' . $producto['imagen'])) ?>"
                     alt="<?= h($producto['nombre']) ?>"
                     style="max-width:320px;width:100%;height:auto;border-radius:18px;background:#f4f7f9;padding:24px">
            </div>

            <div class="panel">
                <span class="badge badge--cyan"><?= h($producto['categoria']) ?></span>
                <p style="font-size:1.6rem;font-weight:800;color:var(--primary);margin:14px 0 4px"><?= h(money($producto['precio'])) ?></p>
                <p>
                    <span class="product-card__stock <?= h(stock_clase((int) $producto['stock'])) ?>">
                        <?= h(stock_texto((int) $producto['stock'])) ?>
                    </span>
                </p>

                <h2>Descripción</h2>
                <p><?= nl2br(h($producto['descripcion'] ?? 'Sin descripción adicional.')) ?></p>

                <div class="form-actions" style="justify-content:flex-start">
                    <a class="btn btn--primary" href="<?= h(url('tienda.php')) ?>">Comprar en la tienda</a>
                    <a class="btn btn--ghost" href="<?= h(mvc_url()) ?>">Volver al catálogo MVC</a>
                </div>

                <div class="tracking-note" style="margin-top:20px">
                    <?= icono('info') ?>
                    <span>Esta vista recibió el producto ya consultado por <code>Producto::encontrar()</code>.
                        No ejecuta SQL ni procesa parámetros.</span>
                </div>
            </div>
        </div>
    </div>
</section>
