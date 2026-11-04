<?php
/**
 * admin/usuarios.php · Administración de cuentas y roles (TP8).
 * Solo el rol admin puede listar usuarios, cambiar roles o desactivar cuentas.
 */

require_once __DIR__ . '/../includes/auth_guard.php';
verificar_rol('admin');

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/componentes.php';

$titulo_pagina = 'Gestión de usuarios';
$descripcion_pagina = 'Cuentas registradas, roles y estado de acceso.';
$pagina_activa = 'admin';
$admin_activo = 'usuarios';

$roles = $pdo->query("SELECT id, nombre, descripcion FROM roles ORDER BY id")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificar_csrf()) {
        flash('error', 'El formulario expiró. Volvé a intentarlo.');
        redirigir('admin/usuarios.php');
    }

    $usuarioId = (int) filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
    $accion = limpiar($_POST['accion'] ?? '');
    $rolId = (int) filter_input(INPUT_POST, 'rol_id', FILTER_VALIDATE_INT);

    if ($usuarioId <= 0) {
        flash('error', 'Usuario inválido.');
        redirigir('admin/usuarios.php');
    }

    // Evitar que el admin se saque a sí mismo el permiso o se desactive.
    if ($usuarioId === (int) $_SESSION['usuario_id']) {
        flash('error', 'No podés modificar tu propia cuenta desde este panel.');
        redirigir('admin/usuarios.php');
    }

    try {
        if ($accion === 'rol') {
            $idsRoles = array_map('intval', array_column($roles, 'id'));
            if (!in_array($rolId, $idsRoles, true)) {
                flash('error', 'Rol inválido.');
                redirigir('admin/usuarios.php');
            }
            $stmt = $pdo->prepare("UPDATE usuarios SET rol_id = :rol WHERE id = :id");
            $stmt->execute([':rol' => $rolId, ':id' => $usuarioId]);
            flash('success', 'Rol actualizado correctamente.');
        } elseif ($accion === 'estado') {
            $nuevoEstado = (int) filter_input(INPUT_POST, 'activo', FILTER_VALIDATE_INT);
            $nuevoEstado = $nuevoEstado === 1 ? 1 : 0;
            $stmt = $pdo->prepare("UPDATE usuarios SET activo = :activo WHERE id = :id");
            $stmt->execute([':activo' => $nuevoEstado, ':id' => $usuarioId]);
            flash('success', $nuevoEstado === 1 ? 'Cuenta activada.' : 'Cuenta desactivada.');
        } else {
            flash('error', 'Acción no reconocida.');
        }
    } catch (PDOException $e) {
        error_log('[TechFix] Error al actualizar usuario #' . $usuarioId . ': ' . $e->getMessage());
        flash('error', 'No pudimos actualizar la cuenta.');
    }

    redirigir('admin/usuarios.php');
}

$busqueda = limpiar($_GET['q'] ?? '');
$rolFiltro = (int) (filter_input(INPUT_GET, 'rol', FILTER_VALIDATE_INT) ?: 0);

$sql = "SELECT u.id, u.nombre, u.email, u.telefono, u.activo, u.created_at,
               r.nombre AS rol, r.id AS rol_id
        FROM usuarios u
        INNER JOIN roles r ON r.id = u.rol_id
        WHERE 1 = 1";
$parametros = [];
if ($busqueda !== '') {
    $sql .= " AND (u.nombre LIKE :q OR u.email LIKE :q)";
    $parametros[':q'] = '%' . $busqueda . '%';
}
if ($rolFiltro > 0) {
    $sql .= " AND u.rol_id = :rol";
    $parametros[':rol'] = $rolFiltro;
}
$sql .= " ORDER BY r.id, u.nombre";

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$usuarios = $stmt->fetchAll();

