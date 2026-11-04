<?php
/**
 * clase5/mensajes.php · Listado de los mensajes persistidos por el formulario POST.
 * Demuestra que los datos guardados se vuelven a escapar al mostrarse.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/funciones.php';

$titulo = 'Mensajes guardados';
$activo = 'mensajes';

$mensajes = array_reverse(leer_mensajes());

require_once __DIR__ . '/includes/header.php';
?>

<section class="c5-hero c5-hero--compact">
    <span class="c5-pill">Persistencia</span>
    <h1>Mensajes guardados</h1>
    <p>
        Estos son los envíos del formulario POST almacenados en
        <code>clase5/data/mensajes.json</code>. Cada campo se escapa nuevamente al renderizar.
    </p>
    <div class="c5-hero__stats">
        <span><strong><?= count($mensajes) ?></strong> mensajes</span>
        <a class="c5-link" href="index.php">Volver a los formularios</a>
    </div>
</section>

<?php if (!$mensajes): ?>
    <div class="c5-empty c5-empty--box">
        <strong>Todavía no hay mensajes</strong>
        <p>Completá el formulario de contacto de la demo para ver la persistencia en acción.</p>
        <a class="c5-btn" href="index.php#contacto">Ir al formulario</a>
    </div>
<?php else: ?>
    <div class="c5-messages">
        <?php foreach ($mensajes as $mensaje): ?>
            <article class="c5-message">
                <header>
                    <div>
                        <h2><?= e($mensaje['nombre'] ?? 'Sin nombre') ?></h2>
                        <p><?= e($mensaje['email'] ?? '') ?><?= !empty($mensaje['telefono']) ? ' · ' . e($mensaje['telefono']) : '' ?></p>
                    </div>
                    <span class="c5-message__date"><?= e(date('d/m/Y H:i', strtotime((string) ($mensaje['fecha'] ?? 'now')))) ?></span>
                </header>
                <p class="c5-message__subject"><?= e($mensaje['asunto'] ?? 'Consulta') ?></p>
                <p class="c5-message__body"><?= nl2br(e($mensaje['mensaje'] ?? '')) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
