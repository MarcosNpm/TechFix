<?php
/**
 * solicitud.php · Formulario POST de solicitud de reparación.
 * Incluye sanitización, validación en servidor, CSRF y persistencia en MySQL.
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/componentes.php';

$titulo_pagina = 'Solicitar reparación';
$descripcion_pagina = 'Completá los datos de tu equipo y contanos la falla. Te respondemos con un diagnóstico y presupuesto.';
$pagina_activa = 'servicios';

$categorias = $pdo->query(
    "SELECT c.id, c.nombre, c.slug,
            GROUP_CONCAT(s.id ORDER BY s.nombre SEPARATOR ',') AS ids
     FROM categorias_servicio c
     INNER JOIN servicios s ON s.categoria_id = c.id AND s.activo = 1
     GROUP BY c.id, c.nombre, c.slug
     ORDER BY c.id"
)->fetchAll();

$servicios = $pdo->query(
    "SELECT s.id, s.nombre, s.categoria_id, s.precio_base, s.tiempo_estimado, s.garantia_dias
     FROM servicios s
     WHERE s.activo = 1
     ORDER BY s.categoria_id, s.nombre"
)->fetchAll();

$serviciosPorCategoria = [];
foreach ($servicios as $item) {
    $serviciosPorCategoria[(int) $item['categoria_id']][] = $item;
}

$servicioSeleccionado = filter_input(INPUT_GET, 'servicio', FILTER_VALIDATE_INT) ?: 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicioSeleccionado = filter_input(INPUT_POST, 'servicio_id', FILTER_VALIDATE_INT) ?: 0;
}

$servicioActual = null;
foreach ($servicios as $item) {
    if ((int) $item['id'] === (int) $servicioSeleccionado) {
        $servicioActual = $item;
        break;
    }
}

$usuario = usuario_actual();
$datos = [
    'servicio_id' => (string) $servicioSeleccionado,
    'nombre'      => $usuario['nombre'] ?? '',
    'email'       => $usuario['email'] ?? '',
    'telefono'    => '',
    'marca'       => '',
    'modelo'      => '',
    'problema'    => '',
];
$errores = [];
$mensajeGeneral = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificar_csrf()) {
        $mensajeGeneral = 'La sesión expiró o el formulario no es válido. Recargá la página e intentá nuevamente.';
    } else {
        $datos = [
            'servicio_id' => (string) (filter_input(INPUT_POST, 'servicio_id', FILTER_VALIDATE_INT) ?: ''),
            'nombre'      => limpiar($_POST['nombre'] ?? ''),
            'email'       => limpiar($_POST['email'] ?? ''),
            'telefono'    => limpiar($_POST['telefono'] ?? ''),
            'marca'       => limpiar($_POST['marca'] ?? ''),
            'modelo'      => limpiar($_POST['modelo'] ?? ''),
            'problema'    => limpiar_multilinea($_POST['problema'] ?? ''),
        ];

        if ($datos['servicio_id'] === '' || !ctype_digit($datos['servicio_id'])) {
            $errores['servicio_id'] = 'Elegí el servicio que necesitás.';
        } else {
            $servicioActual = null;
            foreach ($servicios as $item) {
                if ((int) $item['id'] === (int) $datos['servicio_id']) {
                    $servicioActual = $item;
                    break;
                }
            }
            if (!$servicioActual) {
                $errores['servicio_id'] = 'El servicio seleccionado no está disponible.';
            }
        }

        if ($datos['nombre'] === '' || strlen($datos['nombre']) < 3) {
            $errores['nombre'] = 'Ingresá tu nombre completo (mínimo 3 caracteres).';
        } elseif (strlen($datos['nombre']) > 100) {
            $errores['nombre'] = 'El nombre no puede superar los 100 caracteres.';
        }

        if ($datos['email'] === '' || !email_valido($datos['email'])) {
            $errores['email'] = 'Ingresá un correo electrónico válido.';
        }

        $soloDigitos = preg_replace('/\D+/', '', $datos['telefono']);
        if ($datos['telefono'] === '' || strlen($soloDigitos) < 7) {
            $errores['telefono'] = 'Ingresá un teléfono de contacto válido.';
        } elseif (strlen($soloDigitos) > 15) {
            $errores['telefono'] = 'El teléfono ingresado es demasiado largo.';
        }

        if ($datos['marca'] === '' || strlen($datos['marca']) > 60) {
            $errores['marca'] = 'Indicá la marca del equipo.';
        }

        if ($datos['modelo'] === '' || strlen($datos['modelo']) > 100) {
            $errores['modelo'] = 'Indicá el modelo del equipo.';
        }

        if (strlen($datos['problema']) < 10) {
            $errores['problema'] = 'Contanos un poco más sobre la falla (mínimo 10 caracteres).';
        } elseif (strlen($datos['problema']) > 1200) {
            $errores['problema'] = 'La descripción no puede superar los 1200 caracteres.';
        }

        if (empty($_POST['terminos'])) {
            $errores['terminos'] = 'Tenés que aceptar los términos para continuar.';
        }

        if (empty($errores) && $mensajeGeneral === '') {
            try {
                $codigo = generar_codigo('TF');
                $stmtEstado = $pdo->prepare("SELECT id FROM estados_reparacion WHERE slug = 'recibido' LIMIT 1");
                $stmtEstado->execute();
                $estadoId = (int) $stmtEstado->fetchColumn();

                $stmt = $pdo->prepare(
                    "INSERT INTO reparaciones
                        (codigo, usuario_id, servicio_id, estado_id, cliente_nombre, cliente_email,
                         cliente_telefono, equipo_marca, equipo_modelo, problema, created_at)
                     VALUES
                        (:codigo, :usuario_id, :servicio_id, :estado_id, :nombre, :email,
                         :telefono, :marca, :modelo, :problema, NOW())"
                );
                $stmt->execute([
                    ':codigo'      => $codigo,
                    ':usuario_id'  => $usuario['id'] ?? null,
                    ':servicio_id' => (int) $datos['servicio_id'],
                    ':estado_id'   => $estadoId,
                    ':nombre'      => $datos['nombre'],
                    ':email'       => $datos['email'],
                    ':telefono'    => $datos['telefono'],
                    ':marca'       => $datos['marca'],
                    ':modelo'      => $datos['modelo'],
                    ':problema'    => $datos['problema'],
                ]);

                flash('success', 'Recibimos tu solicitud. Guardá el código para seguir la reparación.');
                redirigir('confirmacion.php?codigo=' . urlencode($codigo));
            } catch (PDOException $e) {
                error_log('[TechFix] Error al crear reparación: ' . $e->getMessage());
                $mensajeGeneral = 'No pudimos registrar la solicitud. Intentá nuevamente en unos minutos.';
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
            <a href="<?= e(url('servicios.php')) ?>">Servicios</a> <?= icono('flecha') ?>
            <span>Solicitud de reparación</span>
        </nav>
        <span class="eyebrow"><?= icono('herramienta') ?> Paso 1 de 2</span>
        <h1>Contanos qué le pasa a tu equipo</h1>
        <p>Completá el formulario y te contactamos con un diagnóstico y un presupuesto sin cargo. No se realiza ningún trabajo sin tu aprobación.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="detail-grid">
            <div>
                <?php if ($mensajeGeneral !== ''): ?>
                    <div class="alert alert--error">
                        <?= icono('info') ?>
                        <div><strong>No pudimos enviar el formulario</strong><?= e($mensajeGeneral) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errores)): ?>
                    <div class="alert alert--error">
                        <?= icono('info') ?>
                        <div><strong>Revisá los campos marcados</strong>Los datos que completaste se conservaron para que puedas corregirlos.</div>
                    </div>
                <?php endif; ?>

                <form class="form-card" method="post" action="<?= e(url('solicitud.php')) ?>" novalidate>
                    <?= csrf_field() ?>

                    <div class="form-section-title"><?= icono('config') ?> Servicio y equipo</div>
                    <div class="form-grid">
                        <div class="form-group form-group--full">
                            <label for="servicio_id">Servicio que necesitás <span class="req">*</span></label>
                            <select class="form-select<?= clase_error($errores, 'servicio_id') ?>" id="servicio_id" name="servicio_id" required>
                                <option value="">Elegí un servicio…</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <?php $idCat = (int) $categoria['id']; ?>
                                    <?php if (!empty($serviciosPorCategoria[$idCat])): ?>
                                        <optgroup label="<?= e($categoria['nombre']) ?>">
                                            <?php foreach ($serviciosPorCategoria[$idCat] as $item): ?>
                                                <option value="<?= (int) $item['id'] ?>" <?= (string) $item['id'] === $datos['servicio_id'] ? 'selected' : '' ?>>
                                                    <?= e($item['nombre']) ?> — desde <?= e(precio($item['precio_base'])) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <?= error_campo($errores, 'servicio_id') ?>
                        </div>

                        <div class="form-group">
                            <label for="marca">Marca del equipo <span class="req">*</span></label>
                            <input class="form-control<?= clase_error($errores, 'marca') ?>" type="text" id="marca" name="marca"
                                   value="<?= e($datos['marca']) ?>" placeholder="Samsung, Lenovo, Apple…" maxlength="60" required>
                            <?= error_campo($errores, 'marca') ?>
                        </div>

                        <div class="form-group">
                            <label for="modelo">Modelo <span class="req">*</span></label>
                            <input class="form-control<?= clase_error($errores, 'modelo') ?>" type="text" id="modelo" name="modelo"
                                   value="<?= e($datos['modelo']) ?>" placeholder="Galaxy A54, IdeaPad 3…" maxlength="100" required>
                            <?= error_campo($errores, 'modelo') ?>
                        </div>

                        <div class="form-group form-group--full">
                            <label for="problema">Descripción de la falla <span class="req">*</span></label>
                            <textarea class="form-control<?= clase_error($errores, 'problema') ?>" id="problema" name="problema"
                                      maxlength="1200" placeholder="Contanos cuándo empezó, qué pasa exactamente y si el equipo enciende…" required><?= e($datos['problema']) ?></textarea>
                            <span class="form-help">Máximo 1200 caracteres. Cuanto más detalle, mejor podemos orientarte.</span>
                            <?= error_campo($errores, 'problema') ?>
                        </div>
                    </div>

                    <div class="form-section-title"><?= icono('usuario') ?> Tus datos de contacto</div>
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
                            <label for="telefono">Teléfono <span class="req">*</span></label>
                            <input class="form-control<?= clase_error($errores, 'telefono') ?>" type="tel" id="telefono" name="telefono"
                                   value="<?= e($datos['telefono']) ?>" placeholder="+54 11 5555-0101" maxlength="30" autocomplete="tel" required>
                            <?= error_campo($errores, 'telefono') ?>
                        </div>

                        <div class="form-group">
                            <label>Modalidad de contacto</label>
                            <div class="form-control" style="display:flex;align-items:center;gap:9px;background:var(--bg);color:var(--muted)">
                                <?= icono('whatsapp') ?> Te escribimos por WhatsApp o correo
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:24px">
                        <label class="form-check">
                            <input type="checkbox" name="terminos" value="1" <?= !empty($_POST['terminos']) ? 'checked' : '' ?>>
                            <span>Acepto los <a href="<?= e(url('sobre-el-taller.php')) ?>#terminos">términos y condiciones</a> y el tratamiento de mis datos para gestionar la reparación.</span>
                        </label>
                        <?= error_campo($errores, 'terminos') ?>
                    </div>

                    <div class="form-actions">
                        <button class="btn btn--primary btn--lg" type="submit">Enviar solicitud <?= icono('flecha') ?></button>
                        <a class="btn btn--ghost" href="<?= e(url('servicios.php')) ?>">Volver a servicios</a>
                        <span class="form-actions__note"><?= icono('candado') ?> Tus datos se envían por POST y se almacenan de forma segura.</span>
                    </div>
                </form>
            </div>

            <aside class="buy-box">
                <span class="badge badge--cyan">Resumen de tu solicitud</span>
                <h2 style="font-size:1.25rem;margin-top:14px"><?= $servicioActual ? e($servicioActual['nombre']) : 'Elegí un servicio' ?></h2>
                <?php if ($servicioActual): ?>
                    <div class="buy-box__price">
                        <strong><?= e(precio($servicioActual['precio_base'])) ?></strong>
                        <small>desde</small>
                    </div>
                    <ul class="buy-box__meta">
                        <li><?= icono('reloj') ?> <?= e($servicioActual['tiempo_estimado']) ?></li>
                        <li><?= icono('escudo') ?> <?= (int) $servicioActual['garantia_dias'] ?> días de garantía</li>
                        <li><?= icono('check-circle') ?> Diagnóstico sin cargo</li>
                    </ul>
                <?php else: ?>
                    <p class="muted" style="font-size:.86rem">Seleccioná un servicio del formulario para ver el precio orientativo y el tiempo estimado.</p>
                <?php endif; ?>

                <div class="tracking-note" style="margin-top:18px">
                    <?= icono('info') ?>
                    <span>El presupuesto final se confirma después del diagnóstico. Si no lo aprobás, no abonás nada.</span>
                </div>

                <div style="margin-top:22px;padding-top:18px;border-top:1px solid var(--border)">
                    <p style="font-size:.78rem;color:var(--muted);margin-bottom:10px">¿Preferís hablar con una persona?</p>
                    <a class="btn btn--ghost btn--block btn--sm" href="https://wa.me/<?= e(WHATSAPP) ?>" target="_blank" rel="noopener">
                        <?= icono('whatsapp') ?> Escribir por WhatsApp
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
