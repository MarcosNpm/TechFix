<?php
/**
 * admin/reparaciones.php · Gestión del flujo de reparaciones (TP7 + TP8).
 * Permite buscar por código/cliente, cambiar estado, observaciones y costo final.
 */

require_once __DIR__ . '/../includes/auth_guard.php';
verificar_rol('admin');

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/componentes.php';

$titulo_pagina = 'Gestión de reparaciones';
$descripcion_pagina = 'Seguimiento y actualización de las reparaciones del taller.';
$pagina_activa = 'admin';
$admin_activo = 'reparaciones';

$estados = $pdo->query("SELECT id, nombre, slug, orden FROM estados_reparacion ORDER BY orden")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificar_csrf()) {
        flash('error', 'El formulario expiró. Volvé a intentarlo.');
        redirigir('admin/reparaciones.php');
    }

    $reparacionId = (int) filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $estadoId     = (int) filter_input(INPUT_POST, 'estado_id', FILTER_VALIDATE_INT);
    $observaciones = limpiar_multilinea($_POST['observaciones'] ?? '');
    $costoFinal   = trim((string) ($_POST['costo_final'] ?? '0'));

    $idsEstados = array_map('intval', array_column($estados, 'id'));
    $costoNormalizado = str_replace(',', '.', $costoFinal);

    if ($reparacionId <= 0 || !in_array($estadoId, $idsEstados, true)) {
        flash('error', 'Los datos enviados no son válidos.');
        redirigir('admin/reparaciones.php');
    }

    if (!is_numeric($costoNormalizado) || (float) $costoNormalizado < 0) {
        flash('error', 'El costo final debe ser un número mayor o igual a cero.');
        redirigir('admin/reparaciones.php?editar=' . $reparacionId);
    }

    try {
        $stmt = $pdo->prepare(
            "UPDATE reparaciones
             SET estado_id = :estado_id, observaciones = :observaciones, costo_final = :costo_final
             WHERE id = :id"
        );
        $stmt->execute([
            ':estado_id'     => $estadoId,
            ':observaciones' => $observaciones !== '' ? $observaciones : null,
            ':costo_final'   => number_format((float) $costoNormalizado, 2, '.', ''),
            ':id'            => $reparacionId,
        ]);

        flash('success', 'Reparación actualizada correctamente.');
    } catch (PDOException $e) {
        error_log('[TechFix] Error al actualizar reparación #' . $reparacionId . ': ' . $e->getMessage());
        flash('error', 'No pudimos actualizar la reparación.');
    }

    redirigir('admin/reparaciones.php');
}

$busqueda = limpiar($_GET['q'] ?? '');
$estadoFiltro = (int) (filter_input(INPUT_GET, 'estado', FILTER_VALIDATE_INT) ?: 0);

$sql = "SELECT r.*, s.nombre AS servicio, e.nombre AS estado, e.slug AS estado_slug
        FROM reparaciones r
        INNER JOIN servicios s ON s.id = r.servicio_id
        INNER JOIN estados_reparacion e ON e.id = r.estado_id
        WHERE 1 = 1";
$parametros = [];

if ($busqueda !== '') {
    $sql .= " AND (r.codigo LIKE :q OR r.cliente_nombre LIKE :q OR r.cliente_email LIKE :q
                   OR r.equipo_marca LIKE :q OR r.equipo_modelo LIKE :q)";
    $parametros[':q'] = '%' . $busqueda . '%';
}
if ($estadoFiltro > 0) {
    $sql .= " AND r.estado_id = :estado";
    $parametros[':estado'] = $estadoFiltro;
}
$sql .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$reparaciones = $stmt->fetchAll();

