<?php
/**
 * admin/eliminar.php · Baja de servicios (Delete del CRUD, TP7).
 * GET muestra la confirmación; POST ejecuta la baja con control de integridad.
 */

require_once __DIR__ . '/../includes/auth_guard.php';
verificar_rol('admin');

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/componentes.php';

$titulo_pagina = 'Eliminar servicio';
$descripcion_pagina = 'Confirmación de baja de un servicio.';
$pagina_activa = 'admin';
$admin_activo = 'servicios';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}
$id = $id ?: 0;

$stmt = $pdo->prepare(
    "SELECT s.*, c.nombre AS categoria,
            (SELECT COUNT(*) FROM reparaciones r WHERE r.servicio_id = s.id) AS reparaciones_asociadas
     FROM servicios s
     INNER JOIN categorias_servicio c ON c.id = s.categoria_id
     WHERE s.id = :id
     LIMIT 1"
);
$stmt->execute([':id' => $id]);
$servicio = $stmt->fetch();

if (!$servicio) {
    http_response_code(404);
    $titulo_pagina = 'Servicio no encontrado';
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <section class="section">
        <div class="contenedor">
            <div class="empty-state">
                <div class="empty-state__icon"><?= icono('buscar') ?></div>
                <h1>Servicio no encontrado</h1>
                <p>El servicio que intentás eliminar no existe o ya fue eliminado.</p>
                <a class="btn btn--primary" href="<?= e(url('admin/index.php')) ?>">Volver al listado</a>
            </div>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$reparacionesAsociadas = (int) $servicio['reparaciones_asociadas'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificar_csrf()) {
        $error = 'El formulario expiró. Volvé a intentarlo.';
    } elseif ($reparacionesAsociadas > 0) {
        $error = 'No se puede eliminar: el servicio tiene ' . $reparacionesAsociadas
            . ' reparación(es) asociada(s). Podés desactivarlo para ocultarlo del sitio.';
    } else {
        try {
            $delete = $pdo->prepare("DELETE FROM servicios WHERE id = :id");
            $delete->execute([':id' => $id]);

            flash('success', 'Servicio «' . $servicio['nombre'] . '» eliminado correctamente.');
            redirigir('admin/index.php');
        } catch (PDOException $e) {
            error_log('[TechFix] Error al eliminar servicio #' . $id . ': ' . $e->getMessage());
            $error = 'No pudimos eliminar el servicio porque tiene datos relacionados. Probá desactivarlo en su lugar.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= e(url('admin_panel.php')) ?>">Panel</a> <?= icono('flecha') ?>
            <a href="<?= e(url('admin/index.php')) ?>">Servicios</a> <?= icono('flecha') ?>
            <span>Eliminar</span>
        </nav>
        <span class="eyebrow"><?= icono('eliminar') ?> Delete · PDO</span>
        <h1>Eliminar servicio</h1>
        <p>Esta acción es permanente. Revisá el registro antes de confirmar.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="admin-layout">
            <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>

            <div>
                <div class="panel">
                    <div class="panel__head">
                        <div><h2>Confirmación de baja</h2><p>Servicio #<?= (int) $id ?></p></div>
                        <a class="btn btn--ghost btn--sm" href="<?= e(url('admin/index.php')) ?>"><?= icono('flecha-izq') ?> Volver</a>
                    </div>

                    <?php if ($error !== ''): ?>
                        <div class="flash flash--error" role="alert" style="margin-bottom:18px">
                            <span class="flash__icon"><?= icono('info') ?></span>
                            <span><?= e($error) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="delete-card">
                        <div class="delete-card__icon"><?= icono('eliminar') ?></div>
                        <h3><?= e($servicio['nombre']) ?></h3>
                        <p><?= e(resumen((string) $servicio['descripcion'], 180)) ?></p>

                        <dl class="delete-meta">
                            <div><dt>Categoría</dt><dd><?= e($servicio['categoria']) ?></dd></div>
                            <div><dt>Precio base</dt><dd><?= e(precio($servicio['precio_base'])) ?></dd></div>
                            <div><dt>Estado</dt><dd><?= (int) $servicio['activo'] === 1 ? 'Activo' : 'Inactivo' ?></dd></div>
                            <div><dt>Reparaciones asociadas</dt><dd><?= $reparacionesAsociadas ?></dd></div>
                        </dl>

                        <?php if ($reparacionesAsociadas > 0): ?>
                            <div class="tracking-note" style="text-align:left;margin-top:18px">
                                <?= icono('info') ?>
                                <span>Por integridad referencial no se puede borrar un servicio con reparaciones.
                                    Podés editarlo y quitar la marca «activo» para ocultarlo del sitio.</span>
                            </div>
                            <div class="form-actions" style="justify-content:center">
                                <a class="btn btn--primary" href="<?= e(url('admin/editar.php?id=' . (int) $id)) ?>"><?= icono('editar') ?> Editar y desactivar</a>
                                <a class="btn btn--ghost" href="<?= e(url('admin/index.php')) ?>">Cancelar</a>
                            </div>
                        <?php else: ?>
                            <form method="post" action="<?= e(url('admin/eliminar.php?id=' . (int) $id)) ?>" class="form-actions" style="justify-content:center">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $id ?>">
                                <button class="btn btn--danger" type="submit"><?= icono('eliminar') ?> Sí, eliminar definitivamente</button>
                                <a class="btn btn--ghost" href="<?= e(url('admin/index.php')) ?>">Cancelar</a>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
