<?php
/**
 * login.php · Autenticación con password_verify y regeneración de sesión (TP8).
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/conexion.php';

if (usuario_actual()) {
    redirigir(es_admin() ? 'admin_panel.php' : 'dashboard.php');
}

$titulo_pagina = 'Iniciar sesión';
$descripcion_pagina = 'Ingresá a tu cuenta TechFix para seguir tus reparaciones y pedidos.';
$pagina_activa = 'login';

$email = '';
$mensajeGeneral = '';
$errorCodigo = limpiar($_GET['error'] ?? '');
$mensajeError = mensaje_error_login($errorCodigo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificar_csrf()) {
        $mensajeGeneral = 'La sesión expiró o el formulario no es válido. Recargá la página e intentá nuevamente.';
    } else {
        $email = limpiar($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if (!email_valido($email) || $password === '') {
            $mensajeGeneral = 'El correo o la contraseña no son correctos.';
        } else {
            $stmt = $pdo->prepare(
                "SELECT u.id, u.nombre, u.email, u.password, u.activo, r.nombre AS rol
                 FROM usuarios u
                 INNER JOIN roles r ON r.id = u.rol_id
                 WHERE u.email = :email LIMIT 1"
            );
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch();

            if ($usuario && (int) $usuario['activo'] === 1 && password_verify($password, $usuario['password'])) {
                if (password_needs_rehash($usuario['password'], PASSWORD_BCRYPT)) {
                    $stmtUpdate = $pdo->prepare("UPDATE usuarios SET password = :hash WHERE id = :id");
                    $stmtUpdate->execute([
                        ':hash' => password_hash($password, PASSWORD_BCRYPT),
                        ':id'   => $usuario['id'],
                    ]);
                }

                session_regenerate_id(true);
                $_SESSION['usuario_id'] = (int) $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['login_en'] = time();

                flash('success', '¡Hola de nuevo, ' . explode(' ', $usuario['nombre'])[0] . '!');
                redirigir($usuario['rol'] === 'admin' ? 'admin_panel.php' : 'dashboard.php');
            }

            usleep(250000);
            $mensajeGeneral = 'El correo o la contraseña no son correctos.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrap">
    <aside class="auth-aside">
        <span class="eyebrow" style="background:rgba(0,168,204,.14);border-color:rgba(0,168,204,.35);color:#7fe3f4"><?= icono('candado') ?> Acceso seguro</span>
        <h2>Volvé a tu panel y seguí todo desde ahí.</h2>
        <p>Consultá el estado de tus reparaciones, revisá tus pedidos y actualizá tus datos de contacto.</p>
        <ul class="auth-benefits">
            <li><?= icono('check-circle') ?><span>Sesión protegida con cookies HttpOnly.</span></li>
            <li><?= icono('check-circle') ?><span>Contraseñas verificadas con bcrypt.</span></li>
            <li><?= icono('check-circle') ?><span>Regeneración de sesión al iniciar.</span></li>
            <li><?= icono('check-circle') ?><span>Acceso diferenciado para administradores.</span></li>
        </ul>
        <div style="margin-top:36px;padding-top:22px;border-top:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.55);font-size:.8rem">
            ¿Todavía no tenés cuenta?<br>
            <a href="<?= e(url('registro.php')) ?>" style="color:#7fe3f4;font-weight:700">Crear una cuenta gratis</a>
        </div>
    </aside>

    <div class="auth-main">
        <div class="auth-card">
            <h1>Iniciar sesión</h1>
            <p class="auth-card__lead">Ingresá con el correo y la contraseña que usaste al registrarte.</p>

            <?php if ($mensajeError !== ''): ?>
                <div class="alert alert--warning"><?= icono('info') ?><div><?= e($mensajeError) ?></div></div>
            <?php endif; ?>
            <?php if ($mensajeGeneral !== ''): ?>
                <div class="alert alert--error"><?= icono('info') ?><div><strong>No pudimos iniciar sesión</strong><?= e($mensajeGeneral) ?></div></div>
            <?php endif; ?>

            <form method="post" action="<?= e(url('login.php')) ?>" novalidate>
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <div class="input-icon">
                        <?= icono('mail') ?>
                        <input class="form-control" type="email" id="email" name="email" value="<?= e($email) ?>"
                               placeholder="nombre@correo.com" maxlength="150" autocomplete="email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div style="position:relative">
                        <input class="form-control" type="password" id="password" name="password"
                               placeholder="Tu contraseña" autocomplete="current-password" required>
                        <button type="button" data-toggle-password="password" aria-label="Mostrar contraseña"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:var(--muted-2);padding:6px"><?= icono('ojo') ?></button>
                    </div>
                </div>

                <button class="btn btn--primary btn--block btn--lg" type="submit" style="margin-top:8px">Ingresar <?= icono('flecha') ?></button>
            </form>

            <div class="demo-box">
                <strong>Cuentas de demostración</strong><br>
                Admin: <code>admin@techfix.com.ar</code> · <code>Admin1234!</code><br>
                Cliente: <code>cliente@demo.com</code> · <code>Cliente1234!</code>
            </div>

            <p class="auth-card__foot">¿No tenés cuenta? <a href="<?= e(url('registro.php')) ?>">Crear una cuenta</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