$totalAdmins = (int) $pdo->query(
    "SELECT COUNT(*) FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id WHERE r.nombre = 'admin' AND u.activo = 1"
)->fetchColumn();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= e(url('admin_panel.php')) ?>">Panel</a> <?= icono('flecha') ?>
            <span>Usuarios</span>
        </nav>
        <span class="eyebrow"><?= icono('usuario') ?> Roles y accesos</span>
        <h1>Gestión de usuarios</h1>
        <p>Cuentas registradas en el sitio. Podés cambiar el rol y habilitar o bloquear el acceso.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="admin-layout">
            <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>

            <div>
                <div class="stat-cards" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
                    <article class="stat-card">
                        <span class="stat-card__label"><?= icono('usuario') ?> Cuentas listadas</span>
                        <strong class="stat-card__value"><?= count($usuarios) ?></strong>
                        <span class="stat-card__hint">según el filtro actual</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-card__label"><?= icono('escudo') ?> Administradores activos</span>
                        <strong class="stat-card__value"><?= $totalAdmins ?></strong>
                        <span class="stat-card__hint">con acceso al panel</span>
                    </article>
                </div>

                <div class="panel">
                    <div class="panel__head">
                        <div><h2>Usuarios registrados</h2><p>Los cambios de rol se aplican en el próximo inicio de sesión.</p></div>
                    </div>

                    <form method="get" action="<?= e(url('admin/usuarios.php')) ?>" class="shop-toolbar" style="box-shadow:none;margin-bottom:20px">
                        <div class="shop-search">
                            <?= icono('buscar') ?>
                            <label class="sr-only" for="q">Buscar usuario</label>
                            <input type="search" id="q" name="q" value="<?= e($busqueda) ?>" placeholder="Nombre o correo…">
                        </div>
                        <select class="form-select" name="rol" style="max-width:220px">
                            <option value="0">Todos los roles</option>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?= (int) $rol['id'] ?>" <?= $rolFiltro === (int) $rol['id'] ? 'selected' : '' ?>><?= e(ucfirst($rol['nombre'])) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn--ghost" type="submit">Filtrar</button>
                        <?php if ($busqueda !== '' || $rolFiltro > 0): ?>
                            <a class="btn btn--ghost" href="<?= e(url('admin/usuarios.php')) ?>">Limpiar</a>
                        <?php endif; ?>
                    </form>

                    <?php if ($usuarios): ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr><th>ID</th><th>Usuario</th><th>Teléfono</th><th>Rol</th><th>Estado</th><th>Alta</th><th>Acciones</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <?php $esUnoMismo = (int) $usuario['id'] === (int) $_SESSION['usuario_id']; ?>
                                        <tr>
                                            <td><?= (int) $usuario['id'] ?></td>
                                            <td>
                                                <strong><?= e($usuario['nombre']) ?></strong><?= $esUnoMismo ? ' <span class="badge badge--cyan">Vos</span>' : '' ?>
                                                <small class="muted" style="display:block"><?= e($usuario['email']) ?></small>
                                            </td>
                                            <td><?= e($usuario['telefono'] ?: '—') ?></td>
                                            <td>
                                                <?php if ($esUnoMismo): ?>
                                                    <span class="badge badge--cyan"><?= e(ucfirst($usuario['rol'])) ?></span>
                                                <?php else: ?>
                                                    <form method="post" action="<?= e(url('admin/usuarios.php')) ?>" class="inline-form">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="usuario_id" value="<?= (int) $usuario['id'] ?>">
                                                        <input type="hidden" name="accion" value="rol">
                                                        <label class="sr-only" for="rol-<?= (int) $usuario['id'] ?>">Rol de <?= e($usuario['nombre']) ?></label>
                                                        <select class="form-select form-select--sm" id="rol-<?= (int) $usuario['id'] ?>" name="rol_id">
                                                            <?php foreach ($roles as $rol): ?>
                                                                <option value="<?= (int) $rol['id'] ?>" <?= (int) $usuario['rol_id'] === (int) $rol['id'] ? 'selected' : '' ?>><?= e(ucfirst($rol['nombre'])) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button class="btn btn--outline btn--sm" type="submit">Cambiar</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge <?= (int) $usuario['activo'] === 1 ? 'badge--green' : 'badge--red' ?>"><?= (int) $usuario['activo'] === 1 ? 'Activo' : 'Bloqueado' ?></span></td>
                                            <td><?= e(fecha($usuario['created_at'], false)) ?></td>
                                            <td>
                                                <?php if ($esUnoMismo): ?>
                                                    <span class="muted" style="font-size:.78rem">Tu propia cuenta</span>
                                                <?php else: ?>
                                                    <form method="post" action="<?= e(url('admin/usuarios.php')) ?>" class="inline-form">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="usuario_id" value="<?= (int) $usuario['id'] ?>">
                                                        <input type="hidden" name="accion" value="estado">
                                                        <input type="hidden" name="activo" value="<?= (int) $usuario['activo'] === 1 ? '0' : '1' ?>">
                                                        <button class="btn <?= (int) $usuario['activo'] === 1 ? 'btn--danger' : 'btn--primary' ?> btn--sm" type="submit">
                                                            <?= (int) $usuario['activo'] === 1 ? 'Bloquear' : 'Activar' ?>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state__icon"><?= icono('usuario') ?></div>
                            <h3>Sin usuarios para el filtro</h3>
                            <p>Probá con otra búsqueda o quitá los filtros.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tracking-note">
                    <?= icono('escudo') ?>
                    <span>Las contraseñas se almacenan con <code>password_hash()</code> y se verifican con
                        <code>password_verify()</code>. El rol admin es el único que puede acceder a este panel.</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
