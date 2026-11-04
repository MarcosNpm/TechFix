<?php
/**
 * clase5/index.php · TP5
 * Formulario de contacto por POST + buscador por GET.
 * Sanitización estricta, validación en servidor, mensajes de feedback
 * y persistencia de los campos ante errores.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/funciones.php';

$titulo = 'Formularios POST y GET';
$activo = 'inicio';

$errores = [];
$exito = false;
$mensajeGuardado = null;

$asuntosPermitidos = [
    'Consulta por una reparación',
    'Presupuesto de repuestos',
    'Estado de mi equipo',
    'Garantía',
    'Otro',
];

$datos = [
    'nombre'   => '',
    'email'    => '',
    'telefono' => '',
    'asunto'   => $asuntosPermitidos[0],
    'mensaje'  => '',
    'acepto'   => false,
];

/* ------------------------------------------------------------
   POST · Procesamiento del formulario de contacto
   ------------------------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificar_csrf()) {
        $errores['general'] = 'El formulario expiró. Recargá la página e intentá de nuevo.';
    }

    $datos['nombre']   = limpiar($_POST['nombre'] ?? '');
    $datos['email']    = limpiar($_POST['email'] ?? '');
    $datos['telefono'] = limpiar($_POST['telefono'] ?? '');
    $datos['asunto']   = limpiar($_POST['asunto'] ?? '');
    $datos['mensaje']  = limpiar_multilinea($_POST['mensaje'] ?? '');
    $datos['acepto']   = isset($_POST['acepto']);

    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'Ingresá tu nombre.';
    } elseif (strlen($datos['nombre']) < 3 || strlen($datos['nombre']) > 100) {
        $errores['nombre'] = 'El nombre debe tener entre 3 y 100 caracteres.';
    }

    if ($datos['email'] === '') {
        $errores['email'] = 'Ingresá tu correo electrónico.';
    } elseif (!email_valido($datos['email'])) {
        $errores['email'] = 'El formato del correo no es válido (ej.: nombre@dominio.com).';
    }

    if ($datos['telefono'] !== '' && !preg_match('/^[0-9+()\s-]{6,30}$/', $datos['telefono'])) {
        $errores['telefono'] = 'El teléfono solo admite números, espacios, paréntesis, guiones y el signo +.';
    }

    if (!in_array($datos['asunto'], $asuntosPermitidos, true)) {
        $errores['asunto'] = 'Seleccioná un asunto válido.';
    }

    if ($datos['mensaje'] === '') {
        $errores['mensaje'] = 'Contanos qué necesitás.';
    } elseif (strlen($datos['mensaje']) < 10) {
        $errores['mensaje'] = 'El mensaje debe tener al menos 10 caracteres.';
    } elseif (strlen($datos['mensaje']) > 1000) {
        $errores['mensaje'] = 'El mensaje no puede superar los 1000 caracteres.';
    }

    if (!$datos['acepto']) {
        $errores['acepto'] = 'Tenés que aceptar la política de privacidad.';
    }

    if (empty($errores)) {
        $registro = [
            'id'       => bin2hex(random_bytes(6)),
            'nombre'   => $datos['nombre'],
            'email'    => $datos['email'],
            'telefono' => $datos['telefono'],
            'asunto'   => $datos['asunto'],
            'mensaje'  => $datos['mensaje'],
            'fecha'    => date('c'),
        ];

        if (guardar_mensaje($registro)) {
            $_SESSION['clase5_exito'] = '¡Gracias, ' . $datos['nombre'] . '! Tu mensaje quedó registrado. Te respondemos dentro de las próximas 24 horas hábiles.';
            header('Location: index.php?ok=1');
            exit;
        }

        $errores['general'] = 'No pudimos guardar el mensaje. Revisá los permisos de la carpeta clase5/data.';
    }
}

/* Mensaje de éxito luego del redirect (patrón PRG). */
if (isset($_SESSION['clase5_exito'])) {
    $exito = true;
    $mensajeGuardado = (string) $_SESSION['clase5_exito'];
    unset($_SESSION['clase5_exito']);
}

/* ------------------------------------------------------------
   GET · Búsqueda en el catálogo de ejemplo
   ------------------------------------------------------------ */
$busqueda = limpiar($_GET['q'] ?? '');
if (strlen($busqueda) > 60) {
    $busqueda = substr($busqueda, 0, 60);
}
$resultados = buscar_catalogo($busqueda);
$totalCatalogo = count(catalogo());
$modoBusqueda = $busqueda !== '';

$totalMensajes = count(leer_mensajes());

require_once __DIR__ . '/includes/header.php';
?>

