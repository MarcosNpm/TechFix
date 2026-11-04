<?php
/**
 * contacto.php · Formulario POST de contacto con sanitización y persistencia.
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/componentes.php';

$titulo_pagina = 'Contacto';
$descripcion_pagina = 'Escribinos, pedí un presupuesto o coordiná una visita al taller TechFix.';
$pagina_activa = 'contacto';

$datos = ['nombre' => '', 'email' => '', 'telefono' => '', 'asunto' => '', 'mensaje' => ''];
$errores = [];
$mensajeGeneral = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificar_csrf()) {
        $mensajeGeneral = 'La sesión expiró o el formulario no es válido. Recargá la página e intentá nuevamente.';
    } else {
        $datos = [
            'nombre'   => limpiar($_POST['nombre'] ?? ''),
            'email'    => limpiar($_POST['email'] ?? ''),
            'telefono' => limpiar($_POST['telefono'] ?? ''),
            'asunto'   => limpiar($_POST['asunto'] ?? ''),
            'mensaje'  => limpiar_multilinea($_POST['mensaje'] ?? ''),
        ];

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
        if ($datos['asunto'] === '' || strlen($datos['asunto']) > 120) {
            $errores['asunto'] = 'Indicá un asunto de hasta 120 caracteres.';
        }
        if (strlen($datos['mensaje']) < 10 || strlen($datos['mensaje']) > 1500) {
            $errores['mensaje'] = 'El mensaje debe tener entre 10 y 1500 caracteres.';
        }

        if (empty($errores)) {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO consultas (nombre, email, telefono, asunto, mensaje)
                     VALUES (:nombre, :email, :telefono, :asunto, :mensaje)"
                );
                $stmt->execute([
                    ':nombre'   => $datos['nombre'],
                    ':email'    => $datos['email'],
                    ':telefono' => $datos['telefono'] !== '' ? $datos['telefono'] : null,
                    ':asunto'   => $datos['asunto'],
                    ':mensaje'  => $datos['mensaje'],
                ]);
                flash('success', '¡Gracias por escribirnos! Te respondemos dentro de las próximas 24 horas hábiles.');
                redirigir('contacto.php');
            } catch (PDOException $e) {
                error_log('[TechFix] Error al guardar consulta: ' . $e->getMessage());
                $mensajeGeneral = 'No pudimos enviar el mensaje. Intentá nuevamente en unos minutos.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= e(url('index.php')) ?>">Inicio</a> <?= icono('flecha') ?>
            <span>Contacto</span>
        </nav>
        <span class="eyebrow"><?= icono('mail') ?> Estamos para ayudarte</span>
        <h1>Hablemos de tu equipo</h1>
        <p>Contanos qué necesitás y te respondemos con un presupuesto o coordinamos una visita al taller.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="detail-grid">
            <div>
                <?php if ($mensajeGeneral !== ''): ?>
                    <div class="alert alert--error">
                        <?= icono('info') ?>
                        <div><strong>No pudimos enviar el mensaje</strong><?= e($mensajeGeneral) ?></div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($errores)): ?>
                    <div class="alert alert--error">
                        <?= icono('info') ?>
                        <div><strong>Revisá los campos marcados</strong>Tus datos se conservaron para que puedas corregirlos.</div>
                    </div>
                <?php endif; ?>

                <form class="form-card" method="post" action="<?= e(url('contacto.php')) ?>" novalidate>
                    <?= csrf_field() ?>
                    <div class="form-section-title"><?= icono('mail') ?> Envianos un mensaje</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre y apellido <span class="req">*</span></label>
                            <input class="form-control<?= clase_error($errores, 'nombre') ?>" type="text" id="nombre" name="nombre"
                                   value="<?= e($datos['nombre']) ?>" placeholder="Tu nombre completo" maxlength="100" autocomplete="name" required>
                            <?= error_campo($errores, 'nombre') ?>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo electrónico <span class="req">*</span></label>
                            <input class="form-control<?= clase_error($errores, 'email') ?>" type="email" id="email" name="email"
                                   value="<?= e($datos['email']) ?>" placeholder="nombre@correo.com" maxlength="150" autocomplete="email" required>
                            <?= error_campo($errores, 'email') ?>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono <span class="muted">(opcional)</span></label>
                            <input class="form-control<?= clase_error($errores, 'telefono') ?>" type="tel" id="telefono" name="telefono"
                                   value="<?= e($datos['telefono']) ?>" placeholder="+54 11 5555-0101" maxlength="30" autocomplete="tel">
                            <?= error_campo($errores, 'telefono') ?>
                        </div>
                        <div class="form-group">
                            <label for="asunto">Asunto <span class="req">*</span></label>
                            <input class="form-control<?= clase_error($errores, 'asunto') ?>" type="text" id="asunto" name="asunto"
                                   value="<?= e($datos['asunto']) ?>" placeholder="Presupuesto, garantía, consulta…" maxlength="120" required>
                            <?= error_campo($errores, 'asunto') ?>
                        </div>
                        <div class="form-group form-group--full">
                            <label for="mensaje">Mensaje <span class="req">*</span></label>
                            <textarea class="form-control<?= clase_error($errores, 'mensaje') ?>" id="mensaje" name="mensaje" maxlength="1500"
                                      placeholder="Contanos qué equipo tenés y qué necesitás…" required><?= e($datos['mensaje']) ?></textarea>
                            <?= error_campo($errores, 'mensaje') ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="btn btn--primary btn--lg" type="submit">Enviar mensaje <?= icono('flecha') ?></button>
                        <span class="form-actions__note"><?= icono('candado') ?> No compartimos tus datos con terceros.</span>
                    </div>
                </form>
            </div>

            <aside>
                <div class="card" style="padding:28px;margin-bottom:20px">
                    <h2 style="font-size:1.2rem">Datos del taller</h2>
                    <ul class="footer-contact" style="margin-top:18px">
                        <li style="color:var(--text)"><?= icono('pin') ?><span><strong>Dirección</strong><br><?= e(SHOP_ADDRESS) ?></span></li>
                        <li style="color:var(--text)"><?= icono('telefono') ?><span><strong>Teléfono</strong><br><a href="tel:<?= e(preg_replace('/\s+/', '', SUPPORT_PHONE)) ?>"><?= e(SUPPORT_PHONE) ?></a></span></li>
                        <li style="color:var(--text)"><?= icono('mail') ?><span><strong>Correo</strong><br><a href="mailto:<?= e(SUPPORT_EMAIL) ?>"><?= e(SUPPORT_EMAIL) ?></a></span></li>
                        <li style="color:var(--text)"><?= icono('reloj') ?><span><strong>Horarios</strong><br>Lun a Vie 9–19 h<br>Sábado 9–13 h</span></li>
                    </ul>
                    <a class="btn btn--outline btn--block" href="https://wa.me/<?= e(WHATSAPP) ?>" target="_blank" rel="noopener" style="margin-top:20px">
                        <?= icono('whatsapp') ?> Escribir por WhatsApp
                    </a>
                </div>

                <div class="card" style="overflow:hidden;border:0;box-shadow:var(--shadow-sm)">
                    <div style="position:relative;height:240px;background:linear-gradient(135deg,#E8F1F4,#DCE9EE);overflow:hidden">
                        <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(26,26,46,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(26,26,46,.07) 1px,transparent 1px);background-size:34px 34px"></div>
                        <div style="position:absolute;left:18%;top:20%;width:64%;height:12px;background:#fff;border-radius:999px;transform:rotate(-14deg);box-shadow:0 0 0 1px rgba(26,26,46,.05)"></div>
                        <div style="position:absolute;left:8%;top:52%;width:80%;height:16px;background:#fff;border-radius:999px;transform:rotate(9deg);box-shadow:0 0 0 1px rgba(26,26,46,.05)"></div>
                        <div style="position:absolute;left:44%;top:64%;width:14px;height:14px;background:#00A8CC;border:3px solid #fff;border-radius:50%;box-shadow:0 6px 14px rgba(26,26,46,.25)"></div>
                        <div style="position:absolute;left:calc(44% + 10px);top:calc(64% - 42px);background:#1A1A2E;color:#fff;padding:7px 11px;border-radius:8px;font-size:.7rem;font-weight:700;white-space:nowrap">TechFix · <?= e(SHOP_ADDRESS) ?></div>
                    </div>
                    <div style="padding:18px 20px">
                        <p style="margin:0;font-size:.8rem;color:var(--muted)"><?= icono('pin') ?> A dos cuadras de la estación de subte. Hay estacionamiento en la cuadra.</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
