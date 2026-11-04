<?php
/**
 * admin/crear.php · Alta de servicios (Create del CRUD, TP7).
 * Valida en servidor, usa sentencias preparadas y aplica el patrón PRG.
 */

require_once __DIR__ . '/../includes/auth_guard.php';
verificar_rol('admin');

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/componentes.php';

$titulo_pagina = 'Nuevo servicio';
$descripcion_pagina = 'Alta de un servicio del taller.';
$pagina_activa = 'admin';
$admin_activo = 'servicios';

$categorias = $pdo->query("SELECT id, nombre FROM categorias_servicio ORDER BY nombre")->fetchAll();
$errores = [];

$datos = [
    'nombre'          => '',
    'categoria_id'    => (int) ($categorias[0]['id'] ?? 0),
    'descripcion'     => '',
    'precio_base'     => '',
    'tiempo_estimado' => '',
    'garantia_dias'   => '90',
    'destacado'       => '0',
    'activo'          => '1',
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

    // Nombre
    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'El nombre del servicio es obligatorio.';
    } elseif (strlen($datos['nombre']) < 3 || strlen($datos['nombre']) > 120) {
        $errores['nombre'] = 'El nombre debe tener entre 3 y 120 caracteres.';
    }

    // Categoría: debe existir en la tabla maestra.
    $idsCategorias = array_map('intval', array_column($categorias, 'id'));
    if (!in_array($datos['categoria_id'], $idsCategorias, true)) {
        $errores['categoria_id'] = 'Seleccioná una categoría válida.';
    }

    // Descripción
    if ($datos['descripcion'] === '') {
        $errores['descripcion'] = 'La descripción es obligatoria.';
    } elseif (strlen($datos['descripcion']) < 15) {
        $errores['descripcion'] = 'Contá un poco más: al menos 15 caracteres.';
    }

    // Precio
    $precioNormalizado = str_replace(',', '.', $datos['precio_base']);
    if ($datos['precio_base'] === '' || !is_numeric($precioNormalizado)) {
        $errores['precio_base'] = 'Ingresá un precio numérico.';
    } elseif ((float) $precioNormalizado < 0 || (float) $precioNormalizado > 9999999.99) {
        $errores['precio_base'] = 'El precio debe estar entre 0 y 9.999.999,99.';
    }

    // Tiempo estimado
    if ($datos['tiempo_estimado'] === '') {
        $datos['tiempo_estimado'] = 'A convenir';
    } elseif (strlen($datos['tiempo_estimado']) > 50) {
        $errores['tiempo_estimado'] = 'El tiempo estimado no puede superar los 50 caracteres.';
    }

    // Garantía
    if (!ctype_digit($datos['garantia_dias'])) {
        $errores['garantia_dias'] = 'La garantía debe ser un número entero de días.';
    } elseif ((int) $datos['garantia_dias'] > 3650) {
        $errores['garantia_dias'] = 'La garantía no puede superar los 3650 días.';
    }

    if (empty($errores)) {
        try {
            $slug = slug_unico($pdo, 'servicios', $datos['nombre']);

            $sql = "INSERT INTO servicios
                        (categoria_id, nombre, slug, descripcion, precio_base,
                         tiempo_estimado, garantia_dias, destacado, activo)
                    VALUES
                        (:categoria_id, :nombre, :slug, :descripcion, :precio_base,
                         :tiempo_estimado, :garantia_dias, :destacado, :activo)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':categoria_id'    => $datos['categoria_id'],
                ':nombre'          => $datos['nombre'],
                ':slug'            => $slug,
                ':descripcion'     => $datos['descripcion'],
                ':precio_base'     => number_format((float) $precioNormalizado, 2, '.', ''),
                ':tiempo_estimado' => $datos['tiempo_estimado'],
                ':garantia_dias'   => (int) $datos['garantia_dias'],
                ':destacado'       => (int) $datos['destacado'],
                ':activo'          => (int) $datos['activo'],
            ]);

            flash('success', 'Servicio «' . $datos['nombre'] . '» creado correctamente.');
            redirigir('admin/index.php');
        } catch (PDOException $e) {
            error_log('[TechFix] Error al crear servicio: ' . $e->getMessage());
            $errores['general'] = 'No pudimos guardar el servicio. Verificá los datos e intentá de nuevo.';
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
            <span>Nuevo</span>
        </nav>
        <span class="eyebrow"><?= icono('mas') ?> Create · PDO</span>
        <h1>Nuevo servicio</h1>
        <p>Completá los datos del servicio. El slug se genera automáticamente a partir del nombre.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="admin-layout">
            <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>

            <div>
                <div class="panel">
                    <div class="panel__head">
                        <div><h2>Datos del servicio</h2><p>Los campos marcados con * son obligatorios.</p></div>
                        <a class="btn btn--ghost btn--sm" href="<?= e(url('admin/index.php')) ?>"><?= icono('flecha-izq') ?> Volver</a>
                    </div>

                    <?php if (!empty($errores['general'])): ?>
                        <div class="flash flash--error" role="alert" style="margin-bottom:18px">
                            <span class="flash__icon"><?= icono('info') ?></span>
                            <span><?= e($errores['general']) ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= e(url('admin/crear.php')) ?>" class="form" novalidate>
                        <?= csrf_field() ?>

                        <div class="form-grid">
                            <div class="form-group form-group--full">
                                <label class="form-label" for="nombre">Nombre del servicio *</label>
                                <input class="form-input<?= clase_error($errores, 'nombre') ?>" type="text" id="nombre" name="nombre"
                                       value="<?= e($datos['nombre']) ?>" maxlength="120" required
                                       placeholder="Ej.: Cambio de pantalla de celular">
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
                                       id="precio_base" name="precio_base" value="<?= e($datos['precio_base']) ?>" required
                                       placeholder="Ej.: 45000">
                                <?= error_campo($errores, 'precio_base') ?>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="tiempo_estimado">Tiempo estimado</label>
                                <input class="form-input<?= clase_error($errores, 'tiempo_estimado') ?>" type="text"
                                       id="tiempo_estimado" name="tiempo_estimado" value="<?= e($datos['tiempo_estimado']) ?>"
                                       maxlength="50" placeholder="Ej.: 2 a 4 días hábiles">
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
                                          rows="6" required placeholder="Qué incluye el servicio, qué se revisa y qué garantía se entrega…"><?= e($datos['descripcion']) ?></textarea>
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
                            <button class="btn btn--primary" type="submit"><?= icono('check') ?> Guardar servicio</button>
                            <a class="btn btn--ghost" href="<?= e(url('admin/index.php')) ?>">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
