<?php
/**
 * admin/editar.php · Edición de servicios (Update del CRUD, TP7).
 * Recupera el registro por id, valida y actualiza con sentencia preparada.
 */

require_once __DIR__ . '/../includes/auth_guard.php';
verificar_rol('admin');

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/componentes.php';

$titulo_pagina = 'Editar servicio';
$descripcion_pagina = 'Modificación de un servicio del taller.';
$pagina_activa = 'admin';
$admin_activo = 'servicios';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}
$id = $id ?: 0;

$stmt = $pdo->prepare("SELECT * FROM servicios WHERE id = :id LIMIT 1");
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
                <p>El servicio que intentás editar no existe o fue eliminado.</p>
                <a class="btn btn--primary" href="<?= e(url('admin/index.php')) ?>">Volver al listado</a>
            </div>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$categorias = $pdo->query("SELECT id, nombre FROM categorias_servicio ORDER BY nombre")->fetchAll();
$errores = [];

$datos = [
    'nombre'          => (string) $servicio['nombre'],
    'categoria_id'    => (int) $servicio['categoria_id'],
    'descripcion'     => (string) $servicio['descripcion'],
    'precio_base'     => (string) $servicio['precio_base'],
    'tiempo_estimado' => (string) $servicio['tiempo_estimado'],
    'garantia_dias'   => (string) $servicio['garantia_dias'],
    'destacado'       => (string) $servicio['destacado'],
    'activo'          => (string) $servicio['activo'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificar_csrf()) {
        $errores['general'] = 'El formulario expiró. Volvé a intentarlo.';
    }

    $datos['nombre']          = limpiar($_POST['nombre'] ?? '');
    $datos['categoria_id']    = (int) filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
    $datos['descripcion']     = limpiar_multilinea($_POST['descripcion'] ?? '');
    $datos['precio_base']     = trim((string) ($_POST['precio_base'] ?? ''));
    $datos['tiempo_estimado'] = limpiar($_POST['tiempo_estimado'] ?? '');
    $datos['garantia_dias']   = trim((string) ($_POST['garantia_dias'] ?? '90'));
    $datos['destacado']       = isset($_POST['destacado']) ? '1' : '0';
    $datos['activo']          = isset($_POST['activo']) ? '1' : '0';

    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'El nombre del servicio es obligatorio.';
    } elseif (strlen($datos['nombre']) < 3 || strlen($datos['nombre']) > 120) {
        $errores['nombre'] = 'El nombre debe tener entre 3 y 120 caracteres.';
    }

    $idsCategorias = array_map('intval', array_column($categorias, 'id'));
    if (!in_array($datos['categoria_id'], $idsCategorias, true)) {
        $errores['categoria_id'] = 'Seleccioná una categoría válida.';
    }

    if ($datos['descripcion'] === '') {
        $errores['descripcion'] = 'La descripción es obligatoria.';
    } elseif (strlen($datos['descripcion']) < 15) {
        $errores['descripcion'] = 'Contá un poco más: al menos 15 caracteres.';
    }

    $precioNormalizado = str_replace(',', '.', $datos['precio_base']);
    if ($datos['precio_base'] === '' || !is_numeric($precioNormalizado)) {
        $errores['precio_base'] = 'Ingresá un precio numérico.';
    } elseif ((float) $precioNormalizado < 0 || (float) $precioNormalizado > 9999999.99) {
        $errores['precio_base'] = 'El precio debe estar entre 0 y 9.999.999,99.';
    }

    if ($datos['tiempo_estimado'] === '') {
        $datos['tiempo_estimado'] = 'A convenir';
    } elseif (strlen($datos['tiempo_estimado']) > 50) {
        $errores['tiempo_estimado'] = 'El tiempo estimado no puede superar los 50 caracteres.';
    }

    if (!ctype_digit($datos['garantia_dias'])) {
        $errores['garantia_dias'] = 'La garantía debe ser un número entero de días.';
    } elseif ((int) $datos['garantia_dias'] > 3650) {
        $errores['garantia_dias'] = 'La garantía no puede superar los 3650 días.';
    }

    if (empty($errores)) {
        try {
            // El slug solo se regenera si cambió el nombre.
            $slug = (string) $servicio['slug'];
            if ($datos['nombre'] !== $servicio['nombre']) {
                $slug = slug_unico($pdo, 'servicios', $datos['nombre'], $id);
            }

            $sql = "UPDATE servicios SET
                        categoria_id    = :categoria_id,
                        nombre          = :nombre,
                        slug            = :slug,
                        descripcion     = :descripcion,
                        precio_base     = :precio_base,
                        tiempo_estimado = :tiempo_estimado,
                        garantia_dias   = :garantia_dias,
                        destacado       = :destacado,
                        activo          = :activo
                    WHERE id = :id";

            $update = $pdo->prepare($sql);
            $update->execute([
                ':categoria_id'    => $datos['categoria_id'],
                ':nombre'          => $datos['nombre'],
                ':slug'            => $slug,
                ':descripcion'     => $datos['descripcion'],
                ':precio_base'     => number_format((float) $precioNormalizado, 2, '.', ''),
                ':tiempo_estimado' => $datos['tiempo_estimado'],
                ':garantia_dias'   => (int) $datos['garantia_dias'],
                ':destacado'       => (int) $datos['destacado'],
                ':activo'          => (int) $datos['activo'],
                ':id'              => $id,
            ]);

            flash('success', 'Servicio «' . $datos['nombre'] . '» actualizado correctamente.');
            redirigir('admin/index.php');
        } catch (PDOException $e) {
            error_log('[TechFix] Error al editar servicio #' . $id . ': ' . $e->getMessage());
            $errores['general'] = 'No pudimos actualizar el servicio. Intentá nuevamente.';
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
            <span>Editar #<?= (int) $id ?></span>
        </nav>
        <span class="eyebrow"><?= icono('editar') ?> Update · PDO</span>
        <h1>Editar servicio</h1>
        <p>Modificá los datos del servicio «<?= e($servicio['nombre']) ?>».</p>
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
                            <h2>Datos del servicio #<?= (int) $id ?></h2>
                            <p>Slug actual: <code><?= e($servicio['slug']) ?></code></p>
                        </div>
                        <a class="btn btn--ghost btn--sm" href="<?= e(url('admin/index.php')) ?>"><?= icono('flecha-izq') ?> Volver</a>
                    </div>

                    <?php if (!empty($errores['general'])): ?>
                        <div class="flash flash--error" role="alert" style="margin-bottom:18px">
                            <span class="flash__icon"><?= icono('info') ?></span>
                            <span><?= e($errores['general']) ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= e(url('admin/editar.php?id=' . (int) $id)) ?>" class="form" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $id ?>">

                        <div class="form-grid">
                            <div class="form-group form-group--full">
                                <label class="form-label" for="nombre">Nombre del servicio *</label>
                                <input class="form-input<?= clase_error($errores, 'nombre') ?>" type="text" id="nombre" name="nombre"
                                       value="<?= e($datos['nombre']) ?>" maxlength="120" required>
                                <?= error_campo($errores, 'nombre') ?>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="categoria_id">Categoría *</label>
                                <select class="form-select<?= clase_error($errores, 'categoria_id') ?>" id="categoria_id" name="categoria_id" required>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= (int) $categoria['id'] ?>" <?= $datos['categoria_id'] === (int) $categoria['id'] ? 'selected' : '' ?>>
                                            <?= e($categoria['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= error_campo($errores, 'categoria_id') ?>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="precio_base">Precio base (ARS) *</label>
                                <input class="form-input<?= clase_error($errores, 'precio_base') ?>" type="text" inputmode="decimal"
                                       id="precio_base" name="precio_base" value="<?= e($datos['precio_base']) ?>" required>
                                <?= error_campo($errores, 'precio_base') ?>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="tiempo_estimado">Tiempo estimado</label>
                                <input class="form-input<?= clase_error($errores, 'tiempo_estimado') ?>" type="text"
                                       id="tiempo_estimado" name="tiempo_estimado" value="<?= e($datos['tiempo_estimado']) ?>" maxlength="50">
                                <?= error_campo($errores, 'tiempo_estimado') ?>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="garantia_dias">Garantía (días)</label>
                                <input class="form-input<?= clase_error($errores, 'garantia_dias') ?>" type="number" min="0" max="3650"
                                       id="garantia_dias" name="garantia_dias" value="<?= e($datos['garantia_dias']) ?>">
                                <?= error_campo($errores, 'garantia_dias') ?>
                            </div>

                            <div class="form-group form-group--full">
                                <label class="form-label" for="descripcion">Descripción *</label>
                                <textarea class="form-textarea<?= clase_error($errores, 'descripcion') ?>" id="descripcion" name="descripcion"
                                          rows="6" required><?= e($datos['descripcion']) ?></textarea>
                                <?= error_campo($errores, 'descripcion') ?>
                            </div>

                            <div class="form-group">
                                <label class="form-check">
                                    <input type="checkbox" name="destacado" value="1" <?= $datos['destacado'] === '1' ? 'checked' : '' ?>>
                                    <span>Mostrar como destacado en la home</span>
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="form-check">
                                    <input type="checkbox" name="activo" value="1" <?= $datos['activo'] === '1' ? 'checked' : '' ?>>
                                    <span>Servicio activo (visible en el sitio)</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="btn btn--primary" type="submit"><?= icono('check') ?> Guardar cambios</button>
                            <a class="btn btn--ghost" href="<?= e(url('admin/index.php')) ?>">Cancelar</a>
                            <a class="btn btn--outline" href="<?= e(url('servicio.php?id=' . (int) $id)) ?>" target="_blank" rel="noopener">Ver en el sitio</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
