<?php
/**
 * admin/index.php · Listado y gestión de servicios (Read del CRUD).
 */

require_once __DIR__ . '/../includes/auth_guard.php';
verificar_rol('admin');

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/componentes.php';

$titulo_pagina = 'Gestión de servicios';
$descripcion_pagina = 'Alta, edición y baja de los servicios del taller.';
$pagina_activa = 'admin';
$admin_activo = 'servicios';

$busqueda = limpiar($_GET['q'] ?? '');
$categoriaId = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT) ?: 0;

$categorias = $pdo->query("SELECT id, nombre FROM categorias_servicio ORDER BY nombre")->fetchAll();

$sql = "SELECT s.*, c.nombre AS categoria
        FROM servicios s
        INNER JOIN categorias_servicio c ON c.id = s.categoria_id
        WHERE 1 = 1";
$parametros = [];
if ($busqueda !== '') {
    $sql .= " AND (s.nombre LIKE :q OR s.descripcion LIKE :q)";
    $parametros[':q'] = '%' . $busqueda . '%';
}
if ($categoriaId > 0) {
    $sql .= " AND s.categoria_id = :categoria";
    $parametros[':categoria'] = $categoriaId;
}
$sql .= " ORDER BY s.activo DESC, c.nombre, s.nombre";

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$servicios = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= e(url('admin_panel.php')) ?>">Panel</a> <?= icono('flecha') ?>
            <span>Servicios</span>
        </nav>
        <span class="eyebrow"><?= icono('herramienta') ?> CRUD con PDO</span>
        <h1>Gestión de servicios</h1>
        <p>Altas, ediciones y bajas del catálogo. Todas las consultas usan sentencias preparadas.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="admin-layout">
            <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>

            <div>
                <div class="panel">
                    <div class="panel__head">
                        <div>
                            <h2>Servicios (<?= count($servicios) ?>)</h2>
                            <p>Los cambios se reflejan de inmediato en el sitio público.</p>
                        </div>
                        <a class="btn btn--primary" href="<?= e(url('admin/crear.php')) ?>"><?= icono('mas') ?> Nuevo servicio</a>
                    </div>

                    <form method="get" action="<?= e(url('admin/index.php')) ?>" class="shop-toolbar" style="margin-bottom:20px;box-shadow:none">
                        <div class="shop-search">
                            <?= icono('buscar') ?>
                            <label class="sr-only" for="q">Buscar servicio</label>
                            <input type="search" id="q" name="q" value="<?= e($busqueda) ?>" placeholder="Buscar por nombre o descripción…">
                        </div>
                        <select class="form-select" name="categoria" style="max-width:220px">
                            <option value="0">Todas las categorías</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= (int) $categoria['id'] ?>" <?= $categoriaId === (int) $categoria['id'] ? 'selected' : '' ?>><?= e($categoria['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn--ghost" type="submit">Filtrar</button>
                        <?php if ($busqueda !== '' || $categoriaId > 0): ?>
                            <a class="btn btn--ghost" href="<?= e(url('admin/index.php')) ?>">Limpiar</a>
                        <?php endif; ?>
                    </form>

                    <?php if ($servicios): ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr><th>ID</th><th>Servicio</th><th>Categoría</th><th>Precio</th><th>Tiempo</th><th>Estado</th><th>Acciones</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($servicios as $servicio): ?>
                                        <tr>
                                            <td><?= (int) $servicio['id'] ?></td>
                                            <td>
                                                <strong><?= e($servicio['nombre']) ?></strong>
                                                <?php if ((int) $servicio['destacado'] === 1): ?>
                                                    <span class="badge badge--cyan" style="margin-left:6px">Destacado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= e($servicio['categoria']) ?></td>
                                            <td><?= e(precio($servicio['precio_base'])) ?></td>
                                            <td><?= e($servicio['tiempo_estimado']) ?></td>
                                            <td><span class="badge <?= (int) $servicio['activo'] === 1 ? 'badge--green' : 'badge--gray' ?>"><?= (int) $servicio['activo'] === 1 ? 'Activo' : 'Inactivo' ?></span></td>
                                            <td>
                                                <div class="table__actions">
                                                    <a class="btn btn--ghost btn--sm" href="<?= e(url('servicio.php?id=' . (int) $servicio['id'])) ?>" target="_blank" rel="noopener">Ver</a>
                                                    <a class="btn btn--outline btn--sm" href="<?= e(url('admin/editar.php?id=' . (int) $servicio['id'])) ?>"><?= icono('editar') ?> Editar</a>
                                                    <a class="btn btn--danger btn--sm" href="<?= e(url('admin/eliminar.php?id=' . (int) $servicio['id'])) ?>"><?= icono('eliminar') ?> Eliminar</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state__icon"><?= icono('buscar') ?></div>
                            <h3>No hay servicios para mostrar</h3>
                            <p>Probá con otro filtro o creá el primer servicio.</p>
                            <a class="btn btn--primary" href="<?= e(url('admin/crear.php')) ?>">Crear servicio</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
