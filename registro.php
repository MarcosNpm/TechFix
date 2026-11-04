<?php
/**
 * registro.php · Alta de usuarios con password_hash (TP8).
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/componentes.php';

if (usuario_actual()) {
    redirigir('dashboard.php');
}

$titulo_pagina = 'Crear cuenta';
$descripcion_pagina = 'Registrate en TechFix para seguir tus reparaciones y pedidos.';
$pagina_activa = 'registro';

$datos = ['nombre' => '', 'email' => '', 'telefono' => ''];
$errores = [];
$mensajeGeneral = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificar_csrf()) {
        $mensajeGeneral = 'La sesión expiró o el formulario no es válido. Recargá la página e intentá nuevamente.';
    } else {
        $datos['nombre'] = limpiar($_POST['nombre'] ?? '');
        $datos['email'] = limpiar($_POST['email'] ?? '');
        $datos['telefono'] = limpiar($_POST['telefono'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirmar = (string) ($_POST['password_confirm'] ?? '');

        if (strlen($datos['nombre']) < 3 || strlen($datos['nombre']) > 100) {
            $errores['nombre'] = 'Ingresá tu nombre completo (entre 3 y 100 caracteres).';
        }
        if (!email_valido($datos['email'])) {
            $errores['email'] = 'Ingresá un correo electrónico válido.';
        }
        if ($datos['telefono'] !== '') {
            $digitos = preg_replace('/\D+/', '', $datos['telefono']);
            if (strlen($digitos) < 7 || strlen($digitos) > 15) {
                $errores['telefono'] = 'El teléfono debe tener entre 7 y 15 dígitos.';
            }
        }
        if (strlen($password) < 8) {
            $errores['password'] = 'La contraseña debe tener al menos 8 caracteres.';
        } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
            $errores['password'] = 'La contraseña debe combinar letras y números.';
        }
        if ($password !== $confirmar) {
            $errores['password_confirm'] = 'Las contraseñas no coinciden.';
        }
        if (empty($_POST['terminos'])) {
            $errores['terminos'] = 'Tenés que aceptar los términos para crear la cuenta.';
        }

        if (empty($errores)) {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $datos['email']]);
            if ($stmt->fetch()) {
                $errores['email'] = 'Ya existe una cuenta registrada con ese correo.';
            }
        }

        if (empty($errores)) {
            try {
                $stmtRol = $pdo->prepare("SELECT id FROM roles WHERE nombre = 'usuario' LIMIT 1");
                $stmtRol->execute();
                $rolId = (int) $stmtRol->fetchColumn();

                $hash = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $pdo->prepare(
                    "INSERT INTO usuarios (nombre, email, password, telefono, rol_id, activo)
                     VALUES (:nombre, :email, :password, :telefono, :rol_id, 1)"
                );
                $stmt->execute([
                    ':nombre'   => $datos['nombre'],
                    ':email'    => $datos['email'],
                    ':password' => $hash,
                    ':telefono' => $datos['telefono'] !== '' ? $datos['telefono'] : null,
                    ':rol_id'   => $rolId ?: 2,
                ]);

                flash('success', 'Tu cuenta se creó correctamente. Ya podés iniciar sesión.');
                redirigir('login.php');
            } catch (PDOException $e) {
                error_log('[TechFix] Error al registrar usuario: ' . $e->getMessage());
                $mensajeGeneral = 'No pudimos crear la cuenta. Intentá nuevamente en unos minutos.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrap">
    <aside class="auth-aside">
        <span class="eyebrow" style="background:rgba(0,168,204,.14);border-color:rgba(0,168,204,.35);color:#7fe3f4"><?= icono('escudo') ?> Cuenta TechFix</span>
        <h2>Tu equipo y tus reparaciones, en un solo lugar.</h2>
        <p>Creá tu cuenta para consultar el historial, seguir el estado de cada reparación y guardar tus datos para la próxima visita.</p>
        <ul class="auth-benefits">
            <li><?= icono('check-circle') ?><span>Seguimiento de todas tus reparaciones.</span></li>
            <li><?= icono('check-circle') ?><span>Historial de pedidos de la tienda.</span></li>
            <li><?= icono('check-circle') ?><span>Datos completos para futuras solicitudes.</span></li>
            <li><?= icono('check-circle') ?><span>Contraseña protegida con hash bcrypt.</span></li>
        </ul>
    </aside>

    <div class="auth-main">
        <div class="auth-card">
            <h1>Crear cuenta</h1>
            <p class="auth-card__lead">Completá tus datos. Podés registrarte también después de pedir un presupuesto.</p>

            <?php if ($mensajeGeneral !== ''): ?>
                <div class="alert alert--error"><?= icono('info') ?><div><?= e($mensajeGeneral) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($errores)): ?>
                <div class="alert alert--error"><?= icono('info') ?><div><strong>Revisá los campos</strong>Los datos que completaste se conservaron.</div></div>
            <?php endif; ?>

            <form method="post" action="<?= e(url('registro.php')) ?>" novalidate>
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="nombre">Nombre y apellido <span class="req">*</span></label>
                    <input class="form-control<?= clase_error($errores, 'nombre') ?>" type="text" id="nombre" name="nombre"
                           value="<?= e($datos['nombre']) ?>" maxlength="100" autocomplete="name" required>
                    <?= error_campo($errores, 'nombre') ?>
                </div>

                <div class="form-group">
                    <label for="email">Correo electrónico <span class="req">*</span></label>
                    <input class="form-control<?= clase_error($errores, 'email') ?>" type="email" id="email" name="email"
                           value="<?= e($datos['email']) ?>" maxlength="150" autocomplete="email" required>
                    <?= error_campo($errores, 'email') ?>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono <span class="muted">(opcional)</span></label>
                    <input class="form-control<?= clase_error($errores, 'telefono') ?>" type="tel" id="telefono" name="telefono"
                           value="<?= e($datos['telefono']) ?>" maxlength="30" autocomplete="tel">
                    <?= error_campo($errores, 'telefono') ?>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña <span class="req">*</span></label>
                    <div class="input-icon" style="position:relative">
                        <input class="form-control<?= clase_error($errores, 'password') ?>" type="password" id="password" name="password"
                               minlength="8" autocomplete="new-password" required>
                        <button type="button" data-toggle-password="password" aria-label="Mostrar contraseña"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:var(--muted-2);padding:6px"><?= icono('ojo') ?></button>
                    </div>
                    <span class="form-help">Mínimo 8 caracteres, con al menos una letra y un número.</span>
                    <?= error_campo($errores, 'password') ?>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Repetir contraseña <span class="req">*</span></label>
                    <input class="form-control<?= clase_error($errores, 'password_confirm') ?>" type="password" id="password_confirm" name="password_confirm"
                           minlength="8" autocomplete="new-password" required>
                    <?= error_campo($errores, 'password_confirm') ?>
                </div>

                <label class="form-check" style="margin-top:6px">
                    <input type="checkbox" name="terminos" value="1" <?= !empty($_POST['terminos']) ? 'checked' : '' ?>>
                    <span>Acepto los <a href="<?= e(url('sobre-el-taller.php')) ?>#terminos">términos y condiciones</a> y la política de privacidad.</span>
                </label>
                <?= error_campo($errores, 'terminos') ?>

                <button class="btn btn--primary btn--block btn--lg" type="submit" style="margin-top:18px">Crear mi cuenta</button>
            </form>

            <p class="auth-card__foot">¿Ya tenés cuenta? <a href="<?= e(url('login.php')) ?>">Iniciar sesión</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