<section class="c5-hero">
    <span class="c5-pill">TP N.º 5 · Unidad 4</span>
    <h1>Formularios seguros: POST y GET</h1>
    <p>
        Dos interacciones reales: un formulario de contacto que envía datos por <strong>POST</strong>
        y un buscador que consulta por <strong>GET</strong>. Todo se sanitiza y valida en el servidor
        antes de mostrarse o guardarse.
    </p>
    <div class="c5-hero__stats">
        <span><strong><?= $totalCatalogo ?></strong> servicios en el catálogo</span>
        <span><strong><?= $totalMensajes ?></strong> <?= $totalMensajes === 1 ? 'mensaje persistido' : 'mensajes persistidos' ?></span>
        <span><strong>0</strong> scripts ejecutados</span>
    </div>
</section>

<?php if ($exito && $mensajeGuardado): ?>
    <div class="c5-alert c5-alert--ok" role="status">
        <span class="c5-alert__icon">✓</span>
        <div>
            <strong>Envío exitoso</strong>
            <p><?= e($mensajeGuardado) ?></p>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($errores['general'])): ?>
    <div class="c5-alert c5-alert--error" role="alert">
        <span class="c5-alert__icon">!</span>
        <div><strong>No se pudo completar el envío</strong><p><?= e($errores['general']) ?></p></div>
    </div>
<?php endif; ?>

