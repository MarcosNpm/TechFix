<?php
/**
 * app/Views/error/404.php · Vista de error del módulo MVC.
 */
?>
<section class="section">
    <div class="contenedor">
        <div class="empty-state">
            <div class="empty-state__icon"><?= icono('buscar') ?></div>
            <h1>404 · No encontrado</h1>
            <p><?= h($mensaje ?? 'La página que buscás no existe.') ?></p>
            <a class="btn btn--primary" href="<?= h(mvc_url()) ?>">Volver al catálogo MVC</a>
        </div>
    </div>
</section>