$editarId = (int) (filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT) ?: 0);
$reparacionEdicion = null;
foreach ($reparaciones as $reparacion) {
    if ($editarId > 0 && (int) $reparacion['id'] === $editarId) {
        $reparacionEdicion = $reparacion;
        break;
    }
}
if ($reparacionEdicion === null && $editarId > 0) {
    $stmt = $pdo->prepare(
        "SELECT r.*, s.nombre AS servicio, e.nombre AS estado, e.slug AS estado_slug
         FROM reparaciones r
         INNER JOIN servicios s ON s.id = r.servicio_id
         INNER JOIN estados_reparacion e ON e.id = r.estado_id
         WHERE r.id = :id LIMIT 1"
    );
    $stmt->execute([':id' => $editarId]);
    $reparacionEdicion = $stmt->fetch() ?: null;
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= e(url('admin_panel.php')) ?>">Panel</a> <?= icono('flecha') ?>
            <span>Reparaciones</span>
        </nav>
        <span class="eyebrow"><?= icono('lista') ?> Seguimiento del taller</span>
        <h1>Gestión de reparaciones</h1>
        <p>Actualizá el estado, las observaciones técnicas y el costo final. El cliente ve los cambios en el seguimiento público.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="admin-layout">
            <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>

            <div>
                <?php if ($reparacionEdicion): ?>
                    <div class="panel" style="margin-bottom:24px">
                        <div class="panel__head">
                            <div>
                                <h2>Editar reparación <?= e($reparacionEdicion['codigo']) ?></h2>
                                <p><?= e($reparacionEdicion['cliente_nombre']) ?> · <?= e($reparacionEdicion['equipo_marca'] . ' ' . $reparacionEdicion['equipo_modelo']) ?></p>
                            </div>
                            <a class="btn btn--ghost btn--sm" href="<?= e(url('admin/reparaciones.php')) ?>">Cerrar</a>
                        </div>

                        <form method="post" action="<?= e(url('admin/reparaciones.php')) ?>" class="form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $reparacionEdicion['id'] ?>">

                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label" for="estado_id">Estado del flujo *</label>
                                    <select class="form-select" id="estado_id" name="estado_id" required>
                                        <?php foreach ($estados as $estado): ?>
                                            <option value="<?= (int) $estado['id'] ?>" <?= (int) $reparacionEdicion['estado_id'] === (int) $estado['id'] ? 'selected' : '' ?>>
                                                <?= e($estado['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="costo_final">Costo final (ARS)</label>
                                    <input class="form-input" type="text" inputmode="decimal" id="costo_final" name="costo_final"
                                           value="<?= e($reparacionEdicion['costo_final']) ?>">
                                </div>

                                <div class="form-group form-group--full">
                                    <label class="form-label" for="observaciones">Observaciones para el cliente</label>
                                    <textarea class="form-textarea" id="observaciones" name="observaciones" rows="4"
                                              placeholder="Diagnóstico, repuestos utilizados, recomendaciones…"><?= e($reparacionEdicion['observaciones'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button class="btn btn--primary" type="submit"><?= icono('check') ?> Guardar cambios</button>
                                <a class="btn btn--ghost" href="<?= e(url('admin/reparaciones.php')) ?>">Cancelar</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="panel">
                    <div class="panel__head">
                        <div>
                            <h2>Reparaciones (<?= count($reparaciones) ?>)</h2>
                            <p>Buscar por código, cliente o equipo.</p>
                        </div>
                    </div>

                    <form method="get" action="<?= e(url('admin/reparaciones.php')) ?>" class="shop-toolbar" style="box-shadow:none;margin-bottom:20px">
                        <div class="shop-search">
                            <?= icono('buscar') ?>
                            <label class="sr-only" for="q">Buscar reparación</label>
                            <input type="search" id="q" name="q" value="<?= e($busqueda) ?>" placeholder="Código, cliente, equipo…">
                        </div>
                        <select class="form-select" name="estado" style="max-width:220px">
                            <option value="0">Todos los estados</option>
                            <?php foreach ($estados as $estado): ?>
                                <option value="<?= (int) $estado['id'] ?>" <?= $estadoFiltro === (int) $estado['id'] ? 'selected' : '' ?>><?= e($estado['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn--ghost" type="submit">Filtrar</button>
                        <?php if ($busqueda !== '' || $estadoFiltro > 0): ?>
                            <a class="btn btn--ghost" href="<?= e(url('admin/reparaciones.php')) ?>">Limpiar</a>
                        <?php endif; ?>
                    </form>

                    <?php if ($reparaciones): ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr><th>Código</th><th>Cliente</th><th>Equipo</th><th>Servicio</th><th>Estado</th><th>Costo</th><th>Ingreso</th><th>Acciones</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reparaciones as $reparacion): ?>
                                        <tr>
                                            <td><strong><?= e($reparacion['codigo']) ?></strong></td>
                                            <td>
                                                <?= e($reparacion['cliente_nombre']) ?>
                                                <small class="muted" style="display:block"><?= e($reparacion['cliente_email']) ?></small>
                                            </td>
                                            <td><?= e($reparacion['equipo_marca'] . ' ' . $reparacion['equipo_modelo']) ?></td>
                                            <td><?= e($reparacion['servicio']) ?></td>
                                            <td><?= badge_estado($reparacion) ?></td>
                                            <td><?= (float) $reparacion['costo_final'] > 0 ? e(precio($reparacion['costo_final'])) : '—' ?></td>
                                            <td><?= e(fecha($reparacion['created_at'], false)) ?></td>
                                            <td>
                                                <div class="table__actions">
                                                    <a class="btn btn--outline btn--sm" href="<?= e(url('admin/reparaciones.php?editar=' . (int) $reparacion['id'])) ?>"><?= icono('editar') ?> Editar</a>
                                                    <a class="btn btn--ghost btn--sm" href="<?= e(url('seguimiento.php?codigo=' . urlencode($reparacion['codigo']))) ?>" target="_blank" rel="noopener">Ver</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state__icon"><?= icono('lista') ?></div>
                            <h3>No hay reparaciones</h3>
                            <p>Probá con otro filtro o esperá el ingreso de una nueva solicitud.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