<div class="c5-grid">
    <!-- ============================ POST ============================ -->
    <section class="c5-card" id="contacto" aria-labelledby="titulo-contacto">
        <header class="c5-card__head">
            <span class="c5-tag c5-tag--post">POST</span>
            <h2 id="titulo-contacto">Contacto</h2>
            <p>Los datos se guardan en <code>clase5/data/mensajes.json</code>.</p>
        </header>

        <form method="post" action="index.php#contacto" class="c5-form" novalidate>
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

            <div class="c5-field">
                <label for="nombre">Nombre y apellido <span>*</span></label>
                <input type="text" id="nombre" name="nombre" maxlength="100"
                       value="<?= e($datos['nombre']) ?>" placeholder="Ej.: Ana Pérez"
                       class="<?= isset($errores['nombre']) ? 'is-invalid' : '' ?>"
                       aria-invalid="<?= isset($errores['nombre']) ? 'true' : 'false' ?>"
                       aria-describedby="error-nombre" required>
                <?php if (isset($errores['nombre'])): ?>
                    <small class="c5-error" id="error-nombre"><?= e($errores['nombre']) ?></small>
                <?php endif; ?>
            </div>

            <div class="c5-row">
                <div class="c5-field">
                    <label for="email">Correo electrónico <span>*</span></label>
                    <input type="email" id="email" name="email" maxlength="150"
                           value="<?= e($datos['email']) ?>" placeholder="ana@dominio.com"
                           class="<?= isset($errores['email']) ? 'is-invalid' : '' ?>"
                           aria-invalid="<?= isset($errores['email']) ? 'true' : 'false' ?>"
                           aria-describedby="error-email" required>
                    <?php if (isset($errores['email'])): ?>
                        <small class="c5-error" id="error-email"><?= e($errores['email']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="c5-field">
                    <label for="telefono">Teléfono <small>(opcional)</small></label>
                    <input type="tel" id="telefono" name="telefono" maxlength="30"
                           value="<?= e($datos['telefono']) ?>" placeholder="+54 11 5555-0101"
                           class="<?= isset($errores['telefono']) ? 'is-invalid' : '' ?>"
                           aria-describedby="error-telefono">
                    <?php if (isset($errores['telefono'])): ?>
                        <small class="c5-error" id="error-telefono"><?= e($errores['telefono']) ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="c5-field">
                <label for="asunto">Asunto <span>*</span></label>
                <select id="asunto" name="asunto" class="<?= isset($errores['asunto']) ? 'is-invalid' : '' ?>" required>
                    <?php foreach ($asuntosPermitidos as $asunto): ?>
                        <option value="<?= e($asunto) ?>" <?= $datos['asunto'] === $asunto ? 'selected' : '' ?>><?= e($asunto) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errores['asunto'])): ?>
                    <small class="c5-error"><?= e($errores['asunto']) ?></small>
                <?php endif; ?>
            </div>

            <div class="c5-field">
                <label for="mensaje">Mensaje <span>*</span></label>
                <textarea id="mensaje" name="mensaje" rows="5" maxlength="1000"
                          class="<?= isset($errores['mensaje']) ? 'is-invalid' : '' ?>"
                          aria-describedby="error-mensaje contador-mensaje"
                          placeholder="Contanos qué equipo tenés y qué problema presenta…" required><?= e($datos['mensaje']) ?></textarea>
                <div class="c5-field__meta">
                    <?php if (isset($errores['mensaje'])): ?>
                        <small class="c5-error" id="error-mensaje"><?= e($errores['mensaje']) ?></small>
                    <?php else: ?>
                        <small>Mínimo 10 caracteres.</small>
                    <?php endif; ?>
                    <small id="contador-mensaje"><span data-contador>0</span>/1000</small>
                </div>
            </div>

            <label class="c5-check <?= isset($errores['acepto']) ? 'is-invalid' : '' ?>">
                <input type="checkbox" name="acepto" value="1" <?= $datos['acepto'] ? 'checked' : '' ?>>
                <span>Acepto la política de privacidad y el uso de mis datos para responder la consulta.</span>
            </label>
            <?php if (isset($errores['acepto'])): ?>
                <small class="c5-error"><?= e($errores['acepto']) ?></small>
            <?php endif; ?>

            <button class="c5-btn" type="submit">Enviar consulta</button>
            <p class="c5-hint">Los campos se conservan si hay errores de validación.</p>
        </form>
    </section>

    <!-- ============================ GET ============================= -->
    <section class="c5-card" id="buscador" aria-labelledby="titulo-buscador">
        <header class="c5-card__head">
            <span class="c5-tag c5-tag--get">GET</span>
            <h2 id="titulo-buscador">Buscar en el catálogo</h2>
            <p>El término viaja en la URL: <code>?q=...</code>. Se sanitiza antes de usarlo.</p>
        </header>

        <form method="get" action="index.php#buscador" class="c5-search" role="search">
            <label class="sr-only" for="q">Buscar servicio</label>
            <input type="search" id="q" name="q" maxlength="60" value="<?= e($busqueda) ?>"
                   placeholder="Ej.: batería, SSD, celular…" autocomplete="off">
            <button class="c5-btn c5-btn--ghost" type="submit">Buscar</button>
            <?php if ($modoBusqueda): ?>
                <a class="c5-clear" href="index.php#buscador">Limpiar</a>
            <?php endif; ?>
        </form>

        <p class="c5-result-info" aria-live="polite">
            <?php if ($modoBusqueda): ?>
                <?= count($resultados) ?> resultado(s) para
                <strong>«<?= e($busqueda) ?>»</strong>
            <?php else: ?>
                Mostrando los <?= $totalCatalogo ?> servicios del catálogo.
            <?php endif; ?>
        </p>

        <ul class="c5-results">
            <?php if ($resultados): ?>
                <?php foreach ($resultados as $item): ?>
                    <li class="c5-result">
                        <div class="c5-result__top">
                            <span class="c5-result__cat"><?= e($item['categoria']) ?></span>
                            <strong>$ <?= number_format((float) $item['precio'], 0, ',', '.') ?></strong>
                        </div>
                        <h3><?= resaltar((string) $item['nombre'], $busqueda) ?></h3>
                        <p><?= resaltar((string) $item['descripcion'], $busqueda) ?></p>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="c5-empty">
                    <strong>Sin resultados</strong>
                    <p>Probá con otra palabra, por ejemplo «pantalla» o «SSD».</p>
                </li>
            <?php endif; ?>
        </ul>

        <?php if ($modoBusqueda): ?>
            <div class="c5-note">
                <strong>Prueba de sanitización:</strong> si buscás
                <code>&lt;script&gt;alert(1)&lt;/script&gt;</code>, el texto se muestra escapado y
                nunca se ejecuta en el navegador.
            </div>
        <?php endif; ?>
    </section>
</div>

<section class="c5-explain">
    <h2>Cómo se procesa cada método</h2>
    <div class="c5-explain__grid">
        <article>
            <span class="c5-tag c5-tag--post">POST</span>
            <h3>Contacto</h3>
            <ol>
                <li><code>trim()</code> + <code>strip_tags()</code> sobre cada campo.</li>
                <li><code>filter_var()</code> para el correo y expresiones regulares para el teléfono.</li>
                <li>Validación de requeridos, longitudes y asunto permitido.</li>
                <li>Guardado en JSON con <code>flock()</code> y redirección PRG.</li>
                <li>Al imprimir, todo pasa por <code>htmlspecialchars()</code>.</li>
            </ol>
        </article>
        <article>
            <span class="c5-tag c5-tag--get">GET</span>
            <h3>Buscador</h3>
            <ol>
                <li>El término llega por <code>$_GET['q']</code>.</li>
                <li>Se sanitiza y se limita a 60 caracteres.</li>
                <li>Se compara sin distinguir mayúsculas ni acentos.</li>
                <li>El término se imprime escapado dentro del input y del resumen.</li>
                <li>La búsqueda es compartible porque queda en la URL.</li>
            </ol>
        </article>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
